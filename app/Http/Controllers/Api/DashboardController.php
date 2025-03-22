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
     /*
        // Get weekly revenue
        $weeklyRevenue = PlatformEarning::where('user_id', $user->id)
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->sum('amount');

        // Calculate percentage change from previous week
        $previousWeekRevenue = PlatformEarning::where('user_id', $user->id)
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->sum('amount');
        
        $percentageChange = $previousWeekRevenue != 0 
            ? (($weeklyRevenue - $previousWeekRevenue) / $previousWeekRevenue) * 100 
            : 0;


        // Get weekly comparison data by platform
        $currentWeekByPlatform = PlatformEarning::where('user_id', $user->id)
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
            ->groupBy('platform')
            ->select('platform', DB::raw('SUM(amount) as total'))
            ->get()
            ->pluck('total', 'platform')
            ->toArray();

        $previousWeekByPlatform = PlatformEarning::where('user_id', $user->id)
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekEnd])
            ->groupBy('platform')
            ->select('platform', DB::raw('SUM(amount) as total'))
            ->get()
            ->pluck('total', 'platform')
            ->toArray();

        // Get monthly revenue data (last 6 months)
        $monthlyRevenue = PlatformEarning::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('platform')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->select(
                'platform',
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->get()
            ->groupBy('platform');
*/
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
