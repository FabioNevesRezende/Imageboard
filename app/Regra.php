<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Regra extends Model
{
    protected $table = 'regras';
    public $timestamps = false;
    
    public function board()
    {
        return $this->hasOne('Ibbr\Board', 'sigla','board_name');
    }
}
