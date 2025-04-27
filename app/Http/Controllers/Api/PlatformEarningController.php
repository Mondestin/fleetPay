<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlatformEarning;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Setting;
class PlatformEarningController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
     
        $earnings = PlatformEarning::query()
            ->with(['driver', 'createdBy'])
            ->where('created_by', $user->id)
            ->when(request('driver_id'), fn($q, $driverId) => $q->where('driver_id', $driverId))
            ->when(request('platform'), fn($q, $platform) => $q->where('platform', $platform))
            ->when(request('validated'), fn($q, $validated) => $q->where('validated', $validated))
            ->when(request('week_start'), function($q, $weekStart) {
                $weekEnd = date('Y-m-d', strtotime($weekStart . ' + 6 days'));
                $q->whereBetween('week_start_date', [$weekStart, $weekEnd]);
            })
            ->when(request('search'), function($q, $search) {
                $q->whereHas('driver', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->orderBy(request('sort_by', 'week_start_date'), request('sort_direction', 'desc'))
            ->get()
            ->groupBy(['driver_id', 'week_start_date'])
            ->map(function ($driverWeekEarnings) use ($user) {
                $firstEarning = $driverWeekEarnings->first()->first();
                $totalEarnings = [
                    'bolt_earnings' => '0',
                    'uber_earnings' => '0',
                    'heetch_earnings' => '0'
                ];

                // Sum up earnings by platform
                foreach ($driverWeekEarnings as $weekEarnings) {
                    foreach ($weekEarnings as $earning) {
                        $totalEarnings[$earning->platform . '_earnings'] = (string) $earning->earnings;
                    }
                }

                // Calculate totals
                $total = array_sum(array_map('floatval', $totalEarnings));
                $commission = Setting::where('name', 'commission')->where('user_id', $user->id)->first()->value;
                logger("commission user: $commission");
                $totalDue = $total - $commission;

                return [
                    'id' => $firstEarning->id,
                    'driver_id' => $firstEarning->driver_id,
                    'week_start_date' => $firstEarning->week_start_date->format('Y-m-d'),
                    'bolt_earnings' => $totalEarnings['bolt_earnings'],
                    'uber_earnings' => $totalEarnings['uber_earnings'],
                    'heetch_earnings' => $totalEarnings['heetch_earnings'],
                    'commission_amount' => (string) $commission,
                    'total_due' => (string) $totalDue,
                    'status' => $firstEarning->status,
                    'created_by' => $firstEarning->createdBy,
                    'created_at' => $firstEarning->created_at->toDateTimeString(),
                    'updated_at' => $firstEarning->updated_at->toDateTimeString(),
                    'total_earnings' => $total,
                    'driver' => $firstEarning->driver
                ];
            })
            ->values();
        logger("earnings: ");
        // Paginate the results manually
        $page = request('page', 1);
        $perPage = request('per_page', 100);
        $items = $earnings->forPage($page, $perPage);
        
   
        return response()->json([
            'data' => $items,
            'current_page' => (int) $page,
            'per_page' => (int) $perPage,
            'total' => $earnings->count()
        ]);
    }


    public function show(Request $request)
    {
        try {
            $platformEarning = PlatformEarning::findOrFail($request->platform_earning);
            return response()->json($platformEarning->load(['driver', 'uploadedBy']));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Platform earning not found'], 404);
        }
    }

    public function update(Request $request, PlatformEarning $platformEarning)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid',
        ]);
        try {
            $platformEarning = PlatformEarning::findOrFail($request->platform_earning);
            $platformEarning->update($validated);

            return response()->json($platformEarning->load(['driver', 'createdBy']));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Platform earning not found'], 404);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $platformEarning = PlatformEarning::findOrFail($request->platform_earning);
            try {
                $platformEarning->delete();
                return response()->json("Platform earning deleted successfully");
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Platform earning not found'], 404);
        }
    }
} 