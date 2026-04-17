<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $employees = User::query()
            ->with('role')
            ->where('id', '!=', (int) $request->user()->id)
            ->orderBy('name')
            ->paginate(20);

        return view('admin.employees.index', [
            'employees' => $employees,
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === (int) $request->user()->id) {
            return back()->withErrors([
                'employee' => 'Você não pode desvincular a própria conta.',
            ]);
        }

        if ($user->sales()->exists() || $user->invoices()->exists() || $user->serviceInvoices()->exists()) {
            return back()->withErrors([
                'employee' => 'Não é possível apagar este funcionário porque ele possui vendas ou notas fiscais vinculadas.',
            ]);
        }

        try {
            $user->forceDelete();
        } catch (Throwable) {
            return back()->withErrors([
                'employee' => 'Não foi possível apagar este funcionário no banco de dados.',
            ]);
        }

        return back()->with('status', 'Funcionário apagado com sucesso.');
    }
}
