<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getCommission()
    {
        $setting = Setting::where('name', 'commission')->first();
        return response()->json($setting);
    }


    public function postCommission(Request $request)
    {
        $validated = $request->validate([
            'commission' => 'required|numeric|min:0'
        ]);

        $setting = Setting::where('name', 'commission')->first();
        $setting->value = $validated['commission'];
        $setting->save();

        return response()->json($setting);
    }
} 