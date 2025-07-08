<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EmployeePreRota extends Model
{
    use HasFactory;
    use LogsActivity;
    protected $table = 'employee_pre_rota';
    protected $fillable = ['employee_id', 'branch_id', 'pre_rota_id'];
    public $timestamps = true;

    protected static $logName = 'employee_pre_rota';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(array_diff(array_keys($this->getAttributes()), ['created_at', 'updated_at']))
            ->useLogName('employee_pre_rota')
            ->setDescriptionForEvent(fn(string $eventName) => "Employee Pre-Rota record has been {$eventName}");
    }
}
