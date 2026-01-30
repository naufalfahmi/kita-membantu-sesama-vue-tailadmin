<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the active resource.
     */
    public function index()
    {
        // Default only show active
        $accounts = BankAccount::orderBy('created_at', 'asc')->get();
        
        $totalBalance = $accounts->where('is_active', true)->sum('balance');

        return response()->json([
            'success' => true,
            'data' => $accounts,
            'total_balance' => (float)$totalBalance,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:255',
            'balance' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $account = BankAccount::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil ditambahkan',
                'data' => $account,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan rekening',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $account = BankAccount::find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'bank_name' => 'sometimes|required|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:255',
            'balance' => 'sometimes|required|numeric',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $account->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil diperbarui',
                'data' => $account,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui rekening',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $account = BankAccount::find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak ditemukan',
            ], 404);
        }

        try {
            $account->delete(); // Soft delete

            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus rekening',
            ], 500);
        }
    }
}
