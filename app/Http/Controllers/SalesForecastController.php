<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SalesForecast;
use App\Services\SalesForecastingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesForecastController extends Controller
{
    public function __construct(
        protected SalesForecastingService $forecastService
    ) {}

    /**
     * GET /api/forecasts
     * Ambil semua forecast dengan filter
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'period_type' => 'nullable|in:daily,weekly,monthly',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $query = SalesForecast::query()
            ->when($request->period_type, fn($q, $type) => $q->where('period_type', $type))
            ->when($request->start_date, fn($q, $date) => $q->where('forecast_date', '>=', $date))
            ->when($request->end_date, fn($q, $date) => $q->where('forecast_date', '<=', $date))
            ->orderBy('forecast_date');

        $forecasts = $request->limit 
            ? $query->limit($request->limit)->get() 
            : $query->get();

        return response()->json([
            'success' => true,
            'data' => $forecasts,
            'meta' => [
                'total' => $forecasts->count(),
                'period_type' => $request->period_type ?? 'all',
            ],
        ]);
    }

    /**
     * GET /api/forecasts/daily
     * Prediksi harian untuk N hari ke depan
     */
    public function daily(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'nullable|integer|min:1|max:90',
        ]);

        $days = $request->days ?? 30;

        $forecasts = SalesForecast::daily()
            ->where('forecast_date', '>=', now())
            ->where('forecast_date', '<=', now()->addDays($days))
            ->orderBy('forecast_date')
            ->get();

        // Tambahkan data historis untuk perbandingan
        $historical = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date')
            // ->selectRaw('SUM(total_amount) as revenue')
            ->selectRaw('SUM(total) as revenue')
            ->selectRaw('COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'forecasts' => $forecasts,
                'historical' => $historical,
                'summary' => [
                    'total_predicted_revenue' => $forecasts->sum('predicted_revenue'),
                    'avg_daily_revenue' => $forecasts->avg('predicted_revenue'),
                    'total_predicted_transactions' => $forecasts->sum('predicted_transactions'),
                    'forecast_period' => [
                        'start' => $forecasts->first()?->forecast_date,
                        'end' => $forecasts->last()?->forecast_date,
                    ],
                ],
            ],
        ]);
    }

    /**
     * GET /api/forecasts/weekly
     */
    public function weekly(Request $request): JsonResponse
    {
        $request->validate([
            'weeks' => 'nullable|integer|min:1|max:12',
        ]);

        $weeks = $request->weeks ?? 4;

        $forecasts = SalesForecast::weekly()
            ->where('forecast_date', '>=', now()->startOfWeek())
            ->orderBy('forecast_date')
            ->limit($weeks)
            ->get();

        $historical = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subWeeks(8))
            ->selectRaw('YEARWEEK(created_at, 1) as year_week')
            ->selectRaw('MIN(DATE(created_at)) as week_start')
            // ->selectRaw('SUM(total_amount) as revenue')
            ->selectRaw('SUM(total) as revenue')
            ->selectRaw('COUNT(*) as transactions')
            ->groupBy('year_week')
            ->orderBy('year_week')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'forecasts' => $forecasts,
                'historical' => $historical,
                'summary' => [
                    'total_predicted_revenue' => $forecasts->sum('predicted_revenue'),
                    'avg_weekly_revenue' => $forecasts->avg('predicted_revenue'),
                ],
            ],
        ]);
    }

    /**
     * GET /api/forecasts/monthly
     */
    public function monthly(Request $request): JsonResponse
    {
        $request->validate([
            'months' => 'nullable|integer|min:1|max:12',
        ]);

        $months = $request->months ?? 3;

        $forecasts = SalesForecast::monthly()
            ->where('forecast_date', '>=', now()->startOfMonth())
            ->orderBy('forecast_date')
            ->limit($months)
            ->get();

        $historical = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-01") as month_start')
            // ->selectRaw('SUM(total_amount) as revenue')
            ->selectRaw('SUM(total) as revenue')
            ->selectRaw('COUNT(*) as transactions')
            ->groupBy('month', 'month_start')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'forecasts' => $forecasts,
                'historical' => $historical,
                'summary' => [
                    'total_predicted_revenue' => $forecasts->sum('predicted_revenue'),
                    'avg_monthly_revenue' => $forecasts->avg('predicted_revenue'),
                ],
            ],
        ]);
    }

    /**
     * POST /api/forecasts/generate
     * Generate atau refresh forecast
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'period_type' => 'nullable|in:daily,weekly,monthly,all',
            'historical_days' => 'nullable|integer|min:30|max:365',
            'forecast_days' => 'nullable|integer|min:7|max:90',
        ]);

        try {
            $service = new SalesForecastingService(
                $request->historical_days,
                $request->forecast_days
            );

            $periodType = $request->period_type ?? 'all';

            $results = match($periodType) {
                'daily' => ['daily' => $service->generateDailyForecast()],
                'weekly' => ['weekly' => $service->generateWeeklyForecast()],
                'monthly' => ['monthly' => $service->generateMonthlyForecast()],
                'all' => $service->generateAllForecasts(),
            };

            return response()->json([
                'success' => true,
                'message' => 'Forecast berhasil di-generate',
                'data' => [
                    'generated_counts' => collect($results)->map->count(),
                    'period_type' => $periodType,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * GET /api/forecasts/accuracy
     * Evaluasi akurasi model
     */
    public function accuracy(Request $request): JsonResponse
    {
        $request->validate([
            'period_type' => 'nullable|in:daily,weekly,monthly',
            'days_back' => 'nullable|integer|min:1|max:30',
        ]);

        $evaluation = $this->forecastService->evaluateAccuracy(
            $request->period_type ?? 'daily',
            $request->days_back ?? 7
        );

        return response()->json([
            'success' => true,
            'data' => $evaluation,
        ]);
    }

    /**
     * GET /api/forecasts/dashboard
     * Data lengkap untuk dashboard
     */
    public function dashboard(): JsonResponse
    {
        // Forecast
        $dailyForecasts = SalesForecast::daily()
            ->where('forecast_date', '>=', now())
            ->where('forecast_date', '<=', now()->addDays(14))
            ->orderBy('forecast_date')
            ->get();

        $weeklyForecasts = SalesForecast::weekly()
            ->where('forecast_date', '>=', now()->startOfWeek())
            ->limit(4)
            ->get();

        $monthlyForecasts = SalesForecast::monthly()
            ->where('forecast_date', '>=', now()->startOfMonth())
            ->limit(3)
            ->get();

        // Historical (30 hari terakhir)
        $historical = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date')
            // ->selectRaw('SUM(total_amount) as revenue')
            ->selectRaw('SUM(total) as revenue')
            ->selectRaw('COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Today's actual vs predicted
        $todayActual = Order::whereDate('created_at', now())
            ->where('status', 'completed')
            // ->selectRaw('SUM(total_amount) as revenue, COUNT(*) as transactions')
            ->selectRaw('SUM(total) as revenue, COUNT(*) as transactions')
            ->first();

        $todayForecast = SalesForecast::daily()
            ->whereDate('forecast_date', now())
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'forecasts' => [
                    'daily' => $dailyForecasts,
                    'weekly' => $weeklyForecasts,
                    'monthly' => $monthlyForecasts,
                ],
                'historical' => $historical,
                'today' => [
                    'actual' => $todayActual,
                    'predicted' => $todayForecast,
                    'variance' => $todayForecast && $todayActual?->revenue
                        ? round((($todayActual->revenue - $todayForecast->predicted_revenue) / $todayForecast->predicted_revenue) * 100, 2)
                        : null,
                ],
                'insights' => $this->generateInsights($dailyForecasts, $historical),
            ],
        ]);
    }

    /**
     * Generate insights dari data
     */
    protected function generateInsights($forecasts, $historical): array
    {
        $insights = [];

        if ($forecasts->isNotEmpty() && $historical->isNotEmpty()) {
            $avgHistorical = $historical->avg('revenue');
            $avgForecast = $forecasts->avg('predicted_revenue');

            $trend = (($avgForecast - $avgHistorical) / $avgHistorical) * 100;

            if ($trend > 5) {
                $insights[] = [
                    'type' => 'positive',
                    'message' => sprintf('Prediksi menunjukkan tren naik %.1f%% dibanding rata-rata historis', $trend),
                ];
            } elseif ($trend < -5) {
                $insights[] = [
                    'type' => 'warning',
                    'message' => sprintf('Prediksi menunjukkan tren turun %.1f%% dibanding rata-rata historis', abs($trend)),
                ];
            }

            // Peak day prediction
            $peakDay = $forecasts->sortByDesc('predicted_revenue')->first();
            if ($peakDay) {
                $insights[] = [
                    'type' => 'info',
                    'message' => sprintf(
                        'Hari dengan prediksi penjualan tertinggi: %s (Rp %s)',
                        $peakDay->forecast_date->locale('id')->translatedFormat('l, d F'),
                        number_format($peakDay->predicted_revenue, 0, ',', '.')
                    ),
                ];
            }
        }

        return $insights;
    }
}