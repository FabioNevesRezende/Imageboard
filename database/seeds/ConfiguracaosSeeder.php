<?php

use Illuminate\Database\Seeder;

class ConfiguracaosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ibbr\Configuracao::create([
            'id' => '1',
            'captchaativado' => 'n'
        ]);
    }
}
