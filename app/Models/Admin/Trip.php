<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'transactionId',
        'deliveryType',
        'vehicleType',
        'clientName',
        'clientNumber',
        'destination',
        'municipality',
        'company',
        'cost',
        'driver',
        'remarks',
        'schedule',
        'arrival_date',
        'status',
        'asssigned_date',
        'proof_photo',
        'proof_video',
    ];

    /**
     * Get all delivery attempts for this trip.
     */
    public function attempts()
    {
        return $this->hasMany(DeliveryAttempt::class, 'trip_id');
    }
}
