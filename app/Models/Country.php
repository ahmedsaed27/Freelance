<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $connection = 'user_db';

    protected $table = 'countries';

    protected $fillable = ['name','iso2' , 'iso3'];

    public $timestamps = true;

    public function city(){
        return $this->hasMany(Cities::class , 'countries_id');
    }
}
