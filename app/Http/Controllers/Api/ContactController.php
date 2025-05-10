<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller   
{
    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'company' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $data = $request->all();
        logger($data);
        try {
            $adminEmail = env('MAIL_TO_ADMIN');
            Mail::to($adminEmail)->send(new ContactMail($data));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Email n\'a pas été envoyé'], 500);
        }

        return response()->json(['message' => 'Email envoyé avec succès']);
    }
}
