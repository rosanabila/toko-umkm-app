<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService
{
    /**
     * Register a new user and automatically create a store profile if their role is seller.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'phone' => $data['phone'] ?? null,
            ]);

            if ($user->isPenjual()) {
                // Automatically create a dummy store profile that they must complete later
                $user->store()->create([
                    'name' => 'Toko ' . $user->name,
                    'slug' => 'toko-' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $user->name)),
                    'description' => 'Profil toko baru Anda. Edit profil untuk mengubah deskripsi.',
                ]);
            }

            return $user;
        });
    }
}
