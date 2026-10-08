<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Employee extends Authenticatable implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'user_id',
        'name',
        'first_name',
        'last_name',
        'username',
        'designation',
        'about',
        'company',
        'pan_number',
        'employee_id',
        'phone',
        'email',
        'joining_date',
        'status',
        'department',
        'password',
        'profile_image',
        'assign_tour',
        'assign_customer',
        'profile_image_path',
        'staff_role', 'gender', 'birthday', 'address', 'passport_no', 'passport_exp_date', 'nationality', 'religion', 'marital_status', 'Employment_of_spouse', 'no_of_children',

    ];
    protected $hidden = [
        'password',
    ];
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'employee_permissions')->withTimestamps();
    }
    public function bankDetail()
    {
        return $this->hasOne(EmployeeBankDetail::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(Employeefamily::class, 'employee_id');
    }

    public function educations()
    {
        return $this->hasMany(EmployeeEducation::class);
    }
    public function experiences()
    {
        return $this->hasMany(EmployeeExperience::class);
    }
    public function emergencyContacts()
    {
        return $this->hasMany(EmployeeEmergencyContact::class);
    }
    public function bankStatutory()
    {
        return $this->hasOne(EmployeeBankStatutory::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
