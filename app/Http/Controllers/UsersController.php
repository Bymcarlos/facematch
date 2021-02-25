<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class UsersController extends GlobalController {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth_user = User::find(Auth::user()->id);
        //if ($auth_user->hasAnyRole(['admin','manager']))
        $users = User::orderBy('username','asc')->get();
        return view('private_area.users')
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('users_levels',$this->getUsersLevels())
            ->with('auth_user',$auth_user)
            ->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->username = $request->username;
        $user->password = $request->password;
        $user->creation_datetime = date("Y-m-d H:i:s");
        $user->level = $request->level;

        $user->save();
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->password = $request->password;

        $user->update();
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('users.index');
    }

    //Validation functions:
    private function checkUsername($username, $user_id=0) {
        $res = -1;
        $user = User::where("username","like","$username")
            ->where('user_id','<>',$user_id)
            ->first();
        if (isset($user) && ($user->id>0)) {
            $res = $user->id;
        }
        return $res;
    }
    public function checkUsernameValue(Request $request) {
        //IP value must be unique:
        return $this->checkUsername($request->username,$request->user_id);
    }

    public function changeUserLevel(Request $request, $id) {
        $user = User::find($id);
        $user->level = $request->level;

        $user->update();
        return redirect()->route('users.index');
    }
}
