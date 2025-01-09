<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{


    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        foreach ($this->admins() as $admin) {

            $email = $admin['email'];
            $user = User::where('email', $email)->firstOrNew();
            $user->forceFill(User::factory()->definition());

            $user->email = $email;
            $user->name = $admin['name'];
            $user->password = Hash::make($admin['password']);
            $user->email_verified_at = now();
            $user->save();

            $user->assignRole($adminRole);
        }
    }

    private function admins(): array
    {
        return [
            [
                'name' => 'Admin',
                'email' => 'admin@mail.ru',
                'password' => 'G@yrat1299'
            ]
        ];
    }
}
