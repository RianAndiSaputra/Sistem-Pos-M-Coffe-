<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'outlet_id',
        'user_id',
        'shift_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'total_paid',
        'change',
        'payment_method',
        'status',
        'notes',
        'member_id'
    ];

    public function scopeMonthlyTotal($query, $month = null, $outletId = null)
    {
        $date = $month ? Carbon::parse($month) : Carbon::now();

        $query = $query->whereBetween('created_at', [
            $date->startOfMonth()->toDateTimeString(),
            $date->endOfMonth()->toDateTimeString()
        ]);

        $query->where('status', 'completed');

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        // if ($outletId) {
        //     $query->where('outlet_id', $outletId);
        // }
        //outlet

        return $query->sum('total');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function inventoryHistory()
    {
        return $this->hasMany(InventoryHistory::class);
    }

    public function rawMaterialHistories()
    {
        return $this->hasMany(RawMaterialStockHistory::class);
    }
}
