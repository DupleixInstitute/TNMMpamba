<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProductScoringAttribute extends Model
{
    use HasFactory;

    protected $casts = [
        'accept_value'=>'array',
    ];

    public function attribute()
    {
        return $this->belongsTo(ScoringAttribute::class, 'scoring_attribute_id', 'id');
    }
    public function options()
    {
        return $this->hasMany(LoanProductScoringAttributeOptionValue::class, 'loan_product_scoring_attribute_id', 'id');
    }

    public function loan()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_id', 'id');
    }

}
