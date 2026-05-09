<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyPreRotaDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'daily_pre_rota_id', 'staff_id', 'branch_id', 'date', 'time_range', 'note', 'status', 'created_by', 'updated_by'
    ];

    public function rota()
    {
        return $this->belongsTo(DailyPreRota::class, 'daily_pre_rota_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


}
