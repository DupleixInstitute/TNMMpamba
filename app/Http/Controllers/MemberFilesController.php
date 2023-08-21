<?php

namespace App\Http\Controllers;

use App\Models\CourseMaterial;
use App\Models\File;
use App\Models\Client;
use App\Models\LoanProductScoringAttribute;
use App\Models\MemberRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MemberFilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:members.files.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.files.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.files.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.files.destroy'])->only(['destroy']);
    }
    public function index(Client $member)
    {

        $files = File::where('record_id', $member->id)
            ->where('category', 'members')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Members/Files/Index', [
            'member' => $member,
            'files' => $files,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Client $member)
    {
        return Inertia::render('Members/Files/Create', [
            'member' => $member,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Client $member)
    {
        if (App::environment('demo')) {
            return redirect()->back()->with('error', 'Creating member files is not allowed in demo.');
        }
        $fileController = new FilesController();
        $file = $fileController->store([
            'file' => $request->file('file'),
            'name' => $request->name,
            'description' => $request->description,
            'category' => 'members',
            'record_id' => $member->id,
        ]);
        activity()
            ->performedOn($member)
            ->log('Create Member File');
        return redirect()->route('members.files.index', [$member->id])->with('success', 'Member File created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        $member = Client::find($file->record_id);

        return Inertia::render('Members/Files/Edit', [
            'member' => $member,
            'file' => $file,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        if (App::environment('demo')) {
            return redirect()->back()->with('error', 'Updating the demo member files is not allowed.');
        }
        $member = Client::find($file->record_id);
        $fileController = new FilesController();
        $file = $fileController->update($file->id,[
            'file' => $request->file('file'),
            'name' => $request->name,
            'description' => $request->description,
        ]);
        activity()
            ->performedOn($member)
            ->log('Update Member File');
        return redirect()->route('members.files.index', [$member->id])->with('success', 'Updated File successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        if (App::environment('demo')) {
            return redirect()->back()->with('error', 'Deleting the demo member files is not allowed.');
        }
        $fileController = new FilesController();
        $fileController->destroy($file->id);
        activity()
            ->performedOn($file)
            ->log('Delete Member File');
        return redirect()->route('members.files.index', [$file->record_id])->with('success', 'File deleted successfully.');

    }
}
