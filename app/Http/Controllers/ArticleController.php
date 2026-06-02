<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articleCategories = Article::CATEGORIES;
        $selectedCategory = array_key_exists($request->query('category'), $articleCategories)
            ? $request->query('category')
            : null;
        $search = trim((string) $request->query('search', ''));

        $articles = Article::with('author')
            ->withCount('likedByUsers')
            ->published();

        if (auth()->check()) {
            $articles->with([
                'likedByUsers' => fn ($query) => $query->where('user_id', auth()->id()),
            ]);
        }

        if ($selectedCategory) {
            $articles->where('category', $selectedCategory);
        }

        if ($search !== '') {
            $matchingCategories = collect($articleCategories)
                ->filter(fn ($label) => str_contains(mb_strtolower($label, 'UTF-8'), mb_strtolower($search, 'UTF-8')))
                ->keys()
                ->all();

            $articles->where(function ($query) use ($search, $matchingCategories) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");

                if (!empty($matchingCategories)) {
                    $query->orWhereIn('category', $matchingCategories);
                }
            });
        }

        $articles = $articles
            ->latest('published_at')
            ->paginate(10);

        // Fetch top 3 featured articles
        $featuredArticles = Article::with('author')
            ->withCount(['likedByUsers', 'approvedComments'])
            ->published()
            ->get()
            ->sortByDesc(function ($article) {
                $wordCount = str_word_count(strip_tags($article->content ?? ''));
                $readingTime = ceil($wordCount / 200);
                return $readingTime + $article->liked_by_users_count + $article->approved_comments_count;
            })
            ->take(3);

        // Fetch top 3 latest articles
        $latestArticles = Article::with('author')
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('articles.index', compact('articles', 'featuredArticles', 'latestArticles', 'articleCategories', 'selectedCategory', 'search'));
    }

    public function show($slug)
    {
        $article = Article::with(['author', 'approvedComments.user'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $recentArticles = Article::with('author')
            ->published()
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('articles.show', compact('article', 'recentArticles'));
    }

    public function toggleLike($article_id)
    {
        $article = Article::findOrFail($article_id);
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Toggle relationship
        if ($article->likedByUsers()->where('user_id', $user->id)->exists()) {
            $article->likedByUsers()->detach($user->id);
            $liked = false;
        } else {
            $article->likedByUsers()->attach($user->id);
            $liked = true;
        }

        $likesCount = $article->likedByUsers()->count();

        if (request()->expectsJson()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $likesCount,
            ]);
        }

        $statusMessage = $liked ? 'Đã thêm bài viết vào danh sách yêu thích.' : 'Đã bỏ bài viết khỏi danh sách yêu thích.';
        return back()->with('status', $statusMessage);
    }
}
