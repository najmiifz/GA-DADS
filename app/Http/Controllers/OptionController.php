<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;

class OptionController extends Controller
{
    public function index(Request $request, $category)
    {
        $options = Option::where('category', $category)->orderBy('name')->pluck('name');
        return response()->json($options);
    }

    public function store(Request $request, $category)
    {
        $request->validate(['name' => 'required|string']);
        $name = $request->name;
        $option = Option::firstOrCreate(['category' => $category, 'name' => $name]);
        return response()->json(['name' => $option->name, 'id' => $option->id]);
    }
}
