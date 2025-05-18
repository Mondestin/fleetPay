<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Subscription;
class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('subscription.user');

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('amount', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('subscription.user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('first_name', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 1000);
        $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'subscription_id' => 'required|exists:subscriptions,id',
                'amount' => 'required|numeric|min:0',
                'status' => ['required', Rule::in(['paid', 'pending', 'failed'])],
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:issue_date',
        ]);

        $validated['invoice_number'] = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Invoice::count() + 1);
        $validated['created_by'] = 1; // Temporary hardcoded user ID
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 422);
        }

        try {
            $invoice = Invoice::create($validated);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($invoice->load(['user', 'createdBy']), 201);
    }

    public function show(Request $request)
    {
        try {
           
          
            //get all invoices for the user
            $invoices = Invoice::where('user_id', $request->invoice)->get();
            logger("invoices: " . $invoices);
            if(!$invoices) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }
            return response()->json($invoices);

        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Error fetching invoice'], 404);
        }
    }

    public function update(Request $request, Invoice $invoice)
    {

        $validated = $request->validate([
                'subscription_id' => 'required|exists:subscriptions,id',
                'amount' => 'required|numeric|min:0',
                'status' => ['required', Rule::in(['paid', 'pending', 'overdue'])],
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:issue_date',
        ]);

        try {
            logger("finding the invoice: " . $request->invoice_number);
            $invoice = Invoice::where('invoice_number', $request->invoice_number)->first();
      
            $invoice->update($validated);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json("Invoice updated successfully");
        
    }

    public function destroy(Request $request)
    {
        try {
            $invoice = Invoice::find($request->invoice);
            try {
                $invoice->delete();
                return response()->json("Invoice deleted successfully");
            } catch (\Exception $e) {
                logger($e->getMessage());
                return response()->json(['error' => $e->getMessage()], 404);
            }
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Invoice not found'], 404);
        }
    }
} 