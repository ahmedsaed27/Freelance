<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CasesUsers extends Pivot
{
    use HasFactory;

    protected $table = 'cases_users';

    protected $fillable = ['user_id' , 'cases_id'];

    public $timestamps = true;

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function cases(){
        return $this->belongsTo(Cases::class , 'cases_id');
    }
}
