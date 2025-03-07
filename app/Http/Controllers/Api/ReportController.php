<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\PlatformEarning;
use App\Models\Driver;
use App\Helpers\NameCheck;
use App\Services\PlatformImporters\UberImporter;
use App\Services\PlatformImporters\BoltImporter;
use App\Services\PlatformImporters\HeetchImporter;

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
        ], 204);
    }

    public function importPlatformEarnings(Request $request)
    {
        $platform = strtolower($request->platform);
        $results = [];


        // Get the appropriate importer
        $importer = match($platform) {
            'uber' => new UberImporter(),
            'bolt' => new BoltImporter(),
            'heetch' => new HeetchImporter(),
            default => throw new \Exception("Unsupported platform: $platform")
        };

        // Filter and process data
        $driversData = collect($request->data)
            ->whereNotNull('firstName')
            ->values();
        logger($driversData);
        foreach ($driversData as $driverData) {
            try {
                $driver = $importer->importDriver($driverData);
                $earning = $importer->importEarnings($driver, $driverData);
                $results[] = $earning;
            } catch (\Exception $e) {
                logger("Error importing driver: " . $e->getMessage());
                continue;
            }
        }

        return response()->json([
            'message' => 'Platform earnings imported successfully',
            'data' => $results
        ], 201);
    }


}
