<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard Empresarial</h2></x-slot>
    <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded shadow">Produtos: <strong>{{ $totalProducts }}</strong></div>
            <div class="bg-white p-4 rounded shadow">Categorias: <strong>{{ $totalCategories }}</strong></div>
            <div class="bg-white p-4 rounded shadow text-red-600">Estoque baixo: <strong>{{ $lowStock }}</strong></div>
            <div class="bg-white p-4 rounded shadow">Lucro total: <strong>R$ {{ number_format($profit,2,',','.') }}</strong></div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-semibold mb-2">Receita Mensal</h3>
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
