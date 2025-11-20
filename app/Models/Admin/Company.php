<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'branch', 
        'email', 
        'address', 
        'owner', 
        'contact', 
        'municipality',
        'password',
        'cost',
        'is_temporary_password',
        'otp', 
        'otp_expires_at', 
    ];

    // Cast otp_expires_at to datetime
    protected $casts = [
        'otp_expires_at' => 'datetime',
    ];
}
