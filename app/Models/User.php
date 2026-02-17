<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Order;
use App\Models\UserDownload;
use App\Models\Subscription;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'role',
        'is_active',
        'avatar',
        'phone',
        'company',
        'job_title',
        'bio',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'language',
        'timezone',
        'marketing_emails',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'marketing_emails' => 'boolean',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() || $this->isManager() || $this->isEditor();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer' || is_null($this->role);
    }

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function downloads()
    {
        return $this->hasMany(UserDownload::class);
    }

   public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->whereNull('cancelled_at')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest();
    }

    
}