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
        $percentage = $totalVideos > 0 ? round(($completedCount / $totalVideos) * 100) : 0;

        return [
            'total_videos' => $totalVideos,
            'completed_videos' => $completedCount,
            'percentage' => (int) $percentage,
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
            'completed_courses' => $completedCourses,
            'courses_in_progress' => $coursesInProgress,
            'not_started_courses' => $totalCourses - $completedCourses - $coursesInProgress,
            'total_videos' => $totalVideos,
            'completed_videos' => $completedVideos,
            'overall_percentage' => (int) $overallPercentage,
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
        $totalCompletions = VideoCompletion::count();

        foreach ($users as $user) {
            $progress = $this->getOverallProgress($user);
            if ($progress['completed_videos'] > 0) {
                $studentsWithProgress++;
            }
            if ($progress['overall_percentage'] === 100) {
                $studentsCompleted++;
            }
        }

        return [
            'total_students' => $totalStudents,
            'students_with_progress' => $studentsWithProgress,
            'students_completed' => $studentsCompleted,
            'total_completions' => $totalCompletions,
            'average_completion_rate' => $totalStudents > 0
                ? round(($studentsCompleted / $totalStudents) * 100)
                : 0,
        ];
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
