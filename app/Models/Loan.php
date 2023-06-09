<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVE = 'APPROVED';
    const STATUS_PAID = 'PAID';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'term',
        'amount',
        'status',
    ];

    /**
     * Get the schedule payment for the Loan.
     */
    public function schedulePayments()
    {
        return $this->hasMany(SchedulePayment::class);
    }
}
