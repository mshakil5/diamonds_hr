<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreRota extends Model
{
    use SoftDeletes;
    protected $guarded=['id'];

    public function employees(){
        return $this->belongsToMany(Employee::class);
    }
}
