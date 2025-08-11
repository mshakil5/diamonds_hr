<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class EmployeePreRota extends Model
{
    use HasFactory;
    use LogsActivity;
    protected $table = 'employee_pre_rotas';
    protected $fillable = ['employee_id', 'branch_id', 'pre_rota_id','date','day_name', 'start_time', 'end_time','status','created_by'];
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

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function holiday()
    {
        return $this->belongsTo(Holiday::class);
    }

    public function getHolidayStatusLabelAttribute()
    {

        $date = Carbon::parse($this->date);
        $today = Carbon::today();

        if ($date->gt($today)) {
            return 'Booked';
        }

        // Check for attendance on this date
        $attendance = $this->employee && $this->employee->attendances()
            ? $this->employee->attendances()->whereDate('clock_in', $date->toDateString())->first()
            : null;


        // If attendance exists, status is Cancel
        if ($attendance) {
            return 'Cancel';
        }

        // Past date with no attendance is Taken
        return 'Taken';
    }


}
