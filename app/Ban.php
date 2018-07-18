<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    protected $table = 'bans';
    public $timestamps = false;
    protected $dates = ['exp_date'];
}
