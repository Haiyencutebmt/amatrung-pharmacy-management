<?php

namespace App\Http\Controllers;

use App\Models\HerbDictionaryEntry;
use Illuminate\Http\Request;

class HerbDictionaryController extends Controller
{
    public function index(Request $request)
    {
        $query = HerbDictionaryEntry::published()
            ->with('images')
            ->withCount('favorites');

        if (auth()->check() && $request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('scientific_name', 'like', "%{$keyword}%")
                    ->orWhere('other_names', 'like', "%{$keyword}%")
                    ->orWhere('effects', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('letter')) {
            $letter = trim($request->letter);
            $query->where('name', 'like', "{$letter}%");
        }

        if (auth()->check()) {
            $query->with(['favorites' => fn ($q) => $q->where('user_id', auth()->id())]);
        }

        $entries = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('herb_dictionary.index', [
            'entries' => $entries,
            'searchEnabled' => auth()->check(),
        ]);
    }

    public function show(HerbDictionaryEntry $entry)
    {
        abort_unless($entry->status === 'published', 404);

        $entry->load(['images', 'favorites' => fn ($q) => $q->where('user_id', auth()->id())])
            ->loadCount('favorites');

        return view('herb_dictionary.show', compact('entry'));
    }

    public function toggleFavorite(HerbDictionaryEntry $entry)
    {
        abort_unless($entry->status === 'published', 404);

        $favorite = $entry->favorites()->where('user_id', auth()->id())->first();

        if ($favorite) {
            $favorite->delete();
            return back()->with('status', 'Đã bỏ khỏi danh sách yêu thích.');
        }

        $entry->favorites()->create(['user_id' => auth()->id()]);

        return back()->with('status', 'Đã thêm vào danh sách yêu thích.');
    }

    public function favorites()
    {
        $entries = auth()->user()
            ->herbDictionaryFavorites()
            ->published()
            ->with('images')
            ->withCount('favorites')
            ->orderByPivot('created_at', 'desc')
            ->paginate(12);

        return view('herb_dictionary.favorites', compact('entries'));
    }
}
