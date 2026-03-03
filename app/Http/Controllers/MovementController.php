<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovementController extends Controller
{
    public function index()
    {
        $movements = Movement::query()
            ->with('product')
            ->latest()
            ->paginate(20);

        return view('admin.movements.index', compact('movements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:entrada,saida'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $product = Product::query()->lockForUpdate()->findOrFail($validated['product_id']);

                if ($validated['type'] === 'saida' && $product->stock < $validated['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Estoque insuficiente para essa saída.',
                    ]);
                }

                $newStock = $validated['type'] === 'entrada'
                    ? $product->stock + $validated['quantity']
                    : $product->stock - $validated['quantity'];

                $product->update(['stock' => $newStock]);

                Movement::create($validated);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        }

        return back()->with('success', 'Movimentação registrada com sucesso.');
    }
}
