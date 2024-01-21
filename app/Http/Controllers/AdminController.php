<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin'); // Assumes your Blade view file is named admin.blade.php
    }
}
