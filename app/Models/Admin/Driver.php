<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'number',
        'address',
        'status',
        'password',            
        'is_temporary_password',
        'otp_code',
        'otp_expires_at'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isOtpValid()
    {
        return $this->otp_code && $this->otp_expires_at && now()->lt($this->otp_expires_at);
    }
}