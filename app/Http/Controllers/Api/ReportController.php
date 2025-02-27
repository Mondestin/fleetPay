<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\PlatformEarning;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $weekStartDate = $request->weekStartDate;
        $weekEndDate = date('Y-m-d', strtotime($weekStartDate . ' + 6 days'));

        $platforms = ['uber', 'bolt', 'heetch'];
        $results = [];
        foreach ($platforms as $platform) {
         
            $platformEarning = PlatformEarning::whereBetween('week_start_date', [$weekStartDate, $weekEndDate])
                                                ->where('platform', $platform)->first();
            
            if (!$platformEarning) {
                $results[] = [
                    'platform' => $platform,
                    'uploaded' => false,
                    'weekStartDate' => $weekStartDate
                ];
                
            } else {
                $results[] = [
                    'platform' => $platform,
                    'uploaded' => true,
                    'weekStartDate' => $weekStartDate
                ];
                
            }
        }
        
        return response()->json([
            'message' => 'Platform earnings import status',
            'data' => $results
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request )
    {
        $weekStartDate = $request->weekStartDate;
        $platform = $request->platform;
        
        $weekEndDate = date('Y-m-d', strtotime($weekStartDate . ' + 6 days'));
        logger($weekEndDate);
        try {
            PlatformEarning::whereBetween('week_start_date', [$weekStartDate, $weekEndDate])
                                            ->where('platform', $platform)->delete();
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'message' => 'Platform earnings not found'
            ], 404);
        }
      
        return response()->json([
            'message' => 'Platform earnings deleted'
        ], 200);
    }

    public function importPlatformEarnings(Request $request)
    {
        
        $results = [];
        foreach ($request->data as $earningData) {

            logger($earningData);
            // Find or create driver by first name and last name
            $driver = \App\Models\Driver::firstOrCreate(
                [
                    'first_name' => $earningData['firstName'],
                    'last_name' => $earningData['lastName'],
                    'email' => $earningData['email'] ?? null, 
                    'phone_number' => $earningData['phoneNumber'] ?? null
                ]
            );
            //get commission from setting
            $commission = Setting::where('name', 'commission')->first()->value;

            // Create platform earning record
            //check if platform earning already exists
            $platformEarning = PlatformEarning::where('driver_id', $driver->id)->where('week_start_date', $earningData['weekDate'])->first();
            if (!$platformEarning) {
                $platformEarning = PlatformEarning::create([
                'driver_id' => $driver->id,
                'created_by' => 'user1',
                'platform' => $earningData['platform'],
                'week_start_date' => $earningData['weekDate'],
                'earnings' => $earningData['totalRevenue'],
                'commission_amount' => $commission,
                'validated' => 1,
                'status' => 'pending',
            ]);
            
            $results[] = $platformEarning;
          }
        }
        return response()->json([
            'message' => 'Platform earnings imported successfully',
            'data' => $results
        ], 201);
    }


}
