<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLogin extends Model
{
    protected $fillable = [
        'user_id',
        'login_type',
        'ip_address',
        'user_agent',
        'logged_in_at',
        'logged_out_at',
        'session_id',
    ];

    protected $casts = [
        'logged_in_at' => 'datetime',
        'logged_out_at' => 'datetime',
    ];

    /**
     * Get the user that owns the login.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
