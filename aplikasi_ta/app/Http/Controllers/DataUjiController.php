<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataUjiController extends Controller
{
    public function index()
    {
        return view('admin.data_uji.index');
    }
}
