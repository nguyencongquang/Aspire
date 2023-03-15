<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulePayment extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_id',
        'due_date',
        'amount',
        'customer_input_amount',
        'status',
        'created_at',
        'updated_at',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
