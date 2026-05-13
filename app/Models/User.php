<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Review;

use App\Notifications\NewOrderReceived;
use App\Notifications\ProductLowStock;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements HasLocalePreference
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'preferred_locale',
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getIsAdminAttribute(): bool
    {
        // FIXED: simple helper used by broadcast channel auth.
        return $this->role === 'admin';
    }

    public function preferredLocale(): string
    {
        return $this->preferred_locale ?: config('app.locale');
    }
    
public function routeNotificationForSlack($notification = null): ?string
{
    // Step 1: Method call થઈ કે નહીં તે confirm કરો
    Log::info('routeNotificationForSlack called', [
        'user_id'           => $this->id,
        'role'              => $this->role,
        'notification_type' => $notification ? get_class($notification) : 'null',
    ]);

    // Step 2: Role check fail થાય છે કે નહીં
    if ($this->role !== 'admin') {
        Log::warning('routeNotificationForSlack: user is not admin, returning null', [
            'user_id' => $this->id,
            'role'    => $this->role,
        ]);
        return null;
    }

    $channel = match (true) {
        $notification instanceof NewOrderReceived =>
            config('services.slack.channels.orders'),

        $notification instanceof ProductLowStock =>
            config('services.slack.channels.alerts'),

        default =>
            config('services.slack.channels.default'),
    };

    // Step 3: Config માંથી actually URL મળ્યો કે null આવ્યો
    Log::info('routeNotificationForSlack: resolved channel URL', [
        'channel_url' => $channel ?? 'NULL - config key missing!',
    ]);

    return $channel;
}

}
