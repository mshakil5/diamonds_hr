<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Carbon;

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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public function preRotas()
    {
        return $this->belongsToMany(PreRota::class, 'employee_pre_rotas', 'employee_id', 'pre_rota_id')
            ->withPivot('date', 'day_name', 'start_time', 'end_time', 'status');
    }

    public function getLeaveStatusCountsAttribute()
    {
        // Fetch EmployeePreRota records with status 3 (Holiday) for this employee
        $holidays = EmployeePreRota::where('employee_id', $this->id)
            ->where('status', '3')
            ->get();

        $today = Carbon::today();
        $durations = ['booked' => 0, 'not_taken' => 0, 'taken' => 0];

        // Debug: Log the holidays found
        \Log::debug('Employee ID: ' . $this->id . ', Holiday records found: ' . $holidays->count(), $holidays->toArray());

        foreach ($holidays as $holiday) {
            $date = Carbon::parse($holiday->date);

            // Debug: Log the date being processed
            \Log::debug('Processing holiday date: ' . $date->toDateString() . ', Is future: ' . ($date->isFuture() ? 'Yes' : 'No'));

            // Fetch attendance for the specific date
            $attendance = $this->attendances()
                ->whereDate('clock_in', $date->toDateString())
                ->first();

            if ($date->isFuture()) {
                $durations['booked']++;
            } elseif ($attendance) {
                $durations['not_taken']++;
                // Debug: Log attendance found
                \Log::debug('Attendance found for date: ' . $date->toDateString(), $attendance->toArray());
            } else {
                $durations['taken']++;
            }
        }

        // Debug: Log final counts
        \Log::debug('Leave status counts for Employee ID ' . $this->id . ': ', $durations);

        return [
            'booked' => $durations['booked'],
            'not_taken' => $durations['not_taken'],
            'taken' => $durations['taken'],
        ];
    }

}
