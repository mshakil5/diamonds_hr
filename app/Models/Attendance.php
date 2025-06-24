<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;
    protected $guarded=['id'];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
