<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::query()->firstOrCreate(['name' => 'Business Demo 1']);
        Tenant::query()->firstOrCreate(['name' => 'Business Demo 2']);
    }
}
