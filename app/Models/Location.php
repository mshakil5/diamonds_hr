<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

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
