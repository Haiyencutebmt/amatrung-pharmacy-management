<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ArticleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:articles.manage'),
        ];
    }

    public function index(Request $request)
    {
        $query = Article::with('author')->withCount('comments');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = $query->orderByDesc('created_at')->paginate(20);
        $articleCategories = Article::CATEGORIES;

        return view('admin.articles.index', compact('articles', 'articleCategories'));
    }

    public function create()
    {
        $articleCategories = Article::CATEGORIES;

        return view('admin.articles.create', compact('articleCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'summary'        => 'nullable|string|max:200',
            'category'       => ['required', Rule::in(array_keys(Article::CATEGORIES))],
            'tags'           => 'nullable|string|max:1000',
            'content'        => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'is_published'   => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_published'] = $request->has('is_published');
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['tags'] = $this->parseTags($validated['tags'] ?? null);

        // Xử lý ảnh base64 trong content
        $validated['content'] = $this->processBase64Images($validated['content']);

        // Upload featured_image
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $path = $file->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        // Đảm bảo slug duy nhất
        $slug = Str::slug($validated['title']);
        $count = Article::where('slug', 'like', $slug . '%')->count();
        $validated['slug'] = $count ? "{$slug}-{$count}" : $slug;

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Đã thêm bài viết mới.');
    }

    public function edit(Article $article)
    {
        $articleCategories = Article::CATEGORIES;

        return view('admin.articles.edit', compact('article', 'articleCategories'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'summary'        => 'nullable|string|max:200',
            'category'       => ['required', Rule::in(array_keys(Article::CATEGORIES))],
            'tags'           => 'nullable|string|max:1000',
            'content'        => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'is_published'   => 'boolean',
        ]);

        $is_published = $request->has('is_published');
        $newTitle = $validated['title'];
        
        // Xử lý ảnh base64 trong content
        $processedContent = $this->processBase64Images($validated['content']);
        
        $data = [
            'title'        => $newTitle,
            'summary'      => $validated['summary'] ?? null,
            'category'     => $validated['category'],
            'tags'         => $this->parseTags($validated['tags'] ?? null),
            'content'      => $processedContent,
            'is_published' => $is_published,
            'published_at' => $is_published && !$article->published_at ? now() : $article->published_at,
        ];

        // Nếu đổi tiêu đề thì cập nhật slug
        if ($article->title !== $newTitle) {
            $slug = Str::slug($newTitle);
            $count = Article::where('slug', 'like', $slug . '%')->where('id', '!=', $article->id)->count();
            $data['slug'] = $count ? "{$slug}-{$count}" : $slug;
        }

        // Xử lý xóa ảnh cũ nếu người dùng yêu cầu xóa
        if ($request->input('remove_featured_image') == '1') {
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $data['featured_image'] = null;
        }

        // Xử lý tải ảnh mới lên
        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $file = $request->file('featured_image');
            $path = $file->store('articles', 'public');
            $data['featured_image'] = $path;
        }
        
        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Đã cập nhật bài viết.');
    }

    private function parseTags(?string $tags): array
    {
        if (empty($tags)) {
            return [];
        }

        return collect(preg_split('/[,;\n]+/', $tags))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->unique()
            ->take(12)
            ->values()
            ->all();
    }

    /**
     * Tìm và lưu ảnh base64 thành file thực tế
     */
    private function processBase64Images($content)
    {
        if (empty($content)) {
            return $content;
        }

        // Tắt libxml errors để tránh warning khi parse HTML không chuẩn
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        // Load HTML với utf-8
        $dom->loadHtml('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getElementsByTagName('img');

        // Thư mục lưu ảnh
        $uploadDir = public_path('uploads/articles');
        if (!File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            // Nếu là base64
            if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                $base64Data = substr($src, strpos($src, ',') + 1);
                $type = strtolower($type[1]); // png, jpg, gif
                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $type = 'png';
                }

                $base64Data = base64_decode($base64Data);
                if ($base64Data !== false) {
                    $imageName = time() . '_' . Str::random(10) . '.' . $type;
                    $path = $uploadDir . '/' . $imageName;

                    File::put($path, $base64Data);

                    // Cập nhật lại src thành URL file
                    $img->removeAttribute('src');
                    $img->setAttribute('src', asset('uploads/articles/' . $imageName));
                }
            }
        }

        $html = $dom->saveHTML();
        // Dọn dẹp thẻ xml tạm
        $html = str_replace('<?xml encoding="UTF-8">', '', $html);

        return $html;
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:articles,id',
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một bài viết để xóa.',
            'ids.min' => 'Vui lòng chọn ít nhất một bài viết để xóa.',
        ]);

        $ids = collect($validated['ids'])->map(fn ($id) => (int) $id)->unique()->values();
        $deletedCount = 0;

        Article::whereIn('id', $ids)->get()->each(function (Article $article) use (&$deletedCount) {
            $article->delete();
            $deletedCount++;
        });

        return redirect()->route('admin.articles.index')->with('success', "Đã xóa {$deletedCount} bài viết.");
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Đã xóa bài viết.');
    }
}
