<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\MedicinalHerb;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'patients'        => Patient::count(),
            'medical_records' => MedicalRecord::count(),
            'herbs'           => MedicinalHerb::count(),
            'herbs_low'       => MedicinalHerb::where('stock_quantity', '<=', 10)->where('stock_quantity', '>', 0)->count(),
            'herbs_out'       => MedicinalHerb::where('stock_quantity', '<=', 0)->count(),
            'articles'        => Article::count(),
            'comments_pending'=> Comment::pending()->count(),
            'users'           => User::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
