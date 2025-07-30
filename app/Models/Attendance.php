<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use App\Models\PreRota;

class Attendance extends Model
{
    use SoftDeletes;
    use LogsActivity;
    protected $guarded=['id'];

    protected static $logName = 'attendance';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(array_diff(array_keys($this->getAttributes()), ['created_at', 'updated_at']))
            ->useLogName('attendance')
            ->setDescriptionForEvent(fn(string $eventName) => "Attendance record has been {$eventName}");
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'branch_id' => auth()->user()->branch_id ?? null,
        ]);
    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function getLateAttribute()
    {
        if (!$this->clock_in) return '-';

        $clockIn = Carbon::parse($this->clock_in);

        $preRota = PreRota::whereHas('employees', function ($q) {
                $q->where('employee_pre_rotas.employee_id', $this->employee_id);
            })
            ->where('branch_id', $this->branch_id)
            ->whereDate('start_date', '<=', $clockIn)
            ->whereDate('end_date', '>=', $clockIn)
            ->first();

        $scheduledIn = Carbon::parse($clockIn)->setTimeFromTimeString($preRota->start_time ?? '09:00:00');

        return $clockIn->gt($scheduledIn)
            ? $clockIn->diff($scheduledIn)->format('%H:%I:%S')
            : 'On Time';
    }

    public function getEarlyLeaveAttribute()
    {
        if (!$this->clock_in || !$this->clock_out) return '-';

        $clockOut = Carbon::parse($this->clock_out);

        $preRota = PreRota::whereHas('employees', function ($q) {
                $q->where('employee_pre_rotas.employee_id', $this->employee_id);
            })
            ->where('branch_id', $this->branch_id)
            ->whereDate('start_date', '<=', $this->clock_in)
            ->whereDate('end_date', '>=', $this->clock_in)
            ->first();

        $scheduledOut = Carbon::parse($clockOut)->setTimeFromTimeString($preRota->end_time ?? '17:00:00');

        return $clockOut->lt($scheduledOut)
            ? $scheduledOut->diff($clockOut)->format('%H:%I:%S')
            : 'Correct Leave';
    }
}
