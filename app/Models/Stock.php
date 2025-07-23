<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    public function stockAssetTypes()
    {
        return $this->hasMany(StockAssetType::class);
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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
