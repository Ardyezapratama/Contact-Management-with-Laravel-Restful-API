<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'ezapratama')->first();
        Contact::create([
            'first_name' => 'Eza',
            'last_name' => 'Pratama',
            'email' => 'eza@gmail.com',
            'phone' => '083678493784',
            'user_id' => $user->id
        ]);
    }
}
