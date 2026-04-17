<div class="flex flex-wrap items-center gap-2 rounded-lg border border-gray-200 bg-white p-2 shadow dark:border-gray-700 dark:bg-gray-800">
    <a
        href="{{ route('invoices.index') }}"
        @class([
            'rounded-md px-4 py-2 text-sm font-semibold transition',
            'bg-indigo-600 text-white' => request()->routeIs('invoices.*'),
            'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' => !request()->routeIs('invoices.*'),
        ])
    >
        NF-e
    </a>
    <a
        href="{{ route('nfse.index') }}"
        @class([
            'rounded-md px-4 py-2 text-sm font-semibold transition',
            'bg-indigo-600 text-white' => request()->routeIs('nfse.*'),
            'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' => !request()->routeIs('nfse.*'),
        ])
    >
        NFS-e
    </a>
</div>
