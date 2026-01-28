<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomInspectionItem extends Model
{
    use HasFactory;


    protected $fillable = ['room_inspection_id', 'checklist_item_id'];

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }

}
