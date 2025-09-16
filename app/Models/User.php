<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function userStatus()
    {
        return $this->belongsTo(UserStatus::class, 'user_status_id');
    }
    
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function userNotes()
    {
        return $this->hasMany(UserNote::class);
    }

    public function notesAdded()
    {
        return $this->hasMany(UserNote::class, 'added_by');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')->withPivot('is_main');
    }

    /**
     * The permissions that are directly assigned to the user.
     */
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    /* Check if the user has a specific role. */
    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    /* Check if the user has a specific permission. */
    public function hasPermission(string $permissionName): bool
    {
        // 1. Admins have all permissions.
        //if ($this->isAdmin()) {
        //    return true;
       // }

        // 2. Check for the permission directly in the user_permissions table.
        // This is now the primary check for all user-specific permissions.
        return $this->directPermissions()->where('name', $permissionName)->exists();
    }

     /**
     * Helper method to check if the user has the 'admin' role.
     */
    public function isAdmin(): bool
    {
        return $this->roles()->where('name', 'admin')->exists();
    }

    public function mainRoleRelation()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->wherePivot('is_main', true);
    }

    public function getMainRoleAttribute()
    {
        return $this->roles->firstWhere('pivot.is_main', true);
    }

    public function setMainRole(Role $role)
    {
        // Remove existing is_main flag from all roles
        $this->roles()->updateExistingPivot(
            $this->roles->pluck('id'), ['is_main' => false]
        );

        // Set is_main = true for the selected role
        $this->roles()->updateExistingPivot($role->id, ['is_main' => true]);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(UserSocialPlatform::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'instructor_id');
    }

    public function awardingBodyRegistrations()
    {
        return $this->hasMany(AwardingBodyRegistration::class);
    }

    public function agentProfile()
    {
        return $this->hasOne(AgentProfile::class);
    }

    public function referredStudents()
    {
        return $this->hasMany(User::class, 'agent_id');
    }

    // users who has assigned to an agent
    public function assignedStudents()
    {
        return $this->hasMany(User::class, 'agent_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'user_id');
    }

    // Total commission earned from assigned students
    public function getTotalCommissionAttribute()
    {
        return $this->assignedStudents->flatMap->commissions->sum('amount');
    }

    // which recruitments a user has called
    public function studentRecruitmentsCalled()
    {
        return $this->hasMany(Recruitment::class, 'called_by');
    }

    public function graduates()
    {
        return $this->hasMany(Graduate::class);
    }

    






    /**
     * Get the sales person associated with the user.
     * These define the relationship where this user belongs to another user (acting as a sales person or agent). 
     * The second argument ('sales_person_id') is crucial because Laravel might guess sales_person_user_id by default if you don't specify the foreign key.
    */
    public function salesPerson()
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }





}
