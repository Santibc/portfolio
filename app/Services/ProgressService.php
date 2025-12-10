<?php

namespace App\Services;

use App\Models\User;
use App\Models\Video;
use App\Models\Course;
use App\Models\VideoCompletion;
use Illuminate\Support\Collection;

class ProgressService
{
    /**
     * Marcar un video como completado
     */
    public function markVideoAsCompleted(User $user, Video $video): VideoCompletion
    {
        return VideoCompletion::firstOrCreate(
            [
                'user_id' => $user->id,
                'video_id' => $video->id,
            ],
            [
                'completed_at' => now(),
            ]
        );
    }

    /**
     * Verificar si un video está completado por un usuario
     */
    public function isVideoCompleted(User $user, Video $video): bool
    {
        return VideoCompletion::where('user_id', $user->id)
            ->where('video_id', $video->id)
            ->exists();
    }

    /**
     * Obtener el progreso de un usuario en un curso
     */
    public function getCourseProgress(User $user, Course $course): array
    {
        $totalVideos = $course->videos()->count();
        $completedVideoIds = VideoCompletion::where('user_id', $user->id)
            ->whereIn('video_id', $course->videos()->pluck('id'))
            ->pluck('video_id')
            ->toArray();

        $completedCount = count($completedVideoIds);
        $percentage = $totalVideos > 0 ? (int) round(($completedCount / $totalVideos) * 100) : 0;

        return [
            'total_videos' => $totalVideos,
            'completed_videos' => $completedCount,
            'percentage' => $percentage,
            'is_completed' => $percentage === 100,
            'completed_video_ids' => $completedVideoIds,
        ];
    }

    /**
     * Obtener el progreso general de un usuario
     */
    public function getOverallProgress(User $user): array
    {
        $publishedCourses = Course::published()->get();
        $totalCourses = $publishedCourses->count();

        $completedCourses = 0;
        $coursesInProgress = 0;
        $totalVideos = 0;
        $completedVideos = 0;

        foreach ($publishedCourses as $course) {
            $progress = $this->getCourseProgress($user, $course);
            $totalVideos += $progress['total_videos'];
            $completedVideos += $progress['completed_videos'];

            if ($progress['is_completed']) {
                $completedCourses++;
            } elseif ($progress['completed_videos'] > 0) {
                $coursesInProgress++;
            }
        }

        $overallPercentage = $totalVideos > 0 ? round(($completedVideos / $totalVideos) * 100) : 0;

        return [
            'total_courses' => $totalCourses,
            'courses_completed' => $completedCourses,
            'courses_in_progress' => $coursesInProgress,
            'not_started_courses' => $totalCourses - $completedCourses - $coursesInProgress,
            'total_videos' => $totalVideos,
            'completed_videos' => $completedVideos,
            'percentage' => (int) $overallPercentage,
        ];
    }

    /**
     * Obtener cursos completados por un usuario
     */
    public function getCompletedCourses(User $user): Collection
    {
        return Course::published()->get()->filter(function ($course) use ($user) {
            $progress = $this->getCourseProgress($user, $course);
            return $progress['is_completed'];
        });
    }

    /**
     * Obtener cursos en progreso de un usuario
     */
    public function getCoursesInProgress(User $user): Collection
    {
        return Course::published()->get()->filter(function ($course) use ($user) {
            $progress = $this->getCourseProgress($user, $course);
            return $progress['completed_videos'] > 0 && !$progress['is_completed'];
        });
    }

    /**
     * Obtener progreso por categoría para un usuario
     */
    public function getProgressByCategory(User $user): array
    {
        $categories = \App\Models\Category::active()
            ->with(['courses' => function ($query) {
                $query->published();
            }])
            ->ordered()
            ->get();

        $result = [];

        foreach ($categories as $category) {
            $totalVideos = 0;
            $completedVideos = 0;

            foreach ($category->courses as $course) {
                $progress = $this->getCourseProgress($user, $course);
                $totalVideos += $progress['total_videos'];
                $completedVideos += $progress['completed_videos'];
            }

            $percentage = $totalVideos > 0 ? (int) round(($completedVideos / $totalVideos) * 100) : 0;

            $result[] = [
                'name' => $category->name,
                'percentage' => $percentage,
                'completed_videos' => $completedVideos,
                'total_videos' => $totalVideos,
            ];
        }

        return $result;
    }

    /**
     * Obtener el siguiente video a ver en un curso
     */
public function getNextVideoToWatch(User $user): ?Video
{
    // Obtener IDs de videos completados
    $completed = VideoCompletion::where('user_id', $user->id)
        ->pluck('video_id')
        ->toArray();

    // Buscar el primer video NO completado de cursos publicados
    return Video::whereHas('course', function ($q) {
            $q->published();
        })
        ->whereNotIn('id', $completed)
        ->orderBy('course_id')
        ->orderBy('order')
        ->first();
}


