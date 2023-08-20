<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'hanasa')->first();
        Contact::create([
            'first_name' => 'maya',
            'last_name'  => 'm',
            'email'      => 'maya@mail.com',
            'phone'      => '111111',
            'user_id'    => $user->id
        ]);
        Contact::create([
            'first_name' => 'hanasa',
            'last_name'  => 'h',
            'email'      => 'hanasa@mail.com',
            'phone'      => '111111',
            'user_id'    => $user->id
        ]);
    }
}
