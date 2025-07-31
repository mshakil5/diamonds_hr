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

    public function preRotas()
    {
        return $this->belongsToMany(PreRota::class, 'employee_pre_rotas', 'employee_id', 'pre_rota_id');
    }
    
    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getLeaveStatusCountsAttribute_old()
    {
        $holidays = $this->holidays()->get();

        $durations = $holidays->groupBy('status')->map(function ($group) {
            return $group->sum(function ($holiday) {
                $start = Carbon::parse($holiday->from_date);
                $end = Carbon::parse($holiday->to_date);
                return $start->diffInDays($end) + 1;
            });
        });

        return [
            'booked' => $durations['Booked'] ?? 0,
            'not_taken' => $durations['Not Taken'] ?? 0,
            'taken' => $durations['Taken'] ?? 0,
        ];
    }

    public function getLeaveStatusCountsAttribute()
    {
        // Fetch holidays and related attendance data
        $holidays = $this->holidays()->get();
        $today = Carbon::today();

        $durations = ['booked' => 0, 'not_taken' => 0, 'taken' => 0];

        foreach ($holidays as $holiday) {
            $start = Carbon::parse($holiday->from_date);
            $end = Carbon::parse($holiday->to_date);
            $days = $start->diffInDays($end) + 1;

            $attendances = $this->attendances()
                ->whereBetween('clock_in', [$holiday->from_date, $holiday->to_date])
                ->get();

            for ($date = $start; $date <= $end; $date->addDay()) {

                $attendance = $attendances->firstWhere(function ($attendance) use ($date) {
                    return Carbon::parse($attendance->clock_in)->toDateString() === $date->toDateString();
                });

                if ($date->isFuture()) {
                    $durations['booked']++;
                } elseif ($attendance) {
                    $durations['not_taken']++;
                } else {
                    $durations['taken']++;
                }
            }
        }

        return [
            'booked' => $durations['booked'],
            'not_taken' => $durations['not_taken'],
            'taken' => $durations['taken'],
        ];
    }

}
