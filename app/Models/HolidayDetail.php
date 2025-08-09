<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolidayDetail extends Model
{
    use HasFactory;

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

}
