<?php

namespace Database\Seeders;

use Ladmin\Engine\Models\Admin;
use Ladmin\Engine\Models\LadminRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /**
         * Create role
         */
        $role = LadminRole::first();
        if (!$role) {
            $role = LadminRole::create([
                'name' => 'Super Admin',
                'gates' => ladmin()->menu()->allGates()
            ]);
        }

        $this->command->line('');
        $this->command->info('# Login Accounts');
        $this->command->line('');
        $this->command->line('View complete account in `' . ladmin()->getAdminTable() . '` table in your database.');
        $this->command->line('');

        /**
         * Create dummy ladmin account 
         */
        Admin::factory(3)->create()
            ->each(function ($admin) use ($role) {
                $admin->roles()->sync([$role->id]);

                $this->command->line('--------------------------------------------');
                $this->command->info('email     : ' . $admin->email);
                $this->command->info('password  : password');
            });

        $this->call(RoleSeeder::class);
    }
}
