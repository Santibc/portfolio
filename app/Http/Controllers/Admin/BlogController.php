<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with('category')->orderByDesc('created_at');
        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where('title', 'like', "%{$q}%");
        }
        if ($request->filled('status')) {
            $query->where('is_published', $request->input('status') === 'published');
        }
        $posts = $query->paginate(15)->withQueryString();
        $categories = BlogCategory::orderBy('order')->get();

        return view('admin.blog.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = BlogCategory::active()->orderBy('order')->get();
        return view('admin.blog.form', ['post' => new BlogPost(), 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $this->storeCover($request->file('cover_image'), 'covers');
        }
        if ($request->hasFile('og_image')) {
            $data['og_image_path'] = $this->storeCover($request->file('og_image'), 'og');
        }
        if ($request->hasFile('twitter_image')) {
            $data['twitter_image_path'] = $this->storeCover($request->file('twitter_image'), 'twitter');
        }
        BlogPost::create($data);
        return redirect()->route('admin.blog.posts.index')->with('success', 'Artículo creado.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::active()->orderBy('order')->get();
        return view('admin.blog.form', compact('post', 'categories'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $data = $this->validated($request, $post->id);

        foreach (['cover_image' => 'cover_image_path', 'og_image' => 'og_image_path', 'twitter_image' => 'twitter_image_path'] as $inputField => $dbField) {
            if ($request->hasFile($inputField)) {
                if ($post->$dbField && file_exists(public_path($post->$dbField))) {
                    @unlink(public_path($post->$dbField));
                }
                $data[$dbField] = $this->storeCover($request->file($inputField),
                    $inputField === 'cover_image' ? 'covers' : ($inputField === 'og_image' ? 'og' : 'twitter'));
            }
        }

        $post->update($data);
        return redirect()->route('admin.blog.posts.index')->with('success', 'Artículo actualizado.');
    }

    public function destroy(BlogPost $post)
    {
        if ($post->cover_image_path && file_exists(public_path($post->cover_image_path))) {
            @unlink(public_path($post->cover_image_path));
        }
        $post->delete();
        return redirect()->route('admin.blog.posts.index')->with('success', 'Artículo eliminado.');
    }

    // ---------- Categories ----------

    public function categoriesIndex()
    {
        $categories = BlogCategory::withCount('posts')->orderBy('order')->get();
        return view('admin.blog.categories', compact('categories'));
    }

    public function categoriesStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:180|unique:blog_categories,slug',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? (BlogCategory::max('order') + 1);
        if (!empty($data['slug'])) {
            $data['slug'] = BlogCategory::uniqueSlug($data['slug']);
        }
        BlogCategory::create($data);
        return redirect()->back()->with('success', 'Categoría creada.');
    }

    public function categoriesUpdate(Request $request, BlogCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('blog_categories', 'slug')->ignore($category->id)],
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', $category->is_active);
        if (!empty($data['slug'])) {
            $data['slug'] = BlogCategory::uniqueSlug($data['slug'], $category->id);
        }
        $category->update($data);
        return redirect()->back()->with('success', 'Categoría actualizada.');
    }

    public function categoriesDestroy(BlogCategory $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Categoría eliminada.');
    }

    // Quick create via AJAX from the post form
    public function categoriesQuickCreate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
        ]);
        $cat = BlogCategory::create([
            'name' => $data['name'],
            'is_active' => true,
            'order' => (BlogCategory::max('order') ?? 0) + 1,
        ]);
        return response()->json([
            'id' => $cat->id,
            'name' => $cat->name,
            'slug' => $cat->slug,
        ]);
    }

    // Image upload endpoint for the Quill editor (returns JSON with URL)
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);
        $folder = public_path('images/blog/uploads');
        if (!is_dir($folder)) {
            @mkdir($folder, 0755, true);
        }
        $file = $request->file('image');
        $filename = 'img_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($folder, $filename);

        return response()->json([
            'url' => asset('images/blog/uploads/' . $filename),
        ]);
    }

    // ---------- Helpers ----------

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'category_id' => 'nullable|exists:blog_categories,id',
            'title' => 'required|string|max:200',
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('blog_posts', 'slug')->ignore($ignoreId)],
            'author_name' => 'nullable|string|max:120',
            'excerpt' => 'nullable|string|max:500',
            'content_html' => 'required|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:500',
            'focus_keyword' => 'nullable|string|max:100',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => ['nullable', Rule::in(['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'])],
            'og_title' => 'nullable|string|max:150',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'og_type' => 'nullable|string|max:50',
            'twitter_card' => ['nullable', Rule::in(['summary', 'summary_large_image'])],
            'twitter_title' => 'nullable|string|max:150',
            'twitter_description' => 'nullable|string',
            'twitter_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'schema_type' => 'nullable|string|max:50',
            'schema_data' => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ];
        $data = $request->validate($rules);
        $data['is_published'] = $request->boolean('is_published', false);
        if (!empty($data['slug'])) {
            $data['slug'] = BlogPost::uniqueSlug($data['slug'], $ignoreId);
        }
        // Defaults
        $data['robots'] = $data['robots'] ?? 'index,follow';
        $data['og_type'] = $data['og_type'] ?? 'article';
        $data['twitter_card'] = $data['twitter_card'] ?? 'summary_large_image';
        $data['schema_type'] = $data['schema_type'] ?? 'BlogPosting';

        // Parse schema_data JSON string into array (or null on invalid/empty)
        if ($request->filled('schema_data')) {
            $decoded = json_decode($request->input('schema_data'), true);
            $data['schema_data'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        } else {
            $data['schema_data'] = null;
        }

        // Do not persist virtual file inputs
        unset($data['cover_image'], $data['og_image'], $data['twitter_image']);
        return $data;
    }

    private function storeCover($file, string $subfolder = 'covers'): string
    {
        $folder = public_path('images/blog/' . $subfolder);
        if (!is_dir($folder)) {
            @mkdir($folder, 0755, true);
        }
        $filename = $subfolder . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($folder, $filename);
        return 'images/blog/' . $subfolder . '/' . $filename;
    }
}
