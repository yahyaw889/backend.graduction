<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactUsController extends Controller
{
    use ApiTrait;
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        $contactUs[] = ContactUs::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);
        return $this->createdResponse( $contactUs, 'Contact Us created successfully');
    }

    
}
