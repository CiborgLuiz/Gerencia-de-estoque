<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">Funcionários</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('employee'))
                <div class="rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/20 dark:text-red-200">
                    {{ $errors->first('employee') }}
                </div>
            @endif

            <div class="overflow-auto rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-800">
                <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left">Nome</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Cargo</th>
                            <th class="px-4 py-3 text-left">Criado em</th>
                            <th class="px-4 py-3 text-left">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-3 font-semibold">{{ $employee->name }}</td>
                                <td class="px-4 py-3">{{ $employee->email }}</td>
                                <td class="px-4 py-3">{{ $employee->role?->name ?? $employee->iden }}</td>
                                <td class="px-4 py-3">{{ $employee->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Deseja realmente apagar este funcionário? Esta ação remove a conta do banco quando não houver histórico vinculado.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-500">
                                            Apagar funcionário
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-300">
                                    Nenhum funcionário encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $employees->links() }}
        </div>
    </div>
</x-app-layout>
