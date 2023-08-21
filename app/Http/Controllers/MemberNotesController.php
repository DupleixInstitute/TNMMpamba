<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LoanProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MemberNotesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:members.notes.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.notes.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.notes.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.notes.destroy'])->only(['destroy']);
    }
    public function index(Client $member)
    {
        $notes = LoanProduct::with(['createdBy'])
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Members/Notes/Index', [
            'member' => $member,
            'notes' => $notes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Client $member)
    {
        return Inertia::render('Members/Notes/Create', [
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
        $request->validate([
            'description' => ['required'],
        ]);
        $memberNote = new LoanProduct();
        $memberNote->created_by_id = Auth::id();
        $memberNote->member_id = $member->id;
        $memberNote->description = $request->description;
        $memberNote->visible_to_member = $request->visible_to_member ? 1 : 0;
        $memberNote->save();
        activity()
            ->performedOn($member)
            ->log('Create Member Note');
        return redirect()->route('members.notes.index', [$member->id])->with('success', 'Member Note created successfully.');

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
    public function edit(LoanProduct $memberNote)
    {
        $member = $memberNote->member;

        return Inertia::render('Members/Notes/Edit', [
            'member' => $member,
            'memberNote' => $memberNote,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoanProduct $memberNote)
    {
        if (App::environment('demo')) {
            return redirect()->back()->with('error', 'Updating the demo member note is not allowed.');
        }
        $member = $memberNote->member;
        $request->validate([
            'description' => ['required'],
        ]);
        $memberNote->description = $request->description;
        $memberNote->visible_to_member = $request->visible_to_member ? 1 : 0;
        $memberNote->save();

        activity()
            ->performedOn($member)
            ->log('Update Member Note');
        return redirect()->route('members.notes.index', [$member->id])->with('success', 'Member Note updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoanProduct $memberNote)
    {
        if (App::environment('demo')) {
            return redirect()->back()->with('error', 'Deleting the demo member note is not allowed.');
        }
        $memberNote->delete();
        activity()
            ->performedOn($memberNote)
            ->log('Delete Member Note');
        return redirect()->route('members.notes.index',[$memberNote->member_id])->with('success', 'Member deleted successfully.');

    }
}
