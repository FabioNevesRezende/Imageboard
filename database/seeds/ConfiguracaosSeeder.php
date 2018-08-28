<?php

use Illuminate\Database\Seeder;

class ConfiguracaosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * 
     */
    public function run()
    {
        Ibbr\Configuracao::create([
            'id' => '1',
            'captcha_ativado' => false,
            'carteira_doacao' => 'endereco-carteira-monero',
            'biscoito_admin' => 'test',
            'tempero_biscoito' => 'tempero',
            'url_repo' => 'https://gitlab.com',
            'num_max_arq_post' => 5,
            'num_max_fios' => 100,
            'num_posts_paginacao' => 10,
            'num_max_posts_fio' => 500
        ]);
    }
}
