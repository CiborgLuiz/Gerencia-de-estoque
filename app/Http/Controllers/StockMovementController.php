<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use Illuminate\Http\Request;

/**
 * @deprecated Mantido temporariamente por retrocompatibilidade.
 */
class StockMovementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:entrada,saida'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        Movement::create($validated);

        return back()->with('success', 'Movimentação registrada!');
    }
}
