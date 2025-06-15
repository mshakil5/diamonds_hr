<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreRota extends Model
{
    protected $guarded=['id'];

    public function employees(){
        return $this->belongsToMany(Employee::class);
    }
}
