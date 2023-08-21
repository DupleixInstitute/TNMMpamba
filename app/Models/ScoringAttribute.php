<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoringAttribute extends Model
{
    use HasFactory;

    protected $appends = [

    ];
    protected $casts = [
        'required'=>'boolean',
        'active'=>'boolean',
        'used'=>'boolean',
    ];
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    public function loan()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_id');
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }


}
