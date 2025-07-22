<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAssetType extends Model
{
    use HasFactory;

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}
