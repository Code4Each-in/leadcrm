<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Product;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('applications.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $application = Application::create(
            $request->all()
        );

        return redirect()
            ->route('applications.create')
            ->with('success', 'Application saved successfully.');
    }
}
