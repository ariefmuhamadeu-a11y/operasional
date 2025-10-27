<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // code, name, role, payment_type, base_rate, active
            ['MYD', 'Mang Yadi', 'jahit', 'per_pcs', null, true],
            ['RDN', 'Jang Ridwan', 'jahit', 'per_pcs', null, true],
            ['MRF', 'Mang Arif', 'cutting', 'per_pcs', null, true],
            ['ANG', 'Angga', 'operasional', 'harian', null, true],
            ['BBI', 'Bibi', 'jahit', 'per_pcs', null, true],
        ];

        foreach ($rows as [$code, $name, $role, $payment, $base, $active]) {
            Employee::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'role' => $role,
                    'payment_type' => $payment,
                    'base_rate' => $base,
                    'active' => $active,
                ]
            );
        }
    }
}
