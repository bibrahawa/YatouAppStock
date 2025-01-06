<?php

namespace App\Http\Controllers;

use URL;
use File;
use Session;
use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Exceptions\ValidationException;
use Illuminate\Support\Str;


class UserController extends Controller
{
    private $searchParams = ['name', 'email'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {
        if (auth()->user()->can('user.manage')) {
            $users = User::orderBy('first_name', 'asc');
            if ($request->get('name')) {
                $users->where(function($q) use($request) {
                    $q->where('first_name', 'LIKE', '%' . $request->get('name') . '%')
                      ->orWhere('last_name', 'LIKE', '%' . $request->get('name') . '%');
                });
            }
            if ($request->get('email')) {
                $users->where('email', 'LIKE', '%' . $request->get('email') . '%');
            }
        } else {
            $users = User::orderBy('first_name', 'asc')->whereId(auth()->user()->id);
        }
        return view('users.index')->withUsers($users->paginate(20));
    }

    public function postIndex(Request $request)
    {
        $params = array_filter($request->only($this->searchParams));
        return redirect()->action([UserController::class, 'getIndex'], $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNewUser()
    {
        $user = new User;
        $roles = auth()->user()->can('admins.create') ? Role::all() : Role::where('name', '!=', 'Super User')->get();
        $warehouses = Warehouse::all();
        return view('users.form', compact('roles', 'user', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function postUser(UserRequest $request, User $user)
    {

        if ($request->id){
            $user = User::find($request->id);
        }

        $user->fill($request->only(['first_name', 'last_name', 'email', 'address', 'phone', 'warehouse_id']));
        if ($request->get('password')) {
            $user->password = Hash::make($request->get('password'));
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::random(12) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/uploads/profiles/'), $filename);
            $user->image = $filename;
        }

        $user->save();

        if ($request->get('role')) {
            $user->roles()->sync($request->get('role'));
        }

        return redirect()->route('user.index')->withMessage(trans('core.changes_saved'));
    }

    public function postProfile(Request $request)
    {
        $user = User::find($request->get('user_id'));
        $user->fill($request->only(['first_name', 'last_name', 'email', 'address', 'phone']));
        $user->save();

        return redirect()->route('user.profile')->withMessage(trans('core.changes_saved'));
    }

    public function viewProfile()
    {
        return view('users.profile')->withUser(auth()->user());
    }

    public function getEditUser(User $user)
    {
        $roles = Role::where('name', '!=', 'Super User')->get();
        $warehouses = Warehouse::all();
        return view('users.form', compact('roles', 'user', 'warehouses'));
    }

    public function lock()
    {
        session(['lockedOutUser' => auth()->user()->id, 'lockedRoute' => url()->previous()]);
        auth()->logout();
        return redirect()->route('locked');
    }

    public function unlock(Request $request)
    {
        $intended = session('lockedRoute');
        if (auth()->attempt($request->only('email', 'password'))) {
            return redirect()->intended($intended);
        }
        return redirect()->back()->withMessage(trans('core.wrong_password'));
    }

    public function locked()
    {
        $user = User::find(session('lockedOutUser'));
        return $user ? view('users.locked')->withUser($user) : redirect()->to('/login');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->to('/');
    }

    public function changePassword(Request $request)
    {
        $user = User::find($request->get('user_id'));
        if ($request->get('password') === $request->get('confirm_password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
            return redirect()->back()->withMessage(trans('core.changes_saved'));
        }
        return redirect()->back()->withMessage(trans('core.oops'));
    }

    public function verifyOldPassword(Request $request)
    {
        if (Hash::check($request->get('password'), auth()->user()->password)) {
            return response()->json(['value' => true, 'code' => 200]);
        }
        throw new ValidationException('Passwords do not match');
    }

    public function postStatus(Request $request)
    {
        $user = User::findOrFail($request->get('user_id'));
        $user->inactive = !$user->inactive;
        $user->save();
        return redirect()->route('user.index')->withSuccess(trans('core.changes_saved'));
    }
}
