<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Cases extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $table = 'cases';

    protected $fillable = ['user_id' , 'notes' , 'message'];

    public $timestamps = true;


    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function receive(){
        return $this->belongsToMany(User::class , 'cases_users' , 'cases_id' , 'user_id')
        ->withTimestamps();
    }

}
