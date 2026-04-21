<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','is_admin','profile_photo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function reseps() {
        return $this->hasMany(Resep::class);
    }

    public function favorites() {
        return $this->hasMany(Favorite::class);
    }

    public function feedbacks() {
        return $this->hasMany(Feedback::class);
    }

    public function fridge() {
        return $this->hasMany(UserFridge::class);
    }

    public function mealPlanners() {
        return $this->hasMany(MealPlanner::class);
    }

    public function following() {
        return $this->hasMany(UserFollow::class, 'user_id');
    }

    public function followers() {
        return $this->hasMany(UserFollow::class, 'to_user_id');
    }
}