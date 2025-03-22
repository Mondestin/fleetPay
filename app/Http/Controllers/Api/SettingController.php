<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get the commission
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommission(Request $request)
    {
        try {
            $setting = Setting::where('name', 'commission')
                 ->where('user_id', $request->user()->id)
                 ->first();
            return response()->json(['commission' => $setting->value]);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Commission n\'a pas été trouvée'], 404);
        }
    }

    /**
     * Update the commission
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCommission(Request $request)
    {
        $validated = $request->validate([
            'commission' => 'required|numeric|min:0'
        ]);
        try {   
                $setting = Setting::where('name', 'commission')
                 ->where('user_id', $request->user()->id)
                 ->first();
                $setting->value = $validated['commission'];
                $setting->save();
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Commission n\'a pas été mise à jour'], 404);
        }

        return response()->json($setting);
    }
} 