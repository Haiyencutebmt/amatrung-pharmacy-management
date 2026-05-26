<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CommentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:comments.manage'),
        ];
    }

    public function index(Request $request)
    {
        $query = Comment::with(['article', 'user']);

        if ($request->has('status') && $request->status != '') {
            $query->where('is_approved', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $comments = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $comment->update(['is_approved' => $validated['is_approved']]);

        $statusStr = $validated['is_approved'] ? 'đã duyệt' : 'bỏ duyệt';
        return redirect()->back()->with('success', "Bình luận {$statusStr} thành công.");
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return redirect()->back()->with('success', 'Đã xóa bình luận.');
    }
}
