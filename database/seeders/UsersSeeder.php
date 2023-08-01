<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        
        \Ibbr\User::create([
            'id' => '1',
            'name' => 'Master Adm',
            'email' => 'algum@email.com',
            'password' => '$2y$10$SVTK3/gdSA4fNNEmTHhDxe8zcOoZwuCkY0e9qgUvPR4NW8B.su9EG' // password 12345678
        ]);
        
        \Ibbr\User::create([
            'id' => '2',
            'name' => 'Other Adm',
            'email' => 'outro@email.com',
            'password' => '$2y$10$SVTK3/gdSA4fNNEmTHhDxe8zcOoZwuCkY0e9qgUvPR4NW8B.su9EG' // password 12345678
        ]);

    }
}
