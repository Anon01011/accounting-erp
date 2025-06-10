<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('sales.settings.index');
    }

    public function update(Request $request)
    {
        // TODO: Implement settings update logic
    }
} 