<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class RegrasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Ibbr\Regra::create([
            'descricao' => 'Não poste conteúdo ilegal'
        ]);
        \Ibbr\Regra::create([
            'descricao' => 'Postagens devem seguir o assunto da board'
        ]);
        \Ibbr\Regra::create([
            'descricao' => 'Respeite as regras locais de cada board'
        ]);
    }
}
