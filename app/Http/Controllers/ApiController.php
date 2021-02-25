<?php

namespace App\Http\Controllers;

use App\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ApiController extends GlobalController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apis = API::all();
        return view('private_area.apis')
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('users_levels',$this->getUsersLevels())
            ->with('integrate_list',$this->getApiIntegrateList())
            ->with('alert_types',$this->getApiAlertTypes())
            ->with('apis',$apis);

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
        //dd($request);
        $api = new Api();
        $api->system_name = $request->integrate;
        //set default values:
        $api->alert_type=$request->alert_type;
        $api->port=0;
        $api->virtual_input=0;
        $api->ip_address=0;
        $api->password=0;
        $api->username=0;
        $api->enabled=0;
        //Check system:
        if (strtolower($request->integrate)=="axis") {
            $api->virtual_input=$request->input_port; 
        } else {
            $api->port=$request->port;
            $api->ip_address=$request->ip;
            $api->password=$request->password;
            $api->username=$request->username;
        }
        $api->save();
        return redirect()->route('api.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function show(Api $api)
    {
        //Use this function to get a json object with an API item from a Ajax call in apis.blade view
        return Response::json($api);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function edit(Api $api)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Api $api)
    {
        $api->system_name = $request->integrate;
        //set default values:
        $api->alert_type="VIP";
        $api->port=0;
        $api->virtual_input=0;
        $api->ip_address=0;
        $api->password=0;
        $api->username=0;
        $api->enabled=0;
        //Check system:
        if (strtolower($request->integrate)=="axis") {
            $api->alert_type=$request->alert_type;
            $api->virtual_input=$request->input_port;
            
        } else {
            $api->port=$request->port;
            $api->ip_address=$request->ip;
            $api->password=$request->password;
            $api->username=$request->username;
        }
        $api->update();
        return redirect()->route('api.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Api  $api
     * @return \Illuminate\Http\Response
     */
    public function destroy(Api $api)
    {
        if (isset($api) && $api->id>0) $api->delete();
        return redirect()->route('api.index');
    }

    public function enableApi($api_id) {
        $api = Api::find($api_id);
        //Change current status:
        if ($api->enabled==0) { 
            //Not enabled, change to enable
            $api->enabled=1;
        } else { 
            //Enabled, change to disabled:
            $api->enabled=0;  
        }
        $api->update();
        return redirect()->route('api.index');
    }
}
