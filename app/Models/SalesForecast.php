<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesForecast extends Model
{
    protected $fillable = [
        'forecast_date',
        'period_type',
        'predicted_revenue',
        'predicted_transactions',
        'lower_bound',
        'upper_bound',
        'confidence_score',
        'metadata',
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'predicted_revenue' => 'decimal:2',
        'predicted_transactions' => 'decimal:2',
        'lower_bound' => 'decimal:2',
        'upper_bound' => 'decimal:2',
        'confidence_score' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function scopeDaily($query)
    {
        return $query->where('period_type', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('period_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('period_type', 'monthly');
    }

    public function scopeUpcoming($query, int $days = 30)
    {
        return $query->whereBetween('forecast_date', [now(), now()->addDays($days)]);
    }
}
