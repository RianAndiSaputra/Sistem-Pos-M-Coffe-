<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SalesForecast;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesForecastingService
{
    // Konfigurasi default
    protected int $historicalDays = 90;
    protected int $forecastDays = 30;
    protected int $movingAverageWindow = 7;
    protected string $timezone = 'Asia/Jakarta'; // Tambahkan timezone

    public function __construct(
        protected ?int $customHistoricalDays = null,
        protected ?int $customForecastDays = null
    ) {
        if ($customHistoricalDays) $this->historicalDays = $customHistoricalDays;
        if ($customForecastDays) $this->forecastDays = $customForecastDays;
    }

    /**
     * Generate forecast untuk semua period type
     */
    public function generateAllForecasts(): array
    {
        return [
            'daily' => $this->generateDailyForecast(),
            'weekly' => $this->generateWeeklyForecast(),
            'monthly' => $this->generateMonthlyForecast(),
        ];
    }

    /**
     * Prediksi harian
     */
    public function generateDailyForecast(): Collection
    {
        $historicalData = $this->getDailySalesData();
        
        if ($historicalData->count() < 14) {
            throw new \Exception('Minimal 14 hari data historis diperlukan untuk prediksi');
        }

        $forecasts = collect();
        $values = $historicalData->pluck('revenue')->toArray();
        $transactions = $historicalData->pluck('transactions')->toArray();
        $dates = $historicalData->pluck('date')->toArray();

        // Hitung komponen untuk prediksi
        $trend = $this->calculateLinearTrend($values);
        $seasonality = $this->calculateWeeklySeasonality($historicalData);
        $volatility = $this->calculateVolatility($values);

        for ($i = 1; $i <= $this->forecastDays; $i++) {
            $forecastDate = now($this->timezone)->addDays($i)->startOfDay();
            $dayOfWeek = $forecastDate->dayOfWeek;

            // Base prediction menggunakan weighted moving average
            $baseRevenue = $this->weightedMovingAverage($values);
            $baseTransactions = $this->weightedMovingAverage($transactions);

            // Apply trend
            $trendAdjustment = $trend['slope'] * $i;
            
            // Apply weekly seasonality
            $seasonalFactor = $seasonality[$dayOfWeek] ?? 1.0;

            // Final prediction
            $predictedRevenue = ($baseRevenue + $trendAdjustment) * $seasonalFactor;
            $predictedTransactions = $baseTransactions * $seasonalFactor;

            // Confidence interval (95%)
            $margin = 1.96 * $volatility * sqrt($i);
            
            $forecast = SalesForecast::updateOrCreate(
                [
                    'forecast_date' => $forecastDate->toDateString(),
                    'period_type' => 'daily',
                ],
                [
                    'predicted_revenue' => max(0, $predictedRevenue),
                    'predicted_transactions' => max(0, $predictedTransactions),
                    'lower_bound' => max(0, $predictedRevenue - $margin),
                    'upper_bound' => $predictedRevenue + $margin,
                    'confidence_score' => $this->calculateConfidenceScore($i, $historicalData->count()),
                    'metadata' => [
                        'trend_slope' => $trend['slope'],
                        'seasonal_factor' => $seasonalFactor,
                        'base_revenue' => $baseRevenue,
                        'method' => 'weighted_ma_with_seasonality',
                    ],
                ]
            );

            $forecasts->push($forecast);
        }

        return $forecasts;
    }

    /**
     * Prediksi mingguan
     */
    public function generateWeeklyForecast(): Collection
    {
        $historicalData = $this->getWeeklySalesData();
        
        if ($historicalData->count() < 4) {
            throw new \Exception('Minimal 4 minggu data historis diperlukan');
        }

        $forecasts = collect();
        $values = $historicalData->pluck('revenue')->toArray();
        $transactions = $historicalData->pluck('transactions')->toArray();

        $trend = $this->calculateLinearTrend($values);
        $volatility = $this->calculateVolatility($values);

        $weeksToForecast = ceil($this->forecastDays / 7);

        for ($i = 1; $i <= $weeksToForecast; $i++) {
            $forecastDate = now()->addWeeks($i)->startOfWeek();

            $baseRevenue = $this->weightedMovingAverage($values, 4);
            $baseTransactions = $this->weightedMovingAverage($transactions, 4);

            $predictedRevenue = $baseRevenue + ($trend['slope'] * $i);
            $predictedTransactions = $baseTransactions + ($this->calculateLinearTrend($transactions)['slope'] * $i);

            $margin = 1.96 * $volatility * sqrt($i);

            $forecast = SalesForecast::updateOrCreate(
                [
                    'forecast_date' => $forecastDate->toDateString(),
                    'period_type' => 'weekly',
                ],
                [
                    'predicted_revenue' => max(0, $predictedRevenue),
                    'predicted_transactions' => max(0, $predictedTransactions),
                    'lower_bound' => max(0, $predictedRevenue - $margin),
                    'upper_bound' => $predictedRevenue + $margin,
                    'confidence_score' => $this->calculateConfidenceScore($i, $historicalData->count()),
                    'metadata' => [
                        'trend_slope' => $trend['slope'],
                        'method' => 'weighted_ma_weekly',
                    ],
                ]
            );

            $forecasts->push($forecast);
        }

        return $forecasts;
    }

    /**
     * Prediksi bulanan
     */
    public function generateMonthlyForecast(): Collection
    {
        $historicalData = $this->getMonthlySalesData();
        
        // Turunkan minimum requirement jika data terbatas
        $minMonths = max(1, min(3, $historicalData->count()));
        if ($historicalData->count() < 1) {
            throw new \Exception('Minimal 1 bulan data historis diperlukan untuk prediksi bulanan');
        }

        $forecasts = collect();
        $values = $historicalData->pluck('revenue')->toArray();
        $transactions = $historicalData->pluck('transactions')->toArray();

        $trend = $this->calculateLinearTrend($values);
        $volatility = $this->calculateVolatility($values);

        $monthsToForecast = ceil($this->forecastDays / 30);

        for ($i = 1; $i <= max(3, $monthsToForecast); $i++) {
            $forecastDate = now($this->timezone)->addMonths($i)->startOfMonth();

            $baseRevenue = $this->simpleMovingAverage($values, min(3, count($values)));
            $baseTransactions = $this->simpleMovingAverage($transactions, min(3, count($transactions)));

            $predictedRevenue = $baseRevenue + ($trend['slope'] * $i);
            $predictedTransactions = $baseTransactions + ($this->calculateLinearTrend($transactions)['slope'] * $i);

            // Jika data terbatas, gunakan estimasi volatilitas yang lebih konservatif
            $margin = $historicalData->count() >= 3 
                ? 1.96 * $volatility * sqrt($i)
                : $baseRevenue * 0.3 * sqrt($i); // 30% margin jika data terbatas

            $forecast = SalesForecast::updateOrCreate(
                [
                    'forecast_date' => $forecastDate->toDateString(),
                    'period_type' => 'monthly',
                ],
                [
                    'predicted_revenue' => max(0, $predictedRevenue),
                    'predicted_transactions' => max(0, $predictedTransactions),
                    'lower_bound' => max(0, $predictedRevenue - $margin),
                    'upper_bound' => $predictedRevenue + $margin,
                    'confidence_score' => $this->calculateConfidenceScore($i, $historicalData->count()),
                    'metadata' => [
                        'trend_slope' => $trend['slope'],
                        'method' => 'sma_monthly',
                    ],
                ]
            );

            $forecasts->push($forecast);
        }

        return $forecasts;
    }

    /**
     * Ambil data penjualan harian
     */
    protected function getDailySalesData(): Collection
    {
        return Order::query()
            ->where('created_at', '>=', now()->subDays($this->historicalDays))
            ->where('status', 'completed') // Sesuaikan dengan status di sistem Anda
            ->selectRaw('DATE(created_at) as date')
            // ->selectRaw('SUM(total_amount) as revenue')
            ->selectRaw('SUM(total) as revenue')
            ->selectRaw('COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Ambil data penjualan mingguan
     */
    protected function getWeeklySalesData(): Collection
    {
        return Order::query()
            ->where('created_at', '>=', now()->subDays($this->historicalDays))
            ->where('status', 'completed')
            ->selectRaw('YEARWEEK(created_at, 1) as year_week')
            ->selectRaw('MIN(DATE(created_at)) as week_start')
            // ->selectRaw('SUM(total_amount) as revenue')
            ->selectRaw('SUM(total) as revenue')
            ->selectRaw('COUNT(*) as transactions')
            ->groupBy('year_week')
            ->orderBy('year_week')
            ->get();
    }

    /**
     * Ambil data penjualan bulanan
     */
    protected function getMonthlySalesData(): Collection
    {
        return Order::query()
            ->where('created_at', '>=', now()->subMonths(12))
            ->where('status', 'completed')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            // ->selectRaw('SUM(total_amount) as revenue')
            ->selectRaw('SUM(total) as revenue')
            ->selectRaw('COUNT(*) as transactions')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Hitung linear trend menggunakan least squares
     */
    protected function calculateLinearTrend(array $values): array
    {
        $n = count($values);
        if ($n < 2) return ['slope' => 0, 'intercept' => $values[0] ?? 0];

        $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $values[$i];
            $sumXY += $i * $values[$i];
            $sumX2 += $i * $i;
        }

        $denominator = ($n * $sumX2) - ($sumX * $sumX);
        
        if ($denominator == 0) {
            return ['slope' => 0, 'intercept' => $sumY / $n];
        }

        $slope = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
        $intercept = ($sumY - ($slope * $sumX)) / $n;

        return ['slope' => $slope, 'intercept' => $intercept];
    }

    /**
     * Hitung seasonality mingguan
     */
    protected function calculateWeeklySeasonality(Collection $dailyData): array
    {
        $dayTotals = array_fill(0, 7, []);
        
        foreach ($dailyData as $data) {
            $dayOfWeek = Carbon::parse($data->date)->dayOfWeek;
            $dayTotals[$dayOfWeek][] = $data->revenue;
        }

        $overallAvg = $dailyData->avg('revenue');
        $seasonality = [];

        for ($i = 0; $i < 7; $i++) {
            if (count($dayTotals[$i]) > 0) {
                $dayAvg = array_sum($dayTotals[$i]) / count($dayTotals[$i]);
                $seasonality[$i] = $overallAvg > 0 ? $dayAvg / $overallAvg : 1.0;
            } else {
                $seasonality[$i] = 1.0;
            }
        }

        return $seasonality;
    }

    /**
     * Simple Moving Average
     */
    protected function simpleMovingAverage(array $values, int $window = null): float
    {
        $window = $window ?? $this->movingAverageWindow;
        $recent = array_slice($values, -$window);
        
        return count($recent) > 0 ? array_sum($recent) / count($recent) : 0;
    }

    /**
     * Weighted Moving Average (lebih berat ke data terbaru)
     */
    protected function weightedMovingAverage(array $values, int $window = null): float
    {
        $window = $window ?? $this->movingAverageWindow;
        $recent = array_slice($values, -$window);
        $n = count($recent);
        
        if ($n === 0) return 0;

        $weightedSum = 0;
        $weightTotal = 0;

        for ($i = 0; $i < $n; $i++) {
            $weight = $i + 1; // Linear weight: 1, 2, 3, ...
            $weightedSum += $recent[$i] * $weight;
            $weightTotal += $weight;
        }

        return $weightedSum / $weightTotal;
    }

    /**
     * Hitung volatilitas (standard deviation)
     */
    protected function calculateVolatility(array $values): float
    {
        $n = count($values);
        if ($n < 2) return 0;

        $mean = array_sum($values) / $n;
        $squaredDiffs = array_map(fn($v) => pow($v - $mean, 2), $values);
        
        return sqrt(array_sum($squaredDiffs) / ($n - 1));
    }

    /**
     * Hitung confidence score berdasarkan jarak prediksi dan jumlah data
     */
    protected function calculateConfidenceScore(int $daysAhead, int $dataPoints): float
    {
        // Confidence menurun seiring jarak prediksi
        $distanceFactor = max(0.5, 1 - ($daysAhead * 0.02));
        
        // Confidence meningkat dengan lebih banyak data
        $dataFactor = min(1, $dataPoints / 90);
        
        return round($distanceFactor * $dataFactor, 4);
    }

    /**
     * Dapatkan akurasi prediksi vs aktual (untuk evaluasi)
     */
    public function evaluateAccuracy(string $periodType = 'daily', int $daysBack = 7): array
    {
        $forecasts = SalesForecast::where('period_type', $periodType)
            ->where('forecast_date', '>=', now()->subDays($daysBack))
            ->where('forecast_date', '<', now())
            ->get();

        if ($forecasts->isEmpty()) {
            return ['mape' => null, 'message' => 'Tidak ada data forecast untuk dievaluasi'];
        }

        $errors = [];

        foreach ($forecasts as $forecast) {
            $actual = Order::whereDate('created_at', $forecast->forecast_date)
                ->where('status', 'completed')
                // ->sum('total_amount');
                ->sum('total');

            if ($actual > 0) {
                $errors[] = abs($forecast->predicted_revenue - $actual) / $actual;
            }
        }

        if (empty($errors)) {
            return ['mape' => null, 'message' => 'Tidak ada data aktual untuk perbandingan'];
        }

        $mape = (array_sum($errors) / count($errors)) * 100;

        return [
            'mape' => round($mape, 2),
            'accuracy' => round(100 - $mape, 2),
            'samples' => count($errors),
            'interpretation' => $this->interpretMape($mape),
        ];
    }

    protected function interpretMape(float $mape): string
    {
        return match(true) {
            $mape < 10 => 'Sangat Akurat',
            $mape < 20 => 'Akurat',
            $mape < 30 => 'Cukup Akurat',
            $mape < 50 => 'Kurang Akurat',
            default => 'Perlu Perbaikan Model',
        };
    }
}