<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translators extends Model
{
    use HasFactory;

    protected $table = 'translators';

    protected $fillable = ['user_id' , 'cost'];

    protected $hidden = ['password'];

    public $timestamps = true;

    public function docs(){
        return $this->belongsToMany(Documents::class , 'docs_translators' , 'translators_id' , 'documents_id')
        ->withTimestamps();
    }
}
