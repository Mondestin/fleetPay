<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Invoice;
class SubscriptionController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse The subscriptions.
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
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse The created subscription.
     */
    public function store(Request $request)
    {
        
        //validate the request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(['active', 'canceled', 'expired'])],
            'payment_status' => ['required', Rule::in(['paid', 'pending', 'failed'])],
        ]);

        try {
            //create a subscription
            $subscription = Subscription::create($validated);

            //create an invoice for the subscription
            $invoice = Invoice::create([
                'subscription_id' => $subscription->id,
                'amount' => $validated['amount'],
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
            ]);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Subscription not created'], 500);
        }
        return response()->json($subscription->load('user'), 201);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse The updated subscription.
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
    * Cancel the specified subscription.
    *
    * @param Request $request The HTTP request object.
    * @param User $user The user to cancel the subscription for.
    * @return \Illuminate\Http\JsonResponse The cancelled subscription.
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
                return response()->json(['error' => 'Subscription not deleted'], 500);
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
        try {
            $subscription = Subscription::where('user_id', $user->id)->with('invoices')->first();
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Subscription not found'], 404);
        }
     
        return response()->json($subscription);
    }

    /**
     * Perform an action (cancel, resume) on the specified subscription.
     *
     * @param Request $request The HTTP request object.
     * @param User $user The user to perform the action on.
     * @param string $action The action to perform.
     * @return \Illuminate\Http\JsonResponse The action result.
     */
    public function action(Request $request)
    {
        $user = $request->user;
        $action = $request->action;

        try {   
            //get the subscription
            $subscription = Subscription::where('user_id', $user)->first();
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        //cancel the subscription
        if ($action == 'cancel') {
            $subscription->status = 'canceled';
            $subscription->save();
        }
        //resume the subscription
        if ($action == 'resume') {
            $subscription->status = 'active';
            $subscription->save();
        }

        return response()->json($subscription);
    }
 
    /**
     * Get the invoices for a subscription.
     *
     * @param Request $request The HTTP request object.
     * @param Subscription $subscription The subscription to get the invoices for.
     * @return \Illuminate\Http\JsonResponse The invoices for the subscription.
     */
    public function invoices(Request $request)
    {
        try {
            //get the subscription with the invoices
            $subscription = Subscription::with('invoices')->findOrFail($request->subscription);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Invoices not found'], 404);
        }
        return response()->json($subscription);
    }
}