    /**
     * Obtener el siguiente curso disponible para un usuario
     */
    public function getNextAvailableCourse(User $user): ?Course
    {
        $publishedCourses = Course::published()
            ->orderBy('category_id')
            ->orderBy('order')
            ->get();

        foreach ($publishedCourses as $course) {
            if ($user->canAccessCourse($course)) {
                $progress = $this->getCourseProgress($user, $course);
                if (!$progress['is_completed']) {
                    return $course;
                }
            }
        }

        return null;
    }

    /**
     * Obtener actividad reciente de un usuario
     */
    public function getRecentActivity(User $user, int $limit = 10): Collection
    {
        return VideoCompletion::with(['video.course'])
            ->where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de progreso para admin
     */
    public function getAdminStats(): array
    {
        $users = User::role('Estudiante')->get();
        $totalStudents = $users->count();

        $studentsWithProgress = 0;
        $studentsCompleted = 0;
        $coursesCompletedTotal = 0;
        $totalCompletions = VideoCompletion::count();

        foreach ($users as $user) {
            $progress = $this->getOverallProgress($user);
            if ($progress['completed_videos'] > 0) {
                $studentsWithProgress++;
            }
            if ($progress['percentage'] === 100) {
                $studentsCompleted++;
            }
            $coursesCompletedTotal += $progress['courses_completed'];
        }

        // Actividad de hoy
        $today = now()->startOfDay();
        $todayCompletions = VideoCompletion::where('completed_at', '>=', $today)->count();
        $todayNotes = \App\Models\Note::where('created_at', '>=', $today)->count();
        $activeStudentsToday = VideoCompletion::where('completed_at', '>=', $today)
            ->distinct('user_id')
            ->count('user_id');

        // Actividad reciente
        $recentActivity = VideoCompletion::with(['user', 'video.course'])
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Calcular tasa de completación general
        $totalVideos = Video::count();
        $totalPossibleCompletions = $totalVideos * $totalStudents;
        $overallCompletionRate = $totalPossibleCompletions > 0
            ? round(($totalCompletions / $totalPossibleCompletions) * 100)
            : 0;

        // Promedio de videos por estudiante
        $avgVideosPerStudent = $totalStudents > 0
            ? round($totalCompletions / $totalStudents, 1)
            : 0;

        // Día más activo de la semana
        $mostActiveDay = $this->getMostActiveDay();

        // Tiempo promedio de completación (aproximado)
        $avgCompletionTime = $this->getAverageCompletionTime();

        return [
            'total_students' => $totalStudents,
            'students_with_progress' => $studentsWithProgress,
            'students_completed' => $studentsCompleted,
            'courses_completed' => $coursesCompletedTotal,
            'total_completions' => $totalCompletions,
            'average_completion_rate' => $totalStudents > 0
                ? round(($studentsCompleted / $totalStudents) * 100)
                : 0,
            'today_completions' => $todayCompletions,
            'today_notes' => $todayNotes,
            'active_students_today' => $activeStudentsToday,
            'recent_activity' => $recentActivity,
            'overall_completion_rate' => $overallCompletionRate,
            'avg_videos_per_student' => $avgVideosPerStudent,
            'most_active_day' => $mostActiveDay,
            'avg_completion_time' => $avgCompletionTime,
        ];
    }

    /**
     * Obtener el día más activo de la semana
     */
    protected function getMostActiveDay(): string
    {
        $result = VideoCompletion::selectRaw('DAYOFWEEK(completed_at) as day_of_week, COUNT(*) as count')
            ->groupBy('day_of_week')
            ->orderBy('count', 'desc')
            ->first();

        if (!$result) {
            return 'N/A';
        }

        $days = ['', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        return $days[$result->day_of_week] ?? 'N/A';
    }

    /**
     * Obtener tiempo promedio de completación de cursos
     */
    protected function getAverageCompletionTime(): string
    {
        // Esta es una implementación simplificada
        // En producción, se calcularía basándose en las fechas de completación
        $totalDuration = Video::sum('duration_seconds');
        $totalCourses = Course::published()->count();

        if ($totalCourses === 0) {
            return 'N/A';
        }

        $avgDuration = $totalDuration / $totalCourses;
        $hours = floor($avgDuration / 3600);
        $minutes = floor(($avgDuration % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes} min";
    }

    /**
     * Obtener progreso de todos los estudiantes para un curso específico
     */
    public function getCourseProgressForAllStudents(Course $course): Collection
    {
        return User::role('Estudiante')->get()->map(function ($user) use ($course) {
            $progress = $this->getCourseProgress($user, $course);
            return [
                'user' => $user,
                'progress' => $progress,
            ];
        });
    }
}
