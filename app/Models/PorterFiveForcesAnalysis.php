<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PorterFiveForcesAnalysis extends Model
{
    use HasFactory;

    protected $table = 'porter_five_forces_analysis';

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('year', 'like', '%' . $search . '%');
            });
        });
    }

    public function chart()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function data()
    {
        return $this->hasMany(IncomeStatementData::class);
    }
}