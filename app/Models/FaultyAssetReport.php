<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaultyAssetReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function stockAssetType()
    {
        return $this->belongsTo(StockAssetType::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
