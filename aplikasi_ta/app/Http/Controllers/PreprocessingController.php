<?php

namespace App\Http\Controllers;

use App\Models\Preprocessing;
use Illuminate\Http\Request;

class PreprocessingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.preprocessing.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Preprocessing $preprocessing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Preprocessing $preprocessing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Preprocessing $preprocessing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Preprocessing $preprocessing)
    {
        //
    }
}
