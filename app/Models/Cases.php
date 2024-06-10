<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Cases extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $table = 'cases';

    protected $connection = 'mysql';


    protected $fillable = [
        'user_id' ,
        'notes'  ,
        'is_visible',
        'message',
        'freelance_type',
        'country',
        'cities_id',
        'notes',
        'message',
        'title',
    ];

    public $timestamps = true;


    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function receive(){
        return $this->belongsToMany(User::class , 'cases_users' , 'cases_id' , 'user_id')
        ->withTimestamps();
    }

    public function city(){
        return $this->belongsTo(Cities::class , 'cities_id');
    }

}
