<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Get the authenticated user's company
     */
    public function show(Request $request){
        try {
            $company = Company::where('user_id', $request->user)->first();
           
            return response()->json($company);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
   
    /**
     * Update the authenticated user's company
     */
    public function update(Request $request)
    {
       logger($request->all());

        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $company = Company::where('user_id', $request->user)->first();
            $company->update($validated);
            return response()->json($company);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

 
} 