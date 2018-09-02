<?php

namespace Ibbr;

use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    public $timestamps = false;
    protected $table = 'arquivos';
    protected $primaryKey = 'filename';
    public $incrementing = false;
    
    protected $fillable = [
        'filename', 'mime', 'spoiler', 'original_filename', 'filesize'
    ];
    
    public function post(){
        return $this->belongsTo('Ibbr\Post');
    }
}
