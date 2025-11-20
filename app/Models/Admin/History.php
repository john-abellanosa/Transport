<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'transaction_id',
        'recipient',
        'client_number',
        'destination',
        'schedule',
        'delivery_type',
        'vehicle_type',
        'company',
        'distance',
        'cost',
        'status',
        'driver',
        'arrival',
        'remarks',
    ];
}
