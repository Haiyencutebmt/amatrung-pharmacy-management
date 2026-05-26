<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Article;

class CommentController extends Controller
{
    public function store(Request $request, $article_id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $article = Article::findOrFail($article_id);

        Comment::create([
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
            'is_approved' => true, // Hiển thị ngay không cần duyệt
        ]);

        return back()->with('status', 'Cảm ơn ý kiến phản hồi và đánh giá của bạn!');
    }
}
