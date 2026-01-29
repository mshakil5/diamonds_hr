<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomInspection extends Model
{
    use HasFactory;


    protected $fillable = ['user_id', 'employee_id', 'branch_id', 'floor_id', 'room', 'note', 'date'];

    // Relationship to the checked items
    public function items()
    {
        return $this->hasMany(RoomInspectionItem::class);
    }

    // Standard Relationships
    public function branch() { return $this->belongsTo(Branch::class); }
    public function floor() { return $this->belongsTo(Floor::class); }
    public function user() { return $this->belongsTo(User::class); }

    

    public function employee(){
        return $this->belongsTo(Employee::class);
    }


}
