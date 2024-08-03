<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function adminHome()
    {
        // Admin-specific logic
        return view('pages.admin.home');
    }

    public function subAdminHome()
    {
        // Sub-admin-specific logic
        return view('pages.subadmin.home');
    }
}
