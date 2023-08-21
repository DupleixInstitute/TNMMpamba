<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LoanProduct;
use App\Models\MemberUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class MemberLoginDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:members.create'])->only(['index', 'show']);
        $this->middleware(['permission:members.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.destroy'])->only(['destroy']);
    }

    public function index(Client $member)
    {
        $member->load(['user']);
        return Inertia::render('Members/LoginDetails/Index', [
            'member' => $member,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Client $member)
    {
        return Inertia::render('Members/LoginDetails/Create', [
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
            'existing_user' => ['required'],
            'user_id' => ['required_if:existing_user,true'],
            'first_name' => ['required_if:existing_user,false', 'string', 'max:255'],
            'last_name' => ['required_if:existing_user,false', 'string', 'max:255'],
            'email' => ['required_if:existing_user,false', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required_if:existing_user,false', 'confirmed'],
        ]);
        if ($request->existing_user) {
           $user=User::find($request->user_id);
            $user->member_id = $member->id;
            $user->save();
        } else {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'member_id' => $member->id,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole(Role::findByName('member'));
        }
        activity()
            ->performedOn($member)
            ->log('Create Member User');
        return redirect()->route('members.login_details.index', [$member->id])->with('success', 'Member user created successfully.');

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
    public function destroy(MemberUser $memberUser)
    {
        if (App::environment('demo')) {
            return redirect()->back()->with('error', 'Deleting the demo member note is not allowed.');
        }
        $memberUser->delete();
        activity()
            ->performedOn($memberUser)
            ->log('Delete Member User');
        return redirect()->route('members.login_details.index', [$memberUser->member_id])->with('success', 'Deleted successfully.');

    }
}
