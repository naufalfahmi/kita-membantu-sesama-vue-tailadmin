<?php

namespace App\Http\Controllers;

use App\Models\LandingProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view landing profile')->only('index');
        $this->middleware('permission:create landing profile')->only('store');
        $this->middleware('permission:update landing profile')->only('update');
    }
    /**
     * Return the first landing profile (there should be at most one)
     */
    public function index()
    {
        $profile = LandingProfile::first();

        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }

    /**
     * Store a new landing profile
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|array',
            'bank_account_1' => 'nullable|array',
            'bank_account_2' => 'nullable|string',
            'about' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['created_by'] = auth()->id();

            $profile = LandingProfile::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Landing profile created',
                'data' => $profile,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create landing profile',
                'error' => config('app.debug') ? $th->getMessage() : 'Error',
            ], 500);
        }
    }

    /**
     * Update (or create if not exists) the landing profile (single resource)
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|array',
            'bank_account_1' => 'nullable|array',
            'bank_account_2' => 'nullable|string',
            'about' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            $profile = LandingProfile::first();

            if (! $profile) {
                $data['created_by'] = auth()->id();
                $profile = LandingProfile::create($data);
            } else {
                $data['updated_by'] = auth()->id();
                $profile->update($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Landing profile saved',
                'data' => $profile->fresh(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save landing profile',
                'error' => config('app.debug') ? $th->getMessage() : 'Error',
            ], 500);
        }
    }
}
