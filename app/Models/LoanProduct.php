<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    use HasFactory;

    protected $appends = [

    ];
    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }


    public function category()
    {
        return $this->belongsTo(LoanProductCategory::class, 'loan_product_category_id', 'id');
    }

    public function scoringAttributes()
    {
        return $this->hasMany(LoanProductScoringAttribute::class, 'loan_product_id', 'id')->orderBy('order_position');
    }

}
