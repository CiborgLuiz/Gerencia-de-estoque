<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        $user->delete();

        return back()->with('status', 'Conta desvinculada com sucesso.');
    }
}
