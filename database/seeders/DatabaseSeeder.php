<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            LanguageSeeder::class,
            CountrySeeder::class,
        ]);

        $email = env('SUPER_ADMIN_EMAIL', app()->environment('local') ? 'admin@visatko.local' : null);
        $password = env('SUPER_ADMIN_PASSWORD', app()->environment('local') ? 'ChangeMe123!' : null);

        if ($email && $password) {
            $user = User::query()->updateOrCreate(['email' => $email], [
                'name' => 'Visatko Super Admin',
                'password' => $password,
                'is_active' => true,
            ]);
            $user->assignRole('Super Admin');
        }
    }
}
