<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::query()
            ->with(['user', 'createdBy'])
            ->when(request('status'), fn($q, $status) => $q->where('status', $status))
            ->when(request('search'), function($q, $search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('invoice_number', 'like', "%{$search}%");
            })
            ->when(request('from_date'), fn($q, $date) => $q->where('issue_date', '>=', $date))
            ->when(request('to_date'), fn($q, $date) => $q->where('issue_date', '<=', $date))
            ->orderBy(request('sort_by', 'issue_date'), request('sort_direction', 'desc'))
            ->paginate(request('per_page', 15));

            
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0',
                'status' => ['required', Rule::in(['paid', 'pending', 'overdue'])],
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
            logger()->info("Invoice ID: " . $request->invoice);
            $invoice = Invoice::findOrFail($request->invoice);
            return response()->json($invoice->load(['user', 'createdBy']));
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => 'Invoice not found'], 404);
        }
    }

    public function update(Request $request, Invoice $invoice)
    {
      
        $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0',
                'status' => ['required', Rule::in(['paid', 'pending', 'overdue'])],
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:issue_date',
        ]);

        try {
            $invoice = Invoice::findOrFail($request->invoice);
            $invoice->update($validated);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($invoice->load(['user', 'createdBy']));
        
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