<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements MustVerifyEmail  , JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'user_db';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function profile(){
        return $this->hasOne(Profiles::class , 'user_id');
    }

    public function cases(){
        return $this->hasMany(Cases::class , 'user_id');
    }

    public function receive(){
        return $this->belongsToMany(Cases::class , 'cases_users' , 'user_id' , 'cases_id')
        ->withTimestamps();
    }

    public function verification(){
        return $this->hasOne(Verification::class , 'user_id');
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
