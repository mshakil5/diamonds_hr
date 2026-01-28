<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'checklist_category_id', 'status', 'created_by'];


    public function category()
    {
        return $this->belongsTo(ChecklistCategory::class, 'checklist_category_id');
    }
}
