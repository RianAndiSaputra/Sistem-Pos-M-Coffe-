@extends('layouts.app')

@section('title', 'Prediksi Tren Penjualan')

@section('content')

<!-- Alert Notification -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-3 w-80">
    <!-- Alert will appear here dynamically -->
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 flex items-center gap-3">
        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#3b6b0d]"></div>
        <span class="text-gray-700 font-medium">Memuat data prediksi...</span>
    </div>
</div>

<!-- Page Title + Action -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h1 class="text-4xl font-bold text-gray-800">Prediksi Tren Penjualan</h1>
        <div class="flex gap-2">
            <button onclick="refreshPredictions()" class="px-4 py-2 bg-[#3b6b0d] text-white rounded-lg hover:bg-[#2d550a] flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                Refresh Prediksi
            </button>
            <button onclick="exportPredictions()" class="px-4 py-2 bg-white text-green-500 border border-green-500 rounded-lg hover:bg-green-50 flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                Ekspor
            </button>
        </div>
    </div>
</div>

<!-- Card: Outlet Info -->
<div class="bg-white rounded-md p-4 shadow-md mb-4">
    <div class="flex items-start gap-2">
        <i data-lucide="store" class="w-5 h-5 text-gray-600 mt-1"></i>
        <div>
            <h4 class="text-lg font-semibold text-gray-800">Menampilkan laporan untuk: MCoffee - Pusat</h4>
            <p class="text-sm text-gray-600">Data yang ditampilkan adalah khusus untuk outlet MCoffee - Pusat.</p>
        </div>
    </div>
</div>

<!-- Insights Cards -->
<div id="insightsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <!-- Insights will be loaded here -->
</div>

<!-- Main Content -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <!-- Header + Filter -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">📊 Grafik Prediksi Tren Penjualan</h2>
            <p class="text-sm text-gray-600">Berdasarkan analisis data penjualan historis</p>
        </div>
        
        <!-- Filter Controls -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Time Range Filter -->
            <div class="relative">
                <select id="timeRange" onchange="changePeriod()" class="pl-10 pr-8 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#3b6b0d] appearance-none bg-white">
                    <option value="daily">Harian</option>
                    <option value="weekly">Mingguan</option>
                    <option value="monthly" selected>Bulanan</option>
                </select>
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                </span>
            </div>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="h-96 bg-gray-50 rounded-lg p-4 mb-6">
        <canvas id="salesTrendChart"></canvas>
    </div>

    <!-- Prediction Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-gray-700 border-b-2">
                <tr>
                    <th class="py-3 font-bold">Periode</th>
                    <th class="py-3 font-bold text-right">Prediksi Pendapatan</th>
                    <th class="py-3 font-bold text-right">Prediksi Transaksi</th>
                    <th class="py-3 font-bold text-right">Confidence Score</th>
                    <th class="py-3 font-bold text-center">Tren</th>
                </tr>
            </thead>
            <tbody id="predictionTableBody" class="text-gray-700 divide-y">
                <!-- Table rows will be loaded here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Historical Data Chart -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">📈 Data Historis Penjualan</h3>
    <div class="h-96 bg-gray-50 rounded-lg p-4">
        <canvas id="historicalChart"></canvas>
    </div>
</div>

