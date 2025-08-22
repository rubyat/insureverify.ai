<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(\Laravel\Cashier\Subscription::class);
    }

    /**
     * Get the Plan model corresponding to the user's active subscription price.
     */
    public function currentPlan(): ?Plan
    {
        $subscription = $this->subscription('default');
        if (! $subscription) {
            return null;
        }
        if (! empty($subscription->plan_id)) {
            return Plan::find($subscription->plan_id);
        }
        if ($subscription->stripe_price) {
            return Plan::where('stripe_plan_id', $subscription->stripe_price)->first();
        }
        return null;
    }
}
