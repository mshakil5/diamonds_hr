<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use SoftDeletes;
    use LogsActivity;
    protected $guarded = ['id'];

    protected static $logName = 'product';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(array_diff(array_keys($this->getAttributes()), ['created_at', 'updated_at', 'deleted_at']))
            ->useLogName('product')
            ->setDescriptionForEvent(fn(string $eventName) => "Product record has been {$eventName}");
    }
}
