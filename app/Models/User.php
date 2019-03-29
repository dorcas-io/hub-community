<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $casts = [
        'is_verified' => 'boolean'
    ];

    protected $fillable = [
        'uuid',
        'company_id',
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'remember_token',
        'gender',
        'photo_url',
        'is_verified',
    ];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Adds a 'name' attribute on the model
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->attributes['firstname'] . ' ' . $this->attributes['lastname'];
    }

    /**
     * Returns the photo URL for this model
     *
     * @return string
     */
    public function getPhotoAttribute(): string
    {
        return (string) $this->attributes['photo_url'] ?? '';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}