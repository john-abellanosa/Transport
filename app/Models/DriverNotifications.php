<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverNotifications extends Model
{
    use HasFactory;

    protected $table = 'driver_notifications';  
    
    protected $fillable = [
        'driver_name',
        'title',
        'message',
        'status',
        'created_at',
    ];
 
    public $timestamps = false;
}
