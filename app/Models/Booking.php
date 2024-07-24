<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'booking';

    protected $fillable = ['date' ,'time' , 'description' , 'status' , 'user_id'];

    public $timestamps = true;


    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

}
