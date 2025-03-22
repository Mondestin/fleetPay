<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getCommission()
    {
        try {
            $setting = Setting::where('name', 'commission')->first();
            return response()->json(['commission' => $setting->value]);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function postCommission(Request $request)
    {
        $validated = $request->validate([
            'commission' => 'required|numeric|min:0'
        ]);
        try {   
                $setting = Setting::where('name', 'commission')->first();
                $setting->value = $validated['commission'];
                $setting->save();
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json($setting);
    }
} 