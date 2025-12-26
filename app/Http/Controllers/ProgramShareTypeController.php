<?php

namespace App\Http\Controllers;

use App\Models\ProgramShareType;
use Illuminate\Http\Request;

class ProgramShareTypeController extends Controller
{
    public function index(Request $request)
    {
        $items = ProgramShareType::orderBy('orders', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}