<!-- Today's Prediction -->
<div id="todayPrediction" class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <!-- Today's prediction will be loaded here -->
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let forecastData = null;
    let salesTrendChart = null;
    let historicalChart = null;
    let currentPeriod = 'monthly';

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadForecastData();
    });

    // Load forecast data from API
    async function loadForecastData() {
        showLoading(true);
        
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                showAlert('error', 'Token tidak ditemukan. Silakan login kembali.');
                window.location.href = '/login';
                return;
            }

            const response = await fetch('http://127.0.0.1:8000/api/forecasts/dashboard', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (response.status === 401) {
                showAlert('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                forecastData = result.data;
                renderInsights();
                renderTodayPrediction();
                renderCharts();
                renderPredictionTable();
                showAlert('success', '✅ Data prediksi berhasil dimuat!');
            } else {
                throw new Error(result.message || 'Gagal memuat data');
            }
        } catch (error) {
            console.error('Error loading forecast data:', error);
            showAlert('error', 'Gagal memuat data prediksi: ' + error.message);
        } finally {
            showLoading(false);
        }
    }

    // Render insights cards
    function renderInsights() {
        const container = document.getElementById('insightsContainer');
        if (!forecastData.insights || forecastData.insights.length === 0) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = forecastData.insights.map(insight => {
            const bgColor = insight.type === 'positive' ? 'bg-green-50' : 
                           insight.type === 'negative' ? 'bg-red-50' : 'bg-blue-50';
            const textColor = insight.type === 'positive' ? 'text-green-700' : 
                             insight.type === 'negative' ? 'text-red-700' : 'text-blue-700';
            const icon = insight.type === 'positive' ? 'trending-up' : 
                        insight.type === 'negative' ? 'trending-down' : 'info';
            
            return `
                <div class="${bgColor} rounded-lg p-4 shadow">
                    <div class="flex items-start gap-3">
                        <i data-lucide="${icon}" class="w-5 h-5 ${textColor} mt-1"></i>
                        <p class="${textColor} text-sm font-medium">${insight.message}</p>
                    </div>
                </div>
            `;
        }).join('');

        // Re-initialize lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Render today's prediction
    function renderTodayPrediction() {
        const container = document.getElementById('todayPrediction');
        const today = forecastData.today;
        
        if (!today || !today.predicted) {
            container.innerHTML = '<p class="text-gray-500">Data prediksi hari ini tidak tersedia.</p>';
            return;
        }

        const predicted = today.predicted;
        const actual = today.actual;
        const hasActual = actual && actual.revenue !== null;

        container.innerHTML = `
            <h3 class="text-lg font-bold text-gray-800 mb-4">📅 Prediksi Hari Ini</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-blue-600 mb-1">Prediksi Pendapatan</p>
                    <p class="text-2xl font-bold text-blue-700">${formatCurrency(predicted.predicted_revenue)}</p>
                    <p class="text-xs text-blue-500 mt-1">Transaksi: ${parseFloat(predicted.predicted_transactions).toFixed(0)}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Confidence Score</p>
                    <p class="text-2xl font-bold text-gray-700">${(parseFloat(predicted.confidence_score) * 100).toFixed(1)}%</p>
                    <p class="text-xs text-gray-500 mt-1">Range: ${formatCurrency(predicted.lower_bound)} - ${formatCurrency(predicted.upper_bound)}</p>
                </div>
                ${hasActual ? `
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-green-600 mb-1">Realisasi Aktual</p>
                    <p class="text-2xl font-bold text-green-700">${formatCurrency(actual.revenue)}</p>
                    <p class="text-xs text-green-500 mt-1">Transaksi: ${actual.transactions}</p>
                </div>
                ` : `
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Realisasi Aktual</p>
                    <p class="text-2xl font-bold text-gray-700">Belum Ada Data</p>
                    <p class="text-xs text-gray-500 mt-1">Menunggu data transaksi hari ini</p>
                </div>
                `}
            </div>
        `;
    }

    // Render charts
    function renderCharts() {
        renderSalesTrendChart();
        renderHistoricalChart();
    }

    // Render sales trend chart
    function renderSalesTrendChart() {
        const ctx = document.getElementById('salesTrendChart').getContext('2d');
        
        if (salesTrendChart) {
            salesTrendChart.destroy();
        }

        const forecasts = forecastData.forecasts[currentPeriod];
        const historical = forecastData.historical;

        // Prepare data
        const allDates = [
            ...historical.map(h => h.date),
            ...forecasts.map(f => formatDate(f.forecast_date))
        ];

        const historicalData = historical.map(h => parseFloat(h.revenue));
        const forecastDataPoints = Array(historical.length).fill(null).concat(
            forecasts.map(f => parseFloat(f.predicted_revenue))
        );

        salesTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: allDates,
                datasets: [
                    {
                        label: 'Penjualan Historis',
                        data: [...historicalData, ...Array(forecasts.length).fill(null)],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3
                    },
                    {
                        label: 'Prediksi',
                        data: forecastDataPoints,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += formatCurrency(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Pendapatan (Rp)'
                        },
                        ticks: {
                            callback: function(value) {
                                return formatCurrencyShort(value);
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Periode'
                        }
                    }
                }
            }
        });
    }

    // Render historical chart
    function renderHistoricalChart() {
        const ctx = document.getElementById('historicalChart').getContext('2d');
        
        if (historicalChart) {
            historicalChart.destroy();
        }

        const historical = forecastData.historical;

        historicalChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: historical.map(h => h.date),
                datasets: [{
                    label: 'Pendapatan Harian',
                    data: historical.map(h => parseFloat(h.revenue)),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: '#3b82f6',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Pendapatan: ${formatCurrency(context.parsed.y)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Pendapatan (Rp)'
                        },
                        ticks: {
                            callback: function(value) {
                                return formatCurrencyShort(value);
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    }
                }
            }
        });
    }

    // Render prediction table
    function renderPredictionTable() {
        const tbody = document.getElementById('predictionTableBody');
        const forecasts = forecastData.forecasts[currentPeriod];

        tbody.innerHTML = forecasts.map((forecast, index) => {
            const growth = index > 0 ? 
                ((parseFloat(forecast.predicted_revenue) - parseFloat(forecasts[index-1].predicted_revenue)) / 
                parseFloat(forecasts[index-1].predicted_revenue) * 100) : 0;
            
            const isPositive = growth >= 0;
            const trendColor = isPositive ? 'text-green-500' : 'text-red-500';
            const trendIcon = isPositive ? 'trending-up' : 'trending-down';

            return `
                <tr class="hover:bg-gray-50 transition-colors duration-200 bg-green-50">
                    <td class="py-4 font-medium">${formatDate(forecast.forecast_date)}</td>
                    <td class="py-4 text-right font-semibold">${formatCurrency(forecast.predicted_revenue)}</td>
                    <td class="py-4 text-right font-semibold">${parseFloat(forecast.predicted_transactions).toFixed(0)}</td>
                    <td class="py-4 text-right font-semibold">${(parseFloat(forecast.confidence_score) * 100).toFixed(1)}%</td>
                    <td class="py-4 text-center">
                        <div class="flex items-center justify-center gap-1 ${trendColor}">
                            <i data-lucide="${trendIcon}" class="w-4 h-4"></i>
                            <span class="text-xs">${Math.abs(growth).toFixed(1)}%</span>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Re-initialize lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Change period filter
    function changePeriod() {
        const select = document.getElementById('timeRange');
        currentPeriod = select.value;
        renderCharts();
        renderPredictionTable();
        showAlert('info', `Menampilkan data periode: ${select.options[select.selectedIndex].text}`);
    }

    // Refresh predictions
    function refreshPredictions() {
        loadForecastData();
    }

    // Export predictions
    function exportPredictions() {
        showAlert('info', '📊 Menyiapkan ekspor data prediksi...');
        
        setTimeout(() => {
            const forecasts = forecastData.forecasts[currentPeriod];
            let csv = 'Periode,Prediksi Pendapatan,Prediksi Transaksi,Confidence Score,Lower Bound,Upper Bound\n';
            
            forecasts.forEach(f => {
                csv += `"${formatDate(f.forecast_date)}","${f.predicted_revenue}","${f.predicted_transactions}","${f.confidence_score}","${f.lower_bound}","${f.upper_bound}"\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', `prediksi-${currentPeriod}-${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showAlert('success', '✅ Data prediksi berhasil diekspor!');
        }, 1000);
    }

    // Utility functions
    function formatCurrency(value) {
        if (!value || value === null) return 'Rp 0';
        return 'Rp ' + parseFloat(value).toLocaleString('id-ID', { 
            minimumFractionDigits: 0,
            maximumFractionDigits: 0 
        });
    }

    function formatCurrencyShort(value) {
        if (!value || value === null) return 'Rp 0';
        const num = parseFloat(value);
        if (num >= 1000000) {
            return 'Rp ' + (num / 1000000).toFixed(1) + ' Jt';
        } else if (num >= 1000) {
            return 'Rp ' + (num / 1000).toFixed(0) + ' Rb';
        }
        return 'Rp ' + num.toFixed(0);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (show) {
            overlay.classList.remove('hidden');
        } else {
            overlay.classList.add('hidden');
        }
    }

    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `px-4 py-3 rounded-lg shadow-md ${
            type === 'error' ? 'bg-red-100 text-red-700' : 
            type === 'success' ? 'bg-green-100 text-green-700' : 
            'bg-orange-100 text-orange-700'
        }`;
        alert.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="hover:opacity-70 text-xl leading-none">
                    ×
                </button>
            </div>
        `;
        alertContainer.appendChild(alert);
        
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
</script>

@endsection