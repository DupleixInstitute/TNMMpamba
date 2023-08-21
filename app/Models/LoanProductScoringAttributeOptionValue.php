<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProductScoringAttributeOptionValue extends Model
{
    use HasFactory;

    protected $casts = [
    ];

    public function member()
    {
        return $this->belongsTo(Client::class, 'member_id', 'id');
    }

    public function loan()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_id', 'id');
    }

}
