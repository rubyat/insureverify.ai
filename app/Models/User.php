<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * A user has many uploaded images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    /**
     * A user can have many saved cards.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(UserCard::class);
    }

    /**
     * A user has many subscriptions.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * We only support a single subscription, so the name is ignored.
     */
    public function subscription(string $name = 'default'): ?Subscription
    {
        return $this->activeSubscription();
    }

    /**
     * Compatibility helper: return true if user has an active or trialing subscription.
     */
    public function subscribed(string $name = 'default'): bool
    {
        return (bool) $this->activeSubscription();
    }

    /**
     * A user has many invoices through subscriptions.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Return the user's current active (or trialing) subscription.
     */
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->whereIn('status', ['active', 'trialing'])
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Get the Plan model for the user's active subscription.
     */
    public function currentPlan(): ?Plan
    {
        $subscription = $this->activeSubscription();
        return $subscription ? Plan::find($subscription->plan_id) : null;
    }
}
