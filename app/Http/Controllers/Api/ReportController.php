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
     * Get platform earnings status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get the week start date from the request
        $weekStartDate = $request->weekStartDate;
        // Calculate the week end date
        $weekEndDate = date('Y-m-d', strtotime($weekStartDate . ' + 6 days'));

        // Get all platforms
        $platforms = ['uber', 'bolt', 'heetch'];
        $results = [];
        foreach ($platforms as $platform) {


            // Get platform earnings for the specified week and platform for the current user
            $platformEarning = PlatformEarning::whereBetween('week_start_date', [$weekStartDate, $weekEndDate])
                                                ->where('platform', $platform)
                                                ->where('created_by', $request->user()->id)
                                                ->first();
            // If there is no platform earnings for the specified week and platform for the current user, add it to the results
            if (!$platformEarning) {
                $results[] = [
                    'platform' => $platform,
                    'uploaded' => false,
                    'weekStartDate' => $weekStartDate
                ];
            // If there is platform earnings for the specified week and platform for the current user, add it to the results
            } else {
                $results[] = [
                    'platform' => $platform,
                    'uploaded' => true,
                    'weekStartDate' => $weekStartDate
                ];
                
            }
        }
        // Return the results
        return response()->json([
            'message' => 'Platform earnings import status',
            'data' => $results
        ], 200);
    }

    /**
     * Remove imported platform earnings
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request )
    {
        // Get the week start date from the request
        $weekStartDate = $request->weekStartDate;
        // Get the platform from the request
        $platform = $request->platform;
        // Calculate the week end date
        $weekEndDate = date('Y-m-d', strtotime($weekStartDate . ' + 6 days'));
       
        try {
            // Delete platform earnings for the specified week and platform for the current user
            PlatformEarning::whereBetween('week_start_date', [$weekStartDate, $weekEndDate])
                                            ->where('platform', $platform)
                                            ->where('created_by', $request->user()->id)
                                            ->delete();
        } catch (\Exception $e) {
            logger($e);
            return response()->json([
                'message' => 'Platform earnings n\'ont pas été supprimées'
            ], 404);
        }
      
        return response()->json([
            'message' => 'Platform earnings supprimées'
        ], 204);
    }

    /**
     * Import platform earnings per platform (uber, bolt, heetch)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importPlatformEarnings(Request $request)
    {
        // Get the platform from the request
        $platform = strtolower($request->platform);
        // Get the results
        $results = [];
        // Get the user id
        $user = $request->user()->id;

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
        
        foreach ($driversData as $driverData) {
            try {
                $driver = $importer->importDriver($driverData, $user);
                $earning = $importer->importEarnings($driver, $driverData, $user);
                $results[] = $earning;
            } catch (\Exception $e) {
                logger("Error importing driver: " . $e->getMessage());
                continue;
            }
        }

        return response()->json([
            'message' => 'Platform earnings ont été importées avec succès',
        ], 201);
    }


}
