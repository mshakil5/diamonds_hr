<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
