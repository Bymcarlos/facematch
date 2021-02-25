<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Channel;
use App\Brightsign;
use App\Brightsignfag;
use App\Brightsignmag;
use Auth;

class BrightsignController extends GlobalController
{
    public function index() {
    	$brightsign = Brightsign::find(1);
    	$channel = Channel::find($brightsign->channel_id);
        $channels = Channel::all();
    	$brightsignfag = Brightsignfag::orderBy('minFemaleAge','Asc')->get();
    	$brightsignmag = Brightsignmag::orderBy('minMaleAge','Asc')->get();
	    return view('private_area.brightsign')
	        ->with('engine_status',$this->checkEngineStatus())
	        ->with('brightsign_enabled',$this->checkBrightSignDevice())
	        ->with('brightsignfag',$brightsignfag)
	        ->with('brightsignmag',$brightsignmag)
	        ->with('channel',$channel)
            ->with('channels',$channels)
            ->with('auth_user_level',$this->getUsersLevels()[Auth::user()->level])
	        ->with('brightsign',$brightsign);
    }

    public function store(Request $request)
    {
    	if ($request->type==0) {
    		//Female
    		$brightsignfag = new Brightsignfag();
    		$brightsignfag->minFemaleAge = $request->min_age;
    		$brightsignfag->maxFemaleAge = $request->max_age;
    		$brightsignfag->udpCommand = $request->udp_command;
    		$brightsignfag->femaleEmotion = "Neutral";
    		$brightsignfag->save();
    	} else {
    		$brightsignmag = new Brightsignmag();
    		$brightsignmag->minFemaleAge = $request->min_age;
    		$brightsignmag->maxFemaleAge = $request->max_age;
    		$brightsignmag->udpCommand = $request->udp_command;
    		$brightsignmag->femaleEmotion = "Neutral";
    		$brightsignmag->save();
    	}
        return redirect()->route('brightsign.index');
    }

    public function update(Request $request, $id)
    {
    	if ($request->type==0) {
    		//Female
    		$brightsignfag = Brightsignfag::find($id);
    		$brightsignfag->minFemaleAge = $request->min_age;
    		$brightsignfag->maxFemaleAge = $request->max_age;
    		$brightsignfag->udpCommand = $request->udp_command;
    		$brightsignfag->femaleEmotion = "Neutral";
    		$brightsignfag->update();
    	} else {
    		$brightsignmag = Brightsignmag::find($id);
    		$brightsignmag->minFemaleAge = $request->min_age;
    		$brightsignmag->maxFemaleAge = $request->max_age;
    		$brightsignmag->udpCommand = $request->udp_command;
    		$brightsignmag->femaleEmotion = "Neutral";
    		$brightsignmag->update();
    	}
        return redirect()->route('brightsign.index');
    }

    public function destroy(Request $request, $id)
    {
    	if ($request->type==0) {
    		//Female
    		$brightsignfag = Brightsignfag::find($id);
    		$brightsignfag->delete();
    	} else {
    		$brightsignmag = Brightsignmag::find($id);
    		$brightsignmag->delete();
    	}
        return redirect()->route('brightsign.index');
    }

    public function fieldValueUpdate(Request $request) {
        $field = $request->field;
        $brightsign = Brightsign::find(1);
        $brightsign->$field = $request->value;
        $brightsign->update();
        if (isset($request->ws))
            return $brightsign->$field;
        else
           return redirect()->route('brightsign.index');
    }

    public function deviceUpdate(Request $request) {
        $brightsign = Brightsign::find(1);
        $brightsign->channel_id = $request->channel_id;
        $brightsign->ip = $request->ip;
        $brightsign->udpPort = $request->udpPort;
        $brightsign->update();
        return redirect()->route('brightsign.index');
    }
}
