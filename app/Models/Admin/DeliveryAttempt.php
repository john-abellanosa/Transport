<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Trip;

class DeliveryAttempt extends Model
{
    use HasFactory;

    protected $table = 'delivery_attempts';

    protected $fillable = [
        'trip_id',
        'attempt',
        'schedule_date',
        'date_status',
        'driver',
        'status',
        'remarks',
        'proof_photo', // ✅ Added this line
        'assigned_date',
    ];

    /**
     * Each delivery attempt belongs to a single trip.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}
