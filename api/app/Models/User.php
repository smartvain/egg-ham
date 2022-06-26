<?php

namespace App\Models;

use App\Notifications\ChangeEmailNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'provider_id',
        'provider_name',
        'theme'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function words()
    {
        return $this->hasMany(Word::class);
    }
    
    public function createUser($form)
    {
        return $this->create($form);
    }

    public function changeUserInfo($userId, $userInfo)
    {
        return $this->find($userId)->fill($userInfo)->save();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function changeEmailVerificationNotification($oldUserId)
    {
        $this->notify(new ChangeEmailNotification($oldUserId));
    }
}
