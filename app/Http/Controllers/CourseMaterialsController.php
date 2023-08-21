<?php

namespace App\Http\Controllers;

use App\Models\CourseMaterial;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CourseMaterialsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:courses.materials.index'])->only(['index', 'show']);
        $this->middleware(['permission:courses.materials.create'])->only(['create', 'store']);
        $this->middleware(['permission:courses.materials.update'])->only(['edit', 'update']);
        $this->middleware(['permission:courses.materials.destroy'])->only(['destroy']);
    }

    public function index(Course $course)
    {

        $materials = CourseMaterial::with(['file'])
            ->where('course_id', $course->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Courses/Materials/Index', [
            'course' => $course,
            'materials' => $materials,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(Course $course)
    {
        return Inertia::render('Courses/Materials/Create', [
            'course' => $course,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => ['required'],
            'type' => ['required'],
            'external_link' => ['nullable', 'required_if:type,external_video,external_link'],
            'file' => ['nullable', 'required_if:type,document,video', 'file'],
        ]);
        $material = new CourseMaterial();
        $material->course_id = $course->id;
        $material->created_by_id = Auth::id();
        $material->type = $request->type;
        $material->title = $request->title;
        $material->external_link = $request->external_link;
        $material->description = $request->description;
        $material->save();
        if ($request->hasFile('file')) {
            $fileController = new FilesController();
            $file = $fileController->store([
                'file' => $request->file('file'),
                'name' => $request->title,
                'category' => 'course_material',
                'record_id' => $material->id,
            ],[],'public','media');
        }


        activity()
            ->performedOn($course)
            ->log('Create Course Material');
        return redirect()->route('courses.materials.index', [$course->id])->with('success', 'Course Material created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param CourseMaterial $material
     * @return \Illuminate\Http\Response
     */
    public function show(CourseMaterial $material)
    {
        $course = $material->course;
        $material->load(['file']);
        return Inertia::render('Courses/Materials/Show', [
            'course' => $course,
            'material' => $material,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CourseMaterial $material
     * @return \Inertia\Response
     */
    public function edit(CourseMaterial $material)
    {
        $course = $material->course;

        return Inertia::render('Courses/Materials/Edit', [
            'course' => $course,
            'material' => $material,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param CourseMaterial $material
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CourseMaterial $material)
    {
        $request->validate([
            'title' => ['required'],
            'type' => ['required'],
            'external_link' => ['nullable', 'required_if:type,external_video,external_link'],
            'file' => ['nullable', 'file'],
        ]);
        $material->type = $request->type;
        $material->title = $request->title;
        $material->external_link = $request->external_link;
        $material->description = $request->description;
        $material->save();
        if ($request->hasFile('file')) {
            if ($material->file) {
                $material->file()->delete();
            }
            $fileController = new FilesController();
            $file = $fileController->store([
                'file' => $request->file('file'),
                'name' => $material->title,
                'category' => 'course_material',
                'record_id' => $material->id,
            ],[],'public','media');
        }

        activity()
            ->performedOn($material)
            ->log('Update Course Material');
        return redirect()->route('courses.materials.index', [$material->course_id])->with('success', 'Updated Material successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CourseMaterial $material
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CourseMaterial $material)
    {
        $material->file()->delete();
        $material->delete();

        activity()
            ->performedOn($material)
            ->log('Delete Course Material');
        return redirect()->route('courses.materials.index', [$material->course_id])->with('success', 'Material deleted successfully.');

    }
}
