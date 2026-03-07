<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Throwable;

class CategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->where(fn ($query) => $query->where('parent_id', $request->input('parent_id'))),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        try {
            Category::create($validated);
        } catch (Throwable) {
            return back()
                ->withInput()
                ->withErrors([
                    'category' => 'Erro ao salvar categoria. Verifique os dados e tente novamente.',
                ]);
        }

        return back()->with('status', 'Categoria criada com sucesso.');
    }
}
