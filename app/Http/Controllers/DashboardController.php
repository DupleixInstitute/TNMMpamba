<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Event;
use App\Models\Vital;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Course;
use App\Models\Article;
use App\Models\Invoice;
use App\Models\Currency;
use App\Models\Province;
use App\Models\LoanProduct;
use App\Models\PaymentType;
use App\Models\UserWidgets;
use App\Models\Consultation;
use Illuminate\Http\Request;
use App\Models\CourseMaterial;
use App\Models\InvoicePayment;
use App\Models\LoanApplication;
use App\Actions\Reports\Reports;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LoanApplicationsExport;
use App\Models\LoanApplicationLinkedApprovalStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;

class DashboardController extends Controller
{
    public $filterOptions;
    public function __construct()
    {
        $this->middleware('auth');
        $this->filterOptions = [];
    }

    public function index()
    {
        //check if it is the first time the user is logging in
        if (Auth::user()->first_attempt === 'yes') {
            //update the first_attempt column to no
            Auth::user()->first_attempt = 'no';
            Auth::user()->save();
            return redirect()->route('profile.show')
                ->with('success', 'Welcome to your dashboard. Please update your password and profile to continue.');
        }
        $clients = Client::count();
        $applicationsCount = LoanApplication::count();
        $applicationsPending =  LoanApplication::whereHas('currentLinkedStage', function ($query) {
            $query->where('status', '!=','approved')
                ->where('status', '!=','rejected');

        })->count();
        $applicationsApproved = LoanApplication::whereHas('currentLinkedStage', function ($query) {
            $query->where('status', 'approved');
        })->count();
        // dd($applicationsApproved);
        $applicationsRejected = LoanApplication::whereHas('currentLinkedStage', function ($query) {
                $query->where('status', 'rejected');
            })->count();
        $applicationsApprovedAmount = LoanApplication::whereHas('currentLinkedStage', function ($query) {
            $query->where('status', 'approved');
        })->sum('amount');
        $applicationsRecommended = LoanApplication::whereHas('currentLinkedStage', function ($query) {
                $query->where('status', 'recommend');
            })->count();


        $totalAmountAppliedAmt = (float) LoanApplication::sum('amount');
        $totalRecommendedAmt = (float)LoanApplication::with('linkedStages')
            ->whereHas('linkedStages', function ($query) {
                $query->where('status', 'recommend');
            })->sum('amount');
        $totalApprovedAmt = LoanApplication::where('status', 'approved')->sum('amount');
        //check among the linked stages, check if there is that has a status of rejected
        $totalRejectedAmt = (float)LoanApplication::whereHas('currentLinkedStage', function ($query) {
                $query->where('status', 'rejected');
            })->sum('amount');

            $totalPendingAmount = (float)LoanApplication::whereHas('currentLinkedStage', function ($query) {
                $query->where('status', '!=','approved')
                    ->where('status', '!=','rejected');

            })->sum('amount');
            // dd($totalPendingAmount);




        return Inertia::render('Dashboard', [
            'clients' => $clients,
            'applicationsCount' => $applicationsCount,
            'applicationsPending' => $applicationsPending,
            'applicationsApproved' => $applicationsApproved,
            'applicationsRejected' => $applicationsRejected,
            'applicationsApprovedAmount' => $applicationsApprovedAmount,
            'totalAmountAppliedAmt' => $totalAmountAppliedAmt,
            'totalRecommendedAmt' => $totalRecommendedAmt,
            'totalApprovedAmt' => $totalApprovedAmt,
            'totalRejectedAmt' => $totalRejectedAmt,
            'applicationsRecommended' => $applicationsRecommended,
            'totalPendingAmount' =>$totalPendingAmount
        ]);
    }

    public function test()
    {
        return Inertia::render('Test', []);
    }

    public function saveWidgets(Request $request)
    {
        $widgets = config('widgets');
        $userWidgets = UserWidgets::where('user_id', Auth::id())->first();
        if (empty($userWidgets)) {
            $userWidgets = new UserWidgets();
            $userWidgets->user_id = Auth::id();
            $userWidgets->widgets = [];
            $userWidgets->save();
        }
        $selectedWidgets = [];
        foreach ($request->widgets as $key) {
            if (empty($userWidgets->widgets[$key])) {
                foreach ($widgets as $widget) {
                    if ($widget['id'] === $key) {
                        $selectedWidgets[$key] = $widget;
                    }
                }
            }
        }
        foreach ($userWidgets->widgets as $widget) {
            if (in_array($widget['id'], $request->widgets)) {
                $selectedWidgets[$widget['id']] = $widget;
            }
        }
        $userWidgets->widgets = $selectedWidgets;
        $userWidgets->save();
        return redirect()->back()->with('success', 'Updated successfully.');
    }

