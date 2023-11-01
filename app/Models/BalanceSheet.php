<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceSheet extends Model
{
    use HasFactory;

protected $casts=[
    'total_assets'=>'double',
    'total_equity'=>'double',
    'total_liabilities'=>'double',
    'total_working_capital'=>'double',
    'total_equity_liabilities'=>'double',
];
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('year', 'like', '%' . $search . '%');
            });
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function data()
    {
        return $this->hasMany(BalanceSheetData::class);
    }
}
