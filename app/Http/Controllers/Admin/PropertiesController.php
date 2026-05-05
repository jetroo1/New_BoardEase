<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;

class PropertiesController extends Controller
{
    public function index()
    {
        $properties = Property::with('owner')->withCount('bookings')->latest()->paginate(20);
        return view('admin.properties.index', compact('properties'));
    }

    public function approve($id)
    {
        Property::findOrFail($id)->update(['is_approved' => true]);
        return back()->with('success', 'Property approved.');
    }

    public function reject($id)
    {
        Property::findOrFail($id)->update(['is_approved' => false]);
        return back()->with('success', 'Property rejected/hidden.');
    }

    public function destroy($id)
    {
        Property::findOrFail($id)->delete();
        return back()->with('success', 'Property deleted.');
    }
}