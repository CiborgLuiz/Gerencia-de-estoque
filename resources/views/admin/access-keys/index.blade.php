<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">Chaves de acesso</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->has('access_key'))
                <div class="rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-200">
                    {{ $errors->first('access_key') }}
                </div>
            @endif

            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-700 dark:bg-gray-800">
                <form method="POST" action="{{ route('admin.access-keys.store') }}" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div>
                        <x-input-label for="role_id" :value="__('Cargo da chave')" class="dark:text-gray-200" />
                        <select id="role_id" name="role_id" class="mt-1 rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                            @forelse($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                            @empty
                                <option value="">Sem cargos disponíveis</option>
                            @endforelse
                        </select>
                    </div>
                    <x-primary-button :disabled="$roles->isEmpty()">Gerar chave</x-primary-button>
                </form>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-700 dark:bg-gray-800">
                <h3 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Chaves geradas</h3>
                <div class="overflow-auto">
                    <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                        <thead>
                            <tr class="text-left border-b border-gray-200 dark:border-gray-700">
                                <th class="py-2 pr-4">Código</th>
                                <th class="py-2 pr-4">Cargo</th>
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2 pr-4">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($keys as $key)
                                @php($isRevoked = (bool) ($key->revoked_at ?? false) || (!$key->used_at && $key->expires_at && $key->expires_at->isPast()))
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <td class="py-2 pr-4 font-mono">{{ $key->code }}</td>
                                    <td class="py-2 pr-4">{{ $key->role?->name }}</td>
                                    <td class="py-2 pr-4">
                                        @if($isRevoked)
                                            <span class="rounded bg-gray-200 px-2 py-1 text-xs font-semibold uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-200">Desligada</span>
                                        @elseif($key->used_at)
                                            <span class="rounded bg-blue-100 px-2 py-1 text-xs font-semibold uppercase text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Utilizada</span>
                                        @else
                                            <span class="rounded bg-emerald-100 px-2 py-1 text-xs font-semibold uppercase text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Disponível</span>
                                        @endif
                                    </td>
                                    <td class="py-2 pr-4">
                                        @if(!$key->used_at && !$isRevoked)
                                            <form method="POST" action="{{ route('admin.access-keys.revoke', $key) }}" onsubmit="return confirm('Desligar esta chave de acesso?')">
                                                @csrf
                                                <button class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-500">
                                                    Desligar
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Sem ação</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 text-sm text-gray-600 dark:text-gray-300">Nenhuma chave cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $keys->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
