<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    use ApiTrait;
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
        $contactUs = ContactUs::create([
            'user_id' => $request->user_id,
            'message' => $request->message,
        ]);
        return $this->createdResponse((array) $contactUs, 'Contact Us created successfully');
    }

    
}
