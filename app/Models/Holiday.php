<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;
use App\Models\Attendance;

class Holiday extends Model
{
    use SoftDeletes;
    use LogsActivity;
    protected $guarded=['id'];

    protected static $logName = 'holiday';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(array_diff(array_keys($this->getAttributes()), ['created_at', 'updated_at', 'deleted_at']))
            ->useLogName('holiday')
            ->setDescriptionForEvent(fn(string $eventName) => "Holiday record has been {$eventName}");
    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function employeePreRota()
    {
        return $this->hasMany(EmployeePreRota::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getStatusAttribute()
    {
        $start = Carbon::parse($this->from_date);
        $end = Carbon::parse($this->to_date);
        $today = Carbon::today();

        // Generate date range
        $dates = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        // Get all attendance clock_in dates
        $attendances = Attendance::where('employee_id', $this->employee_id)
            ->whereDate('clock_in', '>=', $start)
            ->whereDate('clock_in', '<=', $end)
            ->pluck('clock_in')
            ->map(function($clockIn) {
                return Carbon::parse($clockIn)->format('Y-m-d');
            })
            ->toArray();

        if ($start->isFuture()) {
            return 'Booked';
        } elseif (!empty(array_intersect($dates, $attendances))) {
            return 'Not Taken';
        } else {
            return 'Taken';
        }
    }

    public function holidayDetail()
    {
        return $this->hasMany(HolidayDetail::class);
    }
}
