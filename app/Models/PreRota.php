<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PreRota extends Model
{
    use SoftDeletes;
    use LogsActivity;
    protected $guarded=['id'];

    protected $fillable = [
        'branch_id',
        'start_date',
        'end_date',
        'type',
        'start_time',
        'end_time',
        'details'
    ];

    protected static $logName = 'pre_rota';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(array_diff(array_keys($this->getAttributes()), ['created_at', 'updated_at', 'deleted_at']))
            ->useLogName('pre_rota')
            ->setDescriptionForEvent(fn(string $eventName) => "Pre Rota record has been {$eventName}");
    }


    // public function employees()
    // {
    //     return $this->belongsToMany(Employee::class, 'employee_pre_rotas', 'pre_rota_id', 'employee_id');
    // }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_pre_rotas', 'pre_rota_id', 'employee_id')
            ->withPivot(['date', 'day_name', 'start_time', 'end_time', 'created_by'])
            ->withTimestamps()
            ->distinct(); // 🔍 This line removes duplicate employee records
    }

}
