<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devotional;
use App\Models\MemoryVerse;
use App\Models\User;

class HomeController extends Controller
{
    public function home()
    {
        $users = User::all();
        $totalUsers = $users->count();
        $totalSubscribedUsers = $users->where('has_paid', true)->count();
        $totalDevotional = Devotional::count();
        $totalUnsubscribedUsers = $users->where('has_paid', false)->count();
        $totalMemoryVerse = MemoryVerse::count();
        return view('admin.index', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'totalSubscribedUsers' => $totalSubscribedUsers,
            'totalDevotional' => $totalDevotional,
            'totalUnsubscribedUsers' => $totalUnsubscribedUsers,
            'totalMemoryVerse'=> $totalMemoryVerse
        ]);
    }
}
