<?php

namespace App\Http\Controllers;

use App\Events\CourseCreated;
use App\Events\CourseRegistrationCreated;
use App\Events\CourseRegistrationStatusChanged;
use App\Events\CourseStatusChanged;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\CourseCategory;
use App\Models\Client;
use App\Models\Setting;
use App\Models\Tariff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class CourseRegistrationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:courses.registrations.index'])->only(['index', 'show']);
        $this->middleware(['permission:courses.registrations.create'])->only(['create', 'store']);
        $this->middleware(['permission:courses.registrations.update'])->only(['edit', 'update']);
        $this->middleware(['permission:courses.registrations.destroy'])->only(['destroy']);
        $this->middleware(['permission:courses.registrations.approve'])->only(['changeStatus']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $tutorID = null;
        $nurseID = null;
        $receptionistID = null;
        if (Auth::user()->hasRole('tutor') && Auth::user()->hasPermissionTo('registrations.view_assigned_registrations_only')) {
            $tutorID = Auth::id();
        }
        $registrations = CourseRegistration::with(['tutor', 'course', 'member'])
            ->filter(\request()->only('search', 'tutor_id', 'course_id', 'member_id', 'course_category_id', 'status'))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('CourseRegistrations/Index', [
            'filters' => \request()->all('search', 'tutor_id', 'course_id', 'member_id', 'course_category_id', 'status'),
            'registrations' => $registrations,
            'categories' => CourseCategory::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('CourseRegistrations/Create', [
            'member_id'=>\request('member_id'),
            'course_id'=>\request('course_id'),
            'categories' => CourseCategory::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => ['required'],
            'course_id' => ['required'],
        ]);
        $member = Client::find($request->member_id);
        $course = Course::find($request->course_id);
        $registration = new CourseRegistration();
        $registration->created_by_id = Auth::id();
        $registration->tutor_id = $course->tutor_id;
        $registration->course_id = $request->course_id;
        $registration->member_id = $request->member_id;
        $registration->registration_date = $request->registration_date??date('Y-m-d');
        $registration->start_date = $request->start_date??date('Y-m-d');
        $registration->status = $request->status;
        $registration->member_name = $request->member_name??$member->name;
        $registration->channel = $request->channel;
        $registration->description = $request->description;
        $registration->save();
        event(new CourseRegistrationCreated($registration));
        activity()
            ->performedOn($registration)
            ->log('Create Course Registration');
        return redirect()->route('registrations.show', $registration->id)->with('success', 'Course registration created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param CourseRegistration $registration
     * @return \Inertia\Response
     */
    public function show(CourseRegistration $registration)
    {
        $registration->load(['tutor', 'course', 'member']);
        return Inertia::render('CourseRegistrations/Show', [
            'registration' => $registration,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CourseRegistration $registration
     * @return \Inertia\Response
     */
    public function edit(CourseRegistration $registration)
    {
        return Inertia::render('CourseRegistrations/Edit', [
            'registration' => $registration,
            'categories' => CourseCategory::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param CourseRegistration $registration
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CourseRegistration $registration)
    {
        $request->validate([

        ]);
        $registration->registration_date = $request->registration_date??date('Y-m-d');
        $registration->start_date = $request->start_date??date('Y-m-d');
        $registration->status = $request->status;
        $registration->member_name = $request->member_name??$registration->member->name;
        $registration->channel = $request->channel;
        $registration->description = $request->description;
        $registration->save();
        if ($registration->wasChanged('status')) {
            event(new CourseRegistrationStatusChanged($registration));
        }
        return redirect()->route('registrations.show', $registration->id)->with('success', 'Course Registration  updated successfully.');
    }

    public function changeStatus(Request $request, Course $registration)
    {
        $registration->status = $request->status;
        $registration->approval_status_notes = $request->approval_status_notes;
        $registration->approved_by_id = Auth::id();
        $registration->save();
        activity()
            ->performedOn($registration)
            ->log('Change Course Registration status');
        if ($registration->wasChanged('status')) {
            event(new CourseRegistrationStatusChanged($registration));
        }
        return redirect()->route('registrations.show', $registration->id)->with('success', 'Course  Registrationupdated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CourseRegistration $registration
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CourseRegistration $registration)
    {
        $registration->delete();
        activity()
            ->performedOn($registration)
            ->log('Delete Course Registration');
        return redirect()->route('registrations.index')->with('success', 'Course Registration deleted successfully.');
    }
}
