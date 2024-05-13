<?php

namespace App\Models;

use App\Models\Branch;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanApplication extends Model
{
    use LogsActivity, HasFactory;

    protected $casts = [

    ];


    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(LoanProduct::class, 'loan_product_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class);
    }

    public function scores()
    {
        return $this->hasMany(LoanApplicationScore::class, 'loan_application_id');
    }

    public function linkedStages()
    {
        return $this->hasMany(LoanApplicationLinkedApprovalStage::class, 'loan_application_id');
    }

    public function currentLinkedStage()
    {
        return $this->belongsTo(LoanApplicationLinkedApprovalStage::class, 'current_loan_application_approval_stage_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        });
        $query->when($filters['loan_product_id'] ?? null, function ($query, $loan_category_id) {
            $query->where('loan_product_id', $loan_category_id);
        });
        $query->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        });
        $query->when($filters['staff_id'] ?? null, function ($query, $staff_id) {
            $query->where('staff_id', $staff_id);
        });
        $query->when($filters['branch_id'] ?? null, function ($query, $branch_id) {
            $query->where('branch_id', $branch_id);
        });
        $query->when($filters['province_id'] ?? null, function ($query, $province_id) {
            $query->where('province_id', $province_id);
        });
        $query->when($filters['district_id'] ?? null, function ($query, $district_id) {
            $query->where('district_id', $district_id);
        });
        $query->when($filters['ward_id'] ?? null, function ($query, $ward_id) {
            $query->where('ward_id', $ward_id);
        });
        $query->when($filters['village_id'] ?? null, function ($query, $village_id) {
            $query->where('village_id', $village_id);
        });
        $query->when($filters['date_range'] ?? null, function ($query, $search) {
            $date = explode(' to ', $search);
            if (!empty($date[1])) {
                $query->whereBetween('date', [$date[0], $date[1]]);
            }
        });
    }

    public function scopeStaff($query, $staffID)
    {
        $query->when($staffID, function ($query, $staffID) {
            $query->where('staff_id', $staffID);
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