    public function updateWidgets(Request $request)
    {
        $widgets = config('widgets');

        $userWidgets = UserWidgets::where('user_id', Auth::id())->first();
        if (empty($userWidgets)) {
            $userWidgets = new UserWidgets();
            $userWidgets->user_id = Auth::id();
            $userWidgets->widgets = [];
            $userWidgets->save();
        }
        $selectedWidgets = [];
        foreach ($request->widgets as $key) {
            foreach ($widgets as $widget) {
                if ($widget['id'] === $key['id']) {
                    $selectedWidgets[$key['id']] = $key;
                }
            }
        }
        $userWidgets->widgets = $selectedWidgets;
        $userWidgets->save();
        return response()->json([
            'success' => true
        ]);
    }

    public function getTotalConsultationsCount()
    {
        $consultations = Consultation::count();
        $consultationsLastMonth = Consultation::whereBetween('created_at', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $consultationsThisMonth = Consultation::whereBetween('created_at', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $consultationsChange = 0;
        $consultationsChangeClass = 'text-green-500';
        if ($consultationsLastMonth > 0) {
            $consultationsChange = abs(($consultationsThisMonth - $consultationsLastMonth) * 100 / $consultationsLastMonth);
            if ($consultationsThisMonth < $consultationsLastMonth) {
                $consultationsChangeClass = 'text-red-500';
            }
        }
        if ($consultationsLastMonth === 0 && $consultationsThisMonth > 0) {
            $consultationsChange = 100;
        }
        return response()->json([
            'consultations' => number_format($consultations),
            'consultationsChange' => number_format($consultationsChange, 2),
            'consultationsChangeClass' => $consultationsChangeClass,
        ]);
    }

    public function getTotalMembersCount()
    {
        $members = Client::count();
        $membersLastMonth = Client::whereBetween('created_at', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $membersThisMonth = Client::whereBetween('created_at', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $membersChange = 0;
        $membersChangeClass = 'text-green-500';
        if ($membersLastMonth > 0) {
            $membersChange = abs(($membersThisMonth - $membersLastMonth) * 100 / $membersLastMonth);
            if ($membersThisMonth < $membersLastMonth) {
                $membersChangeClass = 'text-red-500';
            }
        }
        if ($membersLastMonth === 0 && $membersThisMonth > 0) {
            $membersChange = 100;
        }
        return response()->json([
            'members' => number_format($members),
            'membersChange' => number_format($membersChange, 2),
            'membersChangeClass' => $membersChangeClass,
        ]);
    }

    public function getTotalAppointmentsCount()
    {
        $appointments = Event::count();
        $appointmentsLastMonth = Event::whereBetween('created_at', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $appointmentsThisMonth = Event::whereBetween('created_at', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $appointmentsChange = 0;
        $appointmentsChangeClass = 'text-green-500';
        if ($appointmentsLastMonth > 0) {
            $appointmentsChange = abs(($appointmentsThisMonth - $appointmentsLastMonth) * 100 / $appointmentsLastMonth);
            if ($appointmentsThisMonth < $appointmentsLastMonth) {
                $appointmentsChangeClass = 'text-red-500';
            }
        }
        if ($appointmentsLastMonth === 0 && $appointmentsThisMonth > 0) {
            $appointmentsChange = 100;
        }
        return response()->json([
            'appointments' => number_format($appointments),
            'appointmentsChange' => number_format($appointmentsChange, 2),
            'appointmentsChangeClass' => $appointmentsChangeClass,
        ]);
    }

    public function getTotalPaymentsAmount()
    {
        $payments = InvoicePayment::selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $paymentsLastMonth = InvoicePayment::whereBetween('created_at', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $paymentsThisMonth = InvoicePayment::whereBetween('created_at', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $paymentsChange = 0;
        $paymentsChangeClass = 'text-green-500';
        if ($paymentsLastMonth > 0) {
            $paymentsChange = abs(($paymentsThisMonth - $paymentsLastMonth) * 100 / $paymentsLastMonth);
            if ($paymentsThisMonth < $paymentsLastMonth) {
                $paymentsChangeClass = 'text-red-500';
            }
        }
        if ($paymentsLastMonth === 0 && $paymentsThisMonth > 0) {
            $paymentsChange = 100;
        }
        return response()->json([
            'payments' => number_format($payments),
            'paymentsChange' => number_format($paymentsChange, 2),
            'paymentsChangeClass' => $paymentsChangeClass,
        ]);
    }

    public function getTotalInvoicesAmount()
    {
        $invoices = Invoice::selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $invoicesLastMonth = Invoice::whereBetween('created_at', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $invoicesThisMonth = Invoice::whereBetween('created_at', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $invoicesChange = 0;
        $invoicesChangeClass = 'text-green-500';
        if ($invoicesLastMonth > 0) {
            $invoicesChange = abs(($invoicesThisMonth - $invoicesLastMonth) * 100 / $invoicesLastMonth);
            if ($invoicesThisMonth < $invoicesLastMonth) {
                $invoicesChangeClass = 'text-red-500';
            }
        }
        if ($invoicesLastMonth === 0 && $invoicesThisMonth > 0) {
            $invoicesChange = 100;
        }
        return response()->json([
            'invoices' => number_format($invoices),
            'invoicesChange' => number_format($invoicesChange, 2),
            'invoicesChangeClass' => $invoicesChangeClass,
        ]);
    }

    public function getWaitingList()
    {
        $query = Consultation::with(['doctor', 'member', 'nurse']);
        if (Auth::user()->hasRole('doctor') && Auth::user()->hasPermissionTo('consultations.view_assigned_consultations_only')) {
            $query->where('doctor_id', Auth::id());
            $query->where('stage', 'waiting_for_doctor');
        }
        if (Auth::user()->hasRole('nurse') && Auth::user()->hasPermissionTo('consultations.view_assigned_consultations_only')) {
            $query->where('nurse_id', Auth::id());
            $query->where('stage', 'waiting_for_nurse');
        }
        if (Auth::user()->hasRole('receptionist') && Auth::user()->hasPermissionTo('consultations.view_assigned_consultations_only')) {
            $query->where('receptionist_id', Auth::id());
            $query->where('stage', 'with_receptionist');
        }
        $members = $query->orderBy('created_at')->get()->map(function ($item) {
            if (Auth::user()->hasRole('doctor')) {
                $item->waiting_time = Carbon::now()->diffForHumans($item->nurse_completed_at, true, false);
            } elseif (Auth::user()->hasRole('nurse')) {
                $item->waiting_time = Carbon::now()->diffForHumans($item->receptionist_completed_at, true, false);
            } else {
                $item->waiting_time = Carbon::now()->diffForHumans($item->created_at, true, false);
            }
            return $item;
        });
        return response()->json($members);
    }

    public function getAppointments(Request $request)
    {
        $doctorID = null;
        $nurseID = null;
        $receptionistID = null;
        if (Auth::user()->hasRole('doctor') && Auth::user()->hasPermissionTo('appointments.view_assigned_appointments_only')) {
            $doctorID = Auth::id();
        }
        $appointments = Event::with(['doctor', 'member'])
            ->filter(\request()->only('search', 'branch_id', 'status', 'created_by_type', 'member_id', 'doctor_id', 'appointment_type', 'date_range'))
            ->doctor($doctorID)
            ->where('start_date', '>=', Carbon::today()->format('Y-m-d'))
            ->get();
        return response()->json($appointments);
    }

    public function getAppointmentsByStatusPieChart(Request $request)
    {
        $reports = new Reports();
        $doctorID = null;
        $nurseID = null;
        $receptionistID = null;
        if (Auth::user()->hasRole('doctor') && Auth::user()->hasPermissionTo('appointments.view_assigned_appointments_only')) {
            $doctorID = Auth::id();
        }
        $appointments = $reports->getAppointmentsByStatus([
            'doctor_id' => $doctorID,
        ]);
        return response()->json($appointments);
    }

    public function getAppointmentsByPeriodGraph(Request $request)
    {
        $reports = new Reports();
        $doctorID = null;
        $nurseID = null;
        $receptionistID = null;
        if (Auth::user()->hasRole('doctor') && Auth::user()->hasPermissionTo('appointments.view_assigned_appointments_only')) {
            $doctorID = Auth::id();
        }
        $appointments = $reports->getAppointmentsByPeriod([
            'doctor_id' => $doctorID,
            'period' => $request->period,
        ]);
        return response()->json($appointments);
    }

    public function getPaymentsByPaymentTypePieChart(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getPaymentsByPaymentType([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'doctor_id' => $request->doctor_id,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'co_payer_id' => $request->co_payer_id,
            'payment_type_id' => $request->payment_type_id,
            'paid_by' => $request->paid_by,
            'period' => $request->period,
        ]);
        return response()->json($data);
    }

    public function getPaymentsByPeriodGraph(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getPaymentsByPeriod([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'doctor_id' => $request->doctor_id,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'co_payer_id' => $request->co_payer_id,
            'payment_type_id' => $request->payment_type_id,
            'paid_by' => $request->paid_by,
            'period' => $request->period,
        ]);
        return response()->json($data);
    }

    public function getIncomeExpensesPieChart(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getIncomeExpenses([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'doctor_id' => $request->doctor_id,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'co_payer_id' => $request->co_payer_id,
            'payment_type_id' => $request->payment_type_id,
            'paid_by' => $request->paid_by,
            'period' => $request->period,
        ]);
        return response()->json($data);
    }

    public function getIncomeExpensesGraph(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getPeriodIncomeExpenses([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'doctor_id' => $request->doctor_id,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'co_payer_id' => $request->co_payer_id,
            'payment_type_id' => $request->payment_type_id,
            'paid_by' => $request->paid_by,
            'period' => $request->period,
        ]);
        return response()->json($data);
    }

    public function getConsultationsByPeriodGraph(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getConsultationsByPeriod([
            'doctor_id' => $request->doctor_id,
            'branch_id' => $request->branch_id,
            'co_payer_id' => $request->co_payer_id,
            'period' => $request->period,
        ]);
        return response()->json($data);
    }

    public function filter($scope)
    {

        $products = LoanProduct::with(['category', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();
        // $branches = Branch::get();
        $filterScope = $scope;
        $users = User::where('active', 1)->get();

        return Inertia::render('Dashboard/CreateFilter', [
            'scope' => $filterScope,
            'provinces' =>  Province::all()->transform(function ($province) {
                return [
                    'value' => $province->id,
                    'label' => $province->name,
                ];
            }),
            'products' => $products,
            'branches' => Branch::all()->transform(function ($branch) {
                return [
                    'value' => $branch->id,
                    'label' => $branch->name,
                ];
            }),
            'users' => $users

        ]);
    }

    public function filterResults(Request $request)
    {

        $this->filterOptions = $request->all();

        // dd($this->filterOptions);

        switch($request->scope){
            case 'all':
                $applications = LoanApplication::with(['staff', 'client', 'product', 'currentLinkedStage', 'currentLinkedStage.stage', 'currentLinkedStage.approver', 'currentLinkedStage.assignedBy', 'branch', 'createdBy', 'client.province'])
            ->filter(\request()->only('search', 'client_id', 'loan_product_id', 'province_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'));
            break;

            case 'pending':
                $applications = LoanApplication::with(['staff', 'client', 'product', 'currentLinkedStage', 'currentLinkedStage.stage', 'currentLinkedStage.approver', 'currentLinkedStage.assignedBy', 'branch', 'createdBy',  'client.province'])
            ->filter(\request()->only('search', 'client_id', 'loan_product_id', 'province_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'))
            ->whereHas('currentLinkedStage', function ($query) {
                $query->where('status', '!=','approved')
                    ->where('status', '!=','rejected');
            });
            break;
            case 'rejected':
                $applications = LoanApplication::with(['staff', 'client', 'product', 'currentLinkedStage', 'currentLinkedStage.stage', 'currentLinkedStage.approver', 'currentLinkedStage.assignedBy', 'branch', 'createdBy',  'client.province'])
            ->filter(\request()->only('search', 'client_id', 'loan_product_id', 'province_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'))
            ->whereHas('currentLinkedStage', function ($query) {
                $query->where('status', 'rejected');
            });
            break;
            case 'approved':
                $applications = LoanApplication::with(['staff', 'client', 'product', 'currentLinkedStage', 'currentLinkedStage.stage', 'currentLinkedStage.approver', 'currentLinkedStage.assignedBy', 'branch', 'createdBy',  'client.province'])
            ->filter(\request()->only('search', 'client_id', 'loan_product_id', 'province_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'))
            ->whereHas('currentLinkedStage', function ($query) {
                $query->where('status', 'approved');
            });
            break;
            default:
            return redirect()->back()->with('error', 'Invalid filter scope');
            break;

        }

             // Apply start date and end date logic
        $endDate = $request->filled('loan_end_date') ? $request->loan_end_date : Carbon::today()->format('Y-m-d');
        $startDate = $request->loan_start_date;

        $applications = $applications->whereBetween('date', [$startDate, $endDate]);


        // Handle optional branch ID
        if ($request->filled('branch')) {
            $applications = $applications->whereIn('branch_id', $request->branch);
        }

        //handle region
        if ($request->filled('region')) {
            $applications = $applications->whereHas('client.province', function ($query) use ($request) {
                $query->whereIn('id', $request->region);
            });
        }

        if ($request->filled('product')) {
            $applications = $applications->where('loan_product_id', $request->product);
        }

        if ($request->filled('loan_initiator_id')) {
            $applications = $applications->where('created_by_id',  $request->loan_initiator_id);
        }

        if ($request->filled('loan_approver_id')) {
            $applications = $applications->where('approved_by_id', $request->loan_approver_id);
        }


        // Apply loan amount logic based on operator
        $loanAmount = $request->loan_amount;
        //validate the loan amount, it shoulf be 0 or greater
        if ($loanAmount < 0) {
            return redirect()->back()->with('error', 'Invalid loan amount');
        }
        $operator = $request->loan_amount_operator;

        switch ($operator) {
            case 'greater':
                $applications = $applications->where('amount', '>', $loanAmount);
                break;
            case 'less':
                $applications = $applications->where('amount', '<', $loanAmount);
                break;
            case 'equal':
                $applications = $applications->where('amount', '=', $loanAmount);
                break;
            default:
                // Handle invalid operator (optional)
                break;
        }
        $applicationCount = $applications->count();
        $applications = $applications->orderBy('created_at', 'desc')
            ->paginate($applicationCount);

            // dd($applications);


        $hiddenColumns =  [
            'loan_description' => $request->loan_description,
            'cif' =>$request->cif,
            'user_id' => $request->user_id,
            'show_branch' =>$request->show_branch,
            'show_region' => $request->show_region,
            'show_loan_approver' => $request->show_loan_approver,

        ];



        // region in in the client table and branch

        return Inertia::render('LoanApplications/Filtered', [
            'filters' => \request()->all('search', 'client_id', 'loan_product_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'),
            'applications' => $applications,
            'products' => LoanProduct::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'branches' => Branch::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'scope' => $request->scope,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'hiddenColumns' => $hiddenColumns
        ]);

        // For example, you might want to redirect back with a success message
        // return redirect()->back()->with('success', 'Filter applied successfully!');
    }

    public function export(Request $request)
    {
        // dd('ok');

        $formData = json_decode($request->get('applications'), true);
        $applications = $formData['data'];

        $extraColumns = json_decode($request->get('hiddenColumns'), true);



        $exportData = [];
        foreach ($applications as $application) {
            $exportData[] = [
                'ID' => $application['id'],
                'Loan Date' => $application['date'],
                'Client' => Client::find($application['client_id'])->name,
                'Product' => LoanProduct::find($application['loan_product_id'])->name,
                'Amount' => $application['amount'],
                'Score' => $application['score'],
                'Status' => $application['current_stage_status'],
                'Created At' =>  $application['created_at'],
                'loan_description' => $extraColumns['loan_description'] != null ? $application['description'] : null,
                'Created By' =>   $extraColumns['user_id'] != null ? User::find($application['created_by_id'])->name : null,
                'CIF' =>  $extraColumns['cif'] != null ? Client::find($application['client_id'])->external_id : null,
            ];

        }
        // dd($extraColumns);


        // Instantiate the export class
        $export = new LoanApplicationsExport($exportData, $extraColumns);

    // Optionally, you can modify the export class properties or methods here if needed

    // Use Laravel Excel to export data
    return Excel::download($export, 'loan_applications.xlsx');
    }

    public function myWorkspace()
    {

    //    $assignedToMeLoans = AuthUser::assignedToMeApplications;
    //    dd($assignedToMeLoans);
    $authenticatedUser = LoanApplicationLinkedApprovalStage::where('approver_id', Auth::id())->get();
    dd($authenticatedUser);


    LoanApplication::where('linkedStages')->get();



        $applications = LoanApplication::with(['staff', 'client', 'product', 'currentLinkedStage', 'currentLinkedStage.stage', 'currentLinkedStage.approver', 'currentLinkedStage.assignedBy','linkedStages', 'branch'])
            ->filter(\request()->only('search', 'client_id', 'loan_product_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        // dd($applications);

        return Inertia::render('Dashboard/MyWorkspace', [
            'filters' => \request()->all('search', 'client_id', 'loan_product_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'),
            'applications' => $applications,
            'products' => LoanProduct::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'branches' => Branch::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
        // return Inertia::render('Dashboard/MyWorkspace', []);
    }
}
