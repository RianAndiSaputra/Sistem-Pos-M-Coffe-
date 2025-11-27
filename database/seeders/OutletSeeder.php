<?php

namespace Database\Seeders;

use App\Models\CashRegister;
use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = [
            ['name' => 'Outlet 1', 'address' => 'Jl. Outlet 1', 'phone' => '081234567890', 'email' => 'outlet1@example.com', 'qris' => 'qris/qrs.png'],
            ['name' => 'Outlet 2', 'address' => 'Jl. Outlet 2', 'phone' => '081234567891', 'email' => 'outlet2@example.com', 'qris' => 'qris/qrs.png'],
            ['name' => 'Outlet 3', 'address' => 'Jl. Outlet 3', 'phone' => '081234567892', 'email' => 'outlet3@example.com', 'qris' => 'qris/qrs.png'],
            ['name' => 'Outlet 4', 'address' => 'Jl. Outlet 4', 'phone' => '081234567893', 'email' => 'outlet4@example.com', 'qris' => 'qris/qrs.png'],
            ['name' => 'Outlet 5', 'address' => 'Jl. Outlet 5', 'phone' => '081234567894', 'email' => 'outlet5@example.com', 'qris' => 'qris/qrs.png'],
        ];

        foreach ($outlets as $outlet) {
            $newOutlet = Outlet::create($outlet);

            CashRegister::create([
                'outlet_id' => $newOutlet->id,
                'balance' => 0,
                'is_active' => true,
            ]);
        }
    }
}
