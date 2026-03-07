<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Dashboard Empresarial</h2></x-slot>
    <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">Produtos: <strong>{{ $totalProducts }}</strong></div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">Categorias: <strong>{{ $totalCategories }}</strong></div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow text-red-600 dark:border-gray-700 dark:bg-gray-800">Estoque baixo: <strong>{{ $lowStock }}</strong></div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">Lucro total: <strong>R$ {{ number_format($profit,2,',','.') }}</strong></div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">Receita Mensal</h3>
            <canvas id="monthlyRevenueChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($monthlyRevenue->pluck('month'));
        const values = @json($monthlyRevenue->pluck('total'));
        new Chart(document.getElementById('monthlyRevenueChart'), {
            type: 'line',
            data: { labels, datasets: [{ label: 'Receita', data: values, borderColor: '#4f46e5' }] },
        });
    </script>
</x-app-layout>
