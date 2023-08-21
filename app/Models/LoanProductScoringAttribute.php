<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProductScoringAttribute extends Model
{
    use HasFactory;

    protected $casts = [
    ];

    public function attribute()
    {
        return $this->belongsTo(ScoringAttribute::class, 'scoring_attribute_id', 'id');
    }

    public function loan()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_id', 'id');
    }

}
