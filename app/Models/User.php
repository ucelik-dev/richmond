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

    public function college()
    { 
        return $this->belongsTo(College::class); 
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

    /* Check if the user has a specific role. */
    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
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

    public function agentCommissions()
    {
        // Student -> Payments -> Commissions (from any agent)
        return $this->hasManyThrough(
            Commission::class,   // far model
            Payment::class,      // through model
            'user_id',           // FK on payments -> users.id
            'payment_id',        // FK on commissions -> payments.id
            'id',                // local key on users
            'id'                 // local key on payments
        );
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

    public function scopeStudentsOnly($q) {
        return $q->whereHas('roles', fn($q)=>$q->where('name','student'))->whereDoesntHave('roles', fn($q)=>$q->whereIn('name',['admin','manager']));
    }

    public function discountCoupons() { return $this->hasMany(DiscountCoupon::class, 'agent_id'); }






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














    /**
     * The permissions that are directly assigned to the user.
     */
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withPivot(['can_view','can_create','can_edit','can_delete'])
            ->withTimestamps();
    }

    /**
     * New: check a resource + ability, e.g. ('admin_payments','view')
     */
    public function canResource(string $resourceKey, string $ability): bool
    {
        $ability = $this->normalizeAbility($ability);             // view|create|edit|delete|export
        $permId  = Permission::where('name', $resourceKey)->value('id');
        if (!$permId) return false;

        $col = 'can_'.$ability;

        return $this->directPermissions()
            ->where('permissions.id', $permId)
            ->wherePivot($col, true)
            ->exists();
    }

    /* Check if the user has a specific permission. */
    public function hasPermission(string $permissionName): bool
    {
        [$ability, $resource] = $this->parsePermissionString($permissionName);
        if (!$ability || !$resource) return false;

        return $this->canResource($resource, $ability);
    }

    /* ---------- helpers ---------- */

    private function parsePermissionString(string $str): array
    {
        $str = trim($str);

        // new style: "resource,ability" or "resource:ability"
        if (str_contains($str, ',')) {
            [$resource, $ability] = array_map('trim', explode(',', $str, 2));
            return [$this->normalizeAbility($ability), strtolower($resource)];
        }
        if (str_contains($str, ':')) {
            [$resource, $ability] = array_map('trim', explode(':', $str, 2));
            return [$this->normalizeAbility($ability), strtolower($resource)];
        }

        // legacy: "ability_resource"
        if (preg_match('/^(view|create|edit|delete|export|index|show|store|update|destroy|sidebar|manage)_(.+)$/i', $str, $m)) {
            return [$this->normalizeAbility($m[1]), strtolower($m[2])];
        }

        return [null, null];
    }

    private function normalizeAbility(string $a): string
    {
        return match (strtolower(trim($a))) {
            'index','show','sidebar' => 'view',
            'store','create'         => 'create',
            'update','edit','manage' => 'edit',
            'destroy','delete'       => 'delete',
            default                  => $a,
        };
    }





}
