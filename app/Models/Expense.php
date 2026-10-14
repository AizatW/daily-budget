<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Expense extends Model
{
    protected $fillable = [
        'input_date',
        'descriptions',
        'amount',
        'type',
        'payment_type',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
