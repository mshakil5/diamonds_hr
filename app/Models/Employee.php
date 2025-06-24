<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $guarded=['id'];

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
