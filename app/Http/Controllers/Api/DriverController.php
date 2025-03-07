<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::query()
            ->when(request('status'), fn($q, $status) => $q->where('status', $status))
            ->when(request('search'), function($q, $search) {
                $q->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->orderBy(request('sort_by', 'created_at'), request('sort_direction', 'desc'))
            ->paginate(request('per_page', 100));
        
        return response()->json($drivers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:drivers',
            'phone_number' => 'nullable|string|max:20',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $driver = Driver::create($validated);

        return response()->json($driver, 201);
    }

    public function show(Request $request)
    {
      try { 
        $driver = Driver::findOrFail($request->driver);
        return response()->json($driver);
      } catch (\Exception $e) {
        return response()->json(['error' => 'Driver not found'], 404);
      }
    }

    public function update(Request $request, Driver $driver)
    {
       $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['nullable', 'email', Rule::unique('drivers')->ignore($driver->id)],
            'phone_number' => 'nullable|string|max:20',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
       try {
     
        $driver = Driver::findOrFail($request->driver);
        $driver->update($validated);

        return response()->json($driver);
      } catch (\Exception $e) {
        return response()->json(['error' => 'Driver not found'], 404);
      }
    }

    public function destroy(Request $request)
    {
      try {
        $driver = Driver::findOrFail($request->driver);
        try {
            $driver->delete();
            return response()->json("Driver deleted successfully");
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
      } catch (\Exception $e) {
        return response()->json(['error' => 'Driver not found'], 404);
      }
    }
} 