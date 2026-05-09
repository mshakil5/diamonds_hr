<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyPreRota extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'staff_id', 'branch_id', 'date', 'note', 'status', 'created_by', 'updated_by'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function details()
    {
        return $this->hasMany(DailyPreRotaDetail::class);
    }
}
