<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\PlatformEarning;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display dashboard statistics and charts
     */
    public function index(Request $request)
    {

        $user = $request->user();
        logger($user);
        $company = $user->company;
        
        // Get current week's start and end dates
        $currentWeekStart = $request->input('current_week_start');
        $currentWeekEnd = $request->input('current_week_end');
        
        // Get previous week's start and end dates
        $previousWeekStart = $request->input('previous_week_start');
        $previousWeekEnd = $request->input('previous_week_end');

        // Get active drivers count
        $activeDrivers = Driver::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'active_drivers' => $activeDrivers,
                'weekly_revenue' => [
                    'amount' => "weeklyRevenue",
                    'percentage_change' => "percentageChange"
                ],

                'weekly_comparison' => [
                    'current' => "currentWeekByPlatform",
                    'previous' => "previousWeekByPlatform"
                ],
                'monthly_revenue' => "monthlyRevenue"
            ]
        ]);
    }
}
