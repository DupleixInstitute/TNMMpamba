<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Country;
use App\Models\District;
use App\Models\LoanApplication;
use App\Models\Client;
use App\Models\Province;
use App\Models\Village;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class ClientsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:clients.index'])->only(['index', 'show']);
        $this->middleware(['permission:clients.create'])->only(['create', 'store']);
        $this->middleware(['permission:clients.update'])->only(['edit', 'update']);
        $this->middleware(['permission:clients.destroy'])->only(['destroy']);
    }

    public function index()
    {

        $clients = Client::with(['country','province', 'district', 'ward', 'village', 'branch'])
        ->filter(\request()->only('search', 'province_id', 'gender', 'branch_id', 'district_id', 'ward_id', 'village_id'))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Clients/Index', [
            'filters' => \request()->all('search', 'province_id', 'gender', 'branch_id', 'district_id', 'ward_id', 'village_id'),
            'clients' => $clients,
            'countries' => Country::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }

    public function create()
    {

        return Inertia::render('Clients/Create', [
            'countries' => Country::all()->transform(function ($country) {
                return [
                    'value' => $country->id,
                    'label' => $country->name,
                ];
            }),
            'branches' => Branch::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'provinces' => Province::get()->transform(function ($item) {
                return [
                    'country_id' => $item->country_id,
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            }),
            'districts' => District::get()->transform(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                    'province_id' => $item->province_id,
                ];
            }),
            'wards' => Ward::get()->transform(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                    'district_id' => $item->district_id,
                ];
            }),
            'villages' => Village::get()->transform(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                    'ward_id' => $item->ward_id,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'type' => ['required', 'string', 'max:255'],
            //'dob' => ['required'],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);
        $client = new Client();
        $client->created_by_id = Auth::id();
        $client->province_id = $request->province_id;
        $client->district_id = $request->branch_id;
        $client->ward_id = $request->ward_id;
        $client->village_id = $request->village_id;
        $client->branch_id = $request->branch_id;
        $client->name = $request->name;
        $client->type = $request->type;
        $client->country_id = $request->country_id;
        $client->title_id = $request->title_id;
        $client->nationality_id = $request->nationality_id;
        $client->gender = $request->gender;
        $client->email = $request->email;
        $client->mobile = $request->mobile;
        $client->tel = $request->tel;
        $client->zip = $request->zip;
        $client->external_id = $request->external_id;
        $client->address = $request->address;
        $client->postal_address = $request->postal_address;
        $client->id_type = $request->id_type;
        $client->id_number = $request->id_number;
        $client->dob = $request->dob;
        $client->marital_status = $request->marital_status;
        $client->occupation = $request->occupation;
        $client->employer_name = $request->employer_name;
        $client->employer_address = $request->employer_address;
        $client->description = $request->description;
        $client->status = $request->status;
        $client->save();
        if ($request->file('photo')) {
            $client->updateProfilePhoto($request->file('photo'));
        }
        activity()
            ->performedOn($client)
            ->log('Create Client');
        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $client->load(['country','province', 'district', 'ward', 'village', 'branch']);
        return Inertia::render('Clients/Show', [
            'client' => $client
        ]);
    }

    public function edit(Client $client)
    {

        return Inertia::render('Clients/Edit', [
            'client' => $client,
            'countries' => Country::all()->transform(function ($country) {
                return [
                    'value' => $country->id,
                    'label' => $country->name,
                ];
            }),
            'branches' => Branch::get()->map(function ($item) {
                return [

                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
            'provinces' => Province::get()->transform(function ($item) {
                return [
                    'country_id' => $item->country_id,
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            }),
            'districts' => District::get()->transform(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                    'province_id' => $item->province_id,
                ];
            }),
            'wards' => Ward::get()->transform(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                    'district_id' => $item->district_id,
                ];
            }),
            'villages' => Village::get()->transform(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                    'ward_id' => $item->ward_id,
                ];
            }),
        ]);
    }

    public function update(Request $request, Client $client)
    {

        $request->validate([
            'name' => ['required', 'string'],
            'type' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);
        $client->province_id = $request->province_id;
        $client->district_id = $request->branch_id;
        $client->ward_id = $request->ward_id;
        $client->village_id = $request->village_id;
        $client->branch_id = $request->branch_id;
        $client->name = $request->name;
        $client->type = $request->type;
        $client->country_id = $request->country_id;
        $client->title_id = $request->title_id;
        $client->nationality_id = $request->nationality_id;
        $client->gender = $request->gender;
        $client->email = $request->email;
        $client->mobile = $request->mobile;
        $client->tel = $request->tel;
        $client->zip = $request->zip;
        $client->external_id = $request->external_id;
        $client->address = $request->address;
        $client->postal_address = $request->postal_address;
        $client->id_type = $request->id_type;
        $client->id_number = $request->id_number;
        $client->dob = $request->dob;
        $client->marital_status = $request->marital_status;
        $client->occupation = $request->occupation;
        $client->employer_name = $request->employer_name;
        $client->employer_address = $request->employer_address;
        $client->description = $request->description;
        $client->status = $request->status;
        $client->save();
        if ($request->file('photo')) {
            $client->updateProfilePhoto($request->file('photo'));
        }
        activity()
            ->performedOn($client)
            ->log('Update Client');
        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {

        $client->delete();
        activity()
            ->performedOn($client)
            ->log('Delete Client');
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->s;
        $id = $request->id;
        $branchID = $request->branch_id;
        $data = Client::with(['country','province', 'branch', 'district', 'ward', 'village'])->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
            $query->orWhere('email', 'like', "%$search%");
            $query->orWhere('id', 'like', "%$search%");
            $query->orWhere('id_number', 'like', "%$search%");
            $query->orWhere('external_id', 'like', "%$search%");
        })->when($id, function ($query) use ($id) {
            return $query->where('id', $id);
        })->when($branchID, function ($query) use ($branchID) {
            return $query->where('branch_id', $branchID);
        })->get();
        return response()->json($data);
    }

    public function loanApplication(Client $client)
    {

        $applications = LoanApplication::with(['staff', 'client', 'product'])
            ->filter(\request()->only('search', 'client_id', 'loan_product_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status'))
            ->where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);


        return Inertia::render('Clients/LoanApplications/Index', [
            'client' => $client,
            'applications' => $applications,
        ]);
    }

    public function courses(Client $client)
    {

        $registrations = CourseRegistration::with(['tutor', 'course', 'client'])
            ->where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Clients/Courses/Index', [
            'client' => $client,
            'registrations' => $registrations,
        ]);
    }
}
