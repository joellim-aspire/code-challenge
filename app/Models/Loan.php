<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'loan_term',
        'loan_term_remaining',
        'amount_required',
        'amount_balance',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function repayment() {
        return $this->hasMany(Repayment::class);
    }

    public function scopeBelongs_to($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public $timestamps = false;
}
