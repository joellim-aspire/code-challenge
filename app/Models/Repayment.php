<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_id',
        'amount',
    ];

    public function loan() {
        return $this->belongsTo(Loan::class);
    }

    public function scopeBelongs_to($query, $loan_id)
    {
        return $query->where('loan_id', $loan_id);
    }


    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public $timestamps = false;
}
