<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyNotifications extends Model
{
    use HasFactory;

    protected $table = 'company_notifications';  
    
    protected $fillable = [
        'company_name',
        'title',
        'message',
        'status',
        'created_at',
    ];
 
    public $timestamps = false;
}
