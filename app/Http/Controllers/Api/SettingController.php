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

                //check if the setting exists
                if (!$setting) {
                    //create the commission setting
                    $setting = Setting::create([
                        'name' => 'commission',
                        'value' => $validated['commission'],
                        'user_id' => $request->user()->id
                    ]);
                    return response()->json(['message' => 'Commission créée avec succès'], 201);
                }

                //update the commission setting
                $setting->value = $validated['commission'];
                $setting->save();

        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour de la commission'], 500);
        }

        return response()->json(['message' => 'Commission mise à jour avec succès']);
    }
} 