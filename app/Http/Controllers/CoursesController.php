<?php

namespace App\Http\Controllers;

use App\Events\CourseCreated;
use App\Events\CourseStatusChanged;
use App\Models\Article;
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

class CoursesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:courses.index'])->only(['index', 'show']);
        $this->middleware(['permission:courses.create'])->only(['create', 'store']);
        $this->middleware(['permission:courses.update'])->only(['edit', 'update']);
        $this->middleware(['permission:courses.destroy'])->only(['destroy']);
        $this->middleware(['permission:courses.approve'])->only(['changeStatus']);
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
        if (Auth::user()->hasRole('tutor') && Auth::user()->hasPermissionTo('courses.view_assigned_courses_only')) {
            $tutorID = Auth::id();
        }
        $courses = Course::with(['tutor','category'])
            ->filter(\request()->only('search', 'tutor_id', 'course_category_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status','approval_status'))

            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Courses/Index', [
            'filters' => \request()->all('search', 'tutor_id', 'course_category_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status','approval_status'),
            'courses' => $courses,
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
        return Inertia::render('Courses/Create', [
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
            'name' => ['required'],
            'status' => ['required'],
            'duration' => ['required'],
        ]);
        $member = Client::find($request->member_id);
        $course = new Course();
        $course->created_by_id = Auth::id();
        $course->tutor_id = Auth::id();
        $course->course_category_id = $request->course_category_id;
        $course->name = $request->name;
        $course->duration = $request->duration;
        $course->status = $request->status;
        $course->amount = $request->amount;
        $course->allow_comments = $request->allow_comments?1:0;
        $course->active = $request->active?1:0;
        $course->description = $request->description;
        $course->save();
        if ($request->file('featured_image')) {
            $filesController = new FilesController();
            $file = $filesController->store([
                'record_id' => $course->id,
                'category' => 'course_file',
                'file' => $request->file('featured_image'),
            ], [], 'public', 'media');
            $course->featured_image = $file->link;
            $course->save();
        }
        event(new CourseCreated($course));
        activity()
            ->performedOn($course)
            ->log('Create Course');
        return redirect()->route('courses.show', $course->id)->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param Course $course
     * @return \Inertia\Response
     */
    public function show(Course $course)
    {
        $course->load(['tutor','category']);
        return Inertia::render('Courses/Show', [
            'course' => $course,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @return \Inertia\Response
     */
    public function edit(Course $course)
    {
        return Inertia::render('Courses/Edit', [
            'course' => $course,
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
     * @param Course $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => ['required'],
            'status' => ['required'],
            'duration' => ['required'],
        ]);
        $course->course_category_id = $request->course_category_id;
        $course->name = $request->name;
        $course->duration = $request->duration;
        $course->status = $request->status;
        $course->amount = $request->amount;
        $course->allow_comments = $request->allow_comments?1:0;
        $course->active = $request->active?1:0;
        $course->description = $request->description;
        $course->save();
        if ($request->file('featured_image')) {
            $filesController = new FilesController();
            $file = $filesController->store([
                'record_id' => $course->id,
                'category' => 'course_file',
                'file' => $request->file('featured_image'),
            ], [], 'public', 'media');
            $course->featured_image = $file->link;
            $course->save();
        }
        return redirect()->route('courses.show', $course->id)->with('success', 'Course updated successfully.');
    }

    public function changeStatus(Request $request, Course $course)
    {
        $course->approval_status = $request->status;
        $course->approval_status_notes = $request->description;
        $course->approved_by_id = Auth::id();
        $course->save();
        activity()
            ->performedOn($course)
            ->log('Change Course status');
        if ($course->wasChanged('approval_status')) {
            event(new CourseStatusChanged($course));
        }
        return redirect()->route('courses.show', $course->id)->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Course $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Course $course)
    {
        $course->delete();
        activity()
            ->performedOn($course)
            ->log('Delete Course');
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }
    public function search(Request $request)
    {
        $search = $request->s;
        $id = $request->id;
        $tutorID= $request->tutor_id;
        $courseCategoryID= $request->course_category_id;
        $data = Course::with(['tutor','category'])->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
            $query->orWhere('id', 'like', "%$search%");
            $query->orWhere('description', 'like', "%$search%");
        })->when($id, function ($query) use ($id) {
            return $query->where('id', $id);
        })->when($tutorID, function ($query) use ($tutorID) {
            return $query->where('tutor_id', $tutorID);
        })->when($courseCategoryID, function ($query) use ($courseCategoryID) {
            return $query->where('course_category_id', $courseCategoryID);
        })->get();
        return response()->json($data);
    }
    public function registrations(Course $course)
    {

        $registrations = CourseRegistration::with(['tutor', 'course', 'member'])
            ->where('course_id',$course->id)
            ->filter(\request()->only('search', 'tutor_id', 'course_id', 'member_id', 'course_category_id', 'status'))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Courses/Registrations/Index', [
            'filters' => \request()->all('search', 'tutor_id', 'course_category_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status','approval_status'),
            'registrations' => $registrations,
            'course' => $course,
            'categories' => CourseCategory::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);

    }
    public function articles(Course $course)
    {

        $articles = Article::filter(\request()->only('search', 'status'))->with(['category', 'createdBy', 'course'])
            ->where('course_id',$course->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Courses/Articles/Index', [
            'filters' => \request()->all('search', 'tutor_id', 'article_category_id', 'province_id', 'branch_id', 'district_id', 'ward_id', 'date_range', 'village_id', 'staff_id', 'status','approval_status'),
            'articles' => $articles,
            'course' => $course,
            'categories' => CourseCategory::get()->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name
                ];
            }),
        ]);

    }
}
