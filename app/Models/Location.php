<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function flooor()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

  protected static function boot()
  {
    parent::boot();

    static::deleting(function ($model) {
      if (auth()->check()) {
        $model->deleted_by = auth()->id();
        $model->save();
      }
    });
  }
}
