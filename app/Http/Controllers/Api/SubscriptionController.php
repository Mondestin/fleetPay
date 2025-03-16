<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::query()
            ->with('user')
            ->when(request('user_id'), fn($q, $userId) => $q->where('user_id', $userId))
            ->when(request('status'), fn($q, $status) => $q->where('status', $status))
            ->when(request('payment_status'), fn($q, $status) => $q->where('payment_status', $status))
            ->when(request('search'), function($q, $search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->when(request('from_date'), fn($q, $date) => $q->where('start_date', '>=', $date))
            ->when(request('to_date'), fn($q, $date) => $q->where('end_date', '<=', $date))
            ->orderBy(request('sort_by', 'created_at'), request('sort_direction', 'desc'))
            ->paginate(request('per_page', 15));

        return response()->json($subscriptions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        logger($request->all());
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(['active', 'canceled', 'expired'])],
            'payment_status' => ['required', Rule::in(['paid', 'pending', 'failed'])],
        ]);
        logger($validated);
        try {
            
            $subscription = Subscription::create($validated);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json($subscription->load('user'), 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
       
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(['active', 'canceled', 'expired'])],
            'payment_status' => ['required', Rule::in(['paid', 'pending', 'failed'])],
        ]);

        try {
            $subscription = Subscription::findOrFail($request->subscription);
            $subscription->update($validated);

            return response()->json($subscription->load('user'));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Subscription not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $subscription = Subscription::findOrFail($request->subscription);
            try {
                $subscription->delete();
                return response()->json(['message' => 'Subscription deleted successfully']);
            } catch (\Exception $e) {
                logger($e->getMessage());
                return response()->json(['error' => $e->getMessage()], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Subscription not found'], 404);
        }
    }
    /**
     * Display the current subscription for a user.
     *
     * @param Request $request The HTTP request object.
     * @param User $user The user to get the current subscription for.
     * @return \Illuminate\Http\JsonResponse The current subscription for the user.
     */
    public function current(Request $request)
    {
        $user = $request->user();
        $subscription = Subscription::where('user_id', $user->id)->where('status', 'active')->with('invoices')->first();
        logger($subscription);
        return response()->json($subscription);
    }
}
