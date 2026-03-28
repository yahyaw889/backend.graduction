<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // TODO: Implement contact form logic (e.g., send email, store in database)

        return response()->json([
            'message' => 'Contact form submitted successfully',
        ], 201);
    }
}
