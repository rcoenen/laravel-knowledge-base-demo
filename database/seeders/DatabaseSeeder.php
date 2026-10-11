<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * The demo staff account, then the Fernleaf help center content (authored
     * by that account).
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => config('demo.email')],
            ['name' => config('demo.name'), 'password' => config('demo.password')],
        );

        $this->call(FernleafSeeder::class);
    }
}
