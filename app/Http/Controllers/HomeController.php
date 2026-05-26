<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\HerbDictionaryEntry;

class HomeController extends Controller
{
    public function index()
    {
        $latestArticles = Article::with('author')
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        // Get 4 random herbs for the homepage dictionary preview
        $randomHerbs = HerbDictionaryEntry::published()->inRandomOrder()->take(4)->get();

        return view('home', compact('latestArticles', 'randomHerbs'));
    }
}
