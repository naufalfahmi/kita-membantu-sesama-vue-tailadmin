<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Remunerasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RemunerasiController extends Controller
{
    /**
     * Display a listing of remunerasi with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Remunerasi API is deprecated; use /operasional/payroll instead'], 410);
    }

    /**
     * Store a newly created remunerasi.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Remunerasi API is deprecated; use /operasional/payroll instead'], 410);
    }

    /**
     * Display the specified remunerasi.
     */
    public function show($id): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Remunerasi API is deprecated; use /operasional/payroll instead'], 410);
    }

    /**
     * Update the specified remunerasi.
     */
    public function update(Request $request, $id): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Remunerasi API is deprecated; use /operasional/payroll instead'], 410);
    }

    /**
     * Remove the specified remunerasi.
     */
    public function destroy($id): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Remunerasi API is deprecated; use /operasional/payroll instead'], 410);
    }
}
