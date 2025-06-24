<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Employee extends Model
{
    use SoftDeletes;
    use LogsActivity;
    protected $guarded=['id'];

    protected static $logName = 'employee';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(array_diff(array_keys($this->getAttributes()), ['created_at', 'updated_at', 'deleted_at']))
            ->useLogName('employee')
            ->setDescriptionForEvent(fn(string $eventName) => "Employee record has been {$eventName}");
    }

    protected $fillable = [
        'name',
        'username',
        'user_id',
        'branch_id',
        'password',
        'join_date',
        'employee_id',
        'email',
        'phone',
        'emergency_contact_number',
        'emergency_contact_person',
        'ni',
        'tax_code',
        'nationality',
        'bank_details',
        'entitled_holiday',
        'address',
        'employee_type',
        'pay_rate',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
