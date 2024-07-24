<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class CasesProfile extends Pivot
{
    use HasFactory , SoftDeletes;

    protected $table = 'case_profile';

    protected $connection = 'mysql';

    protected $fillable = ['profile_id' , 'case_id' , 'suggested_rate' , 'description' , 'status' , 'estimation_time' , 'currency_id'];

    public $timestamps = true;

    public function profile(){
        return $this->belongsTo(Profiles::class , 'profile_id');
    }

    public function cases(){
        return $this->belongsTo(Cases::class , 'case_id');
    }

    public function currency(){
        return $this->belongsTo(Currency::class , 'currency_id');
    }
}
