<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('author')
            ->published()
            ->latest('published_at')
            ->paginate(12);

        return view('articles.index', compact('articles'));
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

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }
}
