<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointConversion;

class PointConversionController extends Controller
{
    //

    public function index()
    {
        // Fetch all point conversions
        $conversions = PointConversion::all();
        return view('admin.pointConverisions.index', compact('conversions'));
    }

    public function create()
    {
        // Show form to create a new point conversion
        return view('admin.pointConverisions.create');
    }

   public function store(Request $request)
{
    // Validation
    $request->validate([
        'points' => 'required|numeric',
        'price' => 'required|numeric',
    ], [
        'points.required' => 'Points are required.',
        'price.required' => 'Price is required.',
        'points.numeric' => 'Points must be a number.',
        'price.numeric' => 'Price must be a number.',
    ]);

    // Save record
    PointConversion::create([
        'points' => $request->points,
        'price' => $request->price,
    ]);

    return redirect()->route('point-conversions.index')->with('success', 'Point conversion created successfully.');
}


    public function edit($id)
    {
        // Show form to edit an existing point conversion
        $conversion = PointConversion::findOrFail($id);
        return view('admin.pointConverisions.edit', compact('conversion'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'points' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        // Find the point conversion and update it
        $conversion = PointConversion::findOrFail($id);
        $conversion->update([
            'points' => $request->points,
            'price' => $request->price,
        ]);

        return redirect()->route('point-conversions.index')->with('success', 'Points conversion updated successfully.');
    }
}
