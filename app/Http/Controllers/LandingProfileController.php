<?php

namespace App\Http\Controllers;

use App\Models\LandingProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingProfileController extends Controller
{
    public function __construct()
    {
        // Any authenticated user may view the landing profile (read-only)
        // (the route group already enforces 'auth')

        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && ($user->hasRole('admin') || $user->can('create landing profile'))) {
                return $next($request);
            }
            abort(403);
        })->only('store');

        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && ($user->hasRole('admin') || $user->can('update landing profile'))) {
                return $next($request);
            }
            abort(403);
        })->only('update');
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

            // Vision & Mission
            'vision_title' => 'nullable|string|max:255',
            'vision_description' => 'nullable|string',
            'mission_description' => 'nullable|string',
            'vision_mission_image' => 'nullable|string',
            'features' => 'nullable|array',

            // CTA
            'cta_title' => 'nullable|string|max:255',
            'cta_description' => 'nullable|string',
            'cta_button_active' => 'nullable|boolean',
            'cta_button_link' => 'nullable|string',

            // Hero
            'hero_title' => 'nullable|string|max:255',
            'hero_description' => 'nullable|string',
            'hero_button_active' => 'nullable|boolean',
            'hero_button_link' => 'nullable|string',
            'hero_whatsapp_active' => 'nullable|boolean',
            'hero_whatsapp_number' => 'nullable|string',
            'hero_image' => 'nullable|string',
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

            // Vision & Mission
            'vision_title' => 'nullable|string|max:255',
            'vision_description' => 'nullable|string',
            'mission_description' => 'nullable|string',
            'vision_mission_image' => 'nullable|string',
            'features' => 'nullable|array',

            // CTA
            'cta_title' => 'nullable|string|max:255',
            'cta_description' => 'nullable|string',
            'cta_button_active' => 'nullable|boolean',
            'cta_button_link' => 'nullable|string',

            // Hero
            'hero_title' => 'nullable|string|max:255',
            'hero_description' => 'nullable|string',
            'hero_button_active' => 'nullable|boolean',
            'hero_button_link' => 'nullable|string',
            'hero_whatsapp_active' => 'nullable|boolean',
            'hero_whatsapp_number' => 'nullable|string',
            'hero_image' => 'nullable|string',
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
