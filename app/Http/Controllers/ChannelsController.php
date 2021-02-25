<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Channel;
use App\Camera;
use App\Setting;

class ChannelsController extends GlobalController
{
    const CH_DEF_MIN_FACE_SIZE = 30;
    const CH_DEF_MAX_FACE_SIZE = 500;
    const CH_DEF_TRACKER_QUALITY = 60;
    const CH_DEF_MATCH_THRESHOLD = 75;

    const CH_CHECK_FR = "FR";
    const CH_CHECK_FF = "FF";
    const CH_CHECK_NR = "NR";
    const CH_CHECK_VR = "VR";

    const CH_IMG_SCREEN_WIDTH = 640;
    const CH_IMG_SCREEN_HEIGHT = 360;

     /* Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->showChannelsView();
    }

    private function showChannelsView($msg_type=null,$msg_text=null) {
        $channels = Channel::orderBy('id', 'asc')->get();
        $cameras = Camera::orderBy('id', 'asc')->get();
        //Get Camera limit from settings table:
        $settings = Setting::find(1);
        return view('private_area.channels')
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('channelTypes',$this->getChannelTypes())
            ->with('settings',$settings)
            ->with('cameras',$cameras)
            ->with('channels',$channels)
            ->with('msg_type',$msg_type)
            ->with('msg_text',$msg_text);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //TODO: Crear un channel con ID 0 y vals por defecto!!!!!!!!!!
        $channel = new Channel();
        $channel->id = 0;
        $camera_url = null; //asset('channel_area.jpg');
        $cameras = Camera::orderBy('id', 'asc')->get();
        //Get Camera limit from settings table:
        $settings = Setting::find(1);
        $ch_usb_id = $this->checkCameraIP(0);
        return view('private_area.channel_features')
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('channelTypes',$this->getChannelTypes())
            ->with('camera_url',$camera_url)
            ->with('isCameraIP',$ch_usb_id>0)
            ->with('cameraUSB_id',$ch_usb_id)
            ->with('channel',$channel)
            ->with('check_FR',"checked")
            ->with('check_FF',"checked")
            ->with('check_NR',"")
            ->with('check_VR',"")
            ->with('settings',$settings)
            ->with('ch_def_min_face_size',self::CH_DEF_MIN_FACE_SIZE)
            ->with('ch_def_max_face_size',self::CH_DEF_MAX_FACE_SIZE)
            ->with('ch_def_tracker_quality',self::CH_DEF_TRACKER_QUALITY)
            ->with('ch_def_match_threshold',self::CH_DEF_MATCH_THRESHOLD)
            ->with('hasCoords',false)
            ->with('ch_img_screen_width',self::CH_IMG_SCREEN_WIDTH)
            ->with('ch_img_screen_height',self::CH_IMG_SCREEN_HEIGHT)
            ->with('cameras',$cameras);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $ch = new Channel();
        if ($request->ch_type==0) { //IP camera features
            //Check if IP address already exists:
            if ($this->checkCameraIP($request->ch_ip)<0) {
                $ch->ip = $request->ch_ip;
                $ch->portNum = $request->ch_port;
                $ch->username = $request->ch_username;
                $ch->password = $request->ch_password;
                $ch->cameraNum = $request->ch_number;
                $ch->camera_id = $request->ch_model;
            } else {
                $this->showChannelsView(-1,"IP address already exists.");
            }
        } else {
            //Only can be added one USB Camera (IP is 0), so check if already exist one:
            if ($this->checkCameraIP(0)<0) {
                $ch->ip = 0;
                $ch->portNum = 0;
                $ch->username = 0;
                $ch->password = 0;
                $ch->cameraNum = 0;
                $ch->camera_id = 1;
            } else {
                $this->showChannelsView(-1,"USB camera already exists.");
            }
        }

        //Common features:
        if ($this->checkCameraDescription($request->ch_description)<0) 
            $ch->description = $request->ch_description;
        else {
                $this->showChannelsView(-1,"Camera description already exists.");
            }
        //Numeric fields default values:
        $ch->minFaceSize = self::CH_DEF_MIN_FACE_SIZE;
        $ch->maxFaceSize = self::CH_DEF_MAX_FACE_SIZE;
        $ch->matchPercentage = self::CH_DEF_MATCH_THRESHOLD;
        $ch->trackerQuality = self::CH_DEF_TRACKER_QUALITY;

        //Check if user set valid values:
        if (is_int($request->ch_min_face)) $ch->minFaceSize = $request->ch_min_face;
        if (is_int($request->ch_max_face)) $ch->maxFaceSize = $request->ch_max_face;
        if (is_int($request->ch_match)) $ch->matchPercentage = $request->ch_match;
        if (is_int($request->ch_tracker)) $ch->trackerQuality = $request->ch_tracker;

        //Check filters:
        $usages="";
        if (isset($request->ch_enable_fr)) $usages.=self::CH_CHECK_FR.";";
        if (isset($request->ch_enable_ff)) $usages.=self::CH_CHECK_FF.";";
        if (isset($request->ch_enable_nr)) $usages.=self::CH_CHECK_NR.";";
        if (isset($request->ch_enable_vr)) $usages.=self::CH_CHECK_VR.";";
        $ch->usages = $usages;

        //Channel image area:
        $ch->coordinates = $this->coordinatesFormatToDB($request->coordinates,self::CH_IMG_SCREEN_WIDTH,self::CH_IMG_SCREEN_HEIGHT);

        //Required by the insert query:
        $ch->maxFaces = 0;
        $ch->enabled = 0;

        $ch->save();

        return redirect()->route('channels.index');
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
        $channel = Channel::find($id);
        $isCameraIP = false;
        $ch_usb_id = -1;
        if ($channel->ip == "0") {
            //USB Camera
            $ch_usb_id = $channel->id;
        } else  {
            //IP Camera
            $isCameraIP = true;
            //check if exist an USB camera and get ID
            $ch_usb_id = $this->checkCameraIP(0,$channel->id);
        }
        $cameras = Camera::orderBy('id', 'asc')->get();
        $camera = Camera::find($channel->camera_id);
        $camera_url = null; //asset('channel_area.jpg');
        if ($isCameraIP && $camera->id>0 && strlen($camera->http_url)>0) {
            $camera_url = "http://".$channel->username.":".$channel->password."@".$channel->ip.$camera->http_url;
            //list($width, $height, $type, $attr) = getimagesize($camera_url);
            //dd("W: $width H: $height");
        }
            //////////////////////Tests to get the camera size:

            /*
            //$camera_url = "http://".base64_encode($channel->username).":".base64_encode($channel->password)."@".$channel->ip."/axis-cgi/imagesize.cgi?camera=1";
            $camera_url = "http://".$channel->username.":".$channel->password."@".$channel->ip."/axis-cgi/imagesize.cgi?camera=1";
            echo $camera_url;
            $ch = curl_init($camera_url);
            $headers = array(
                'Content-Type:application/json',
                'Authorization: Basic '. base64_encode($channel->username.":".$channel->password) // <---
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERPWD, base64_encode($channel->username.":".$channel->password));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($ch);
            curl_close($ch);
            dd($return);
            */
            /////////////////
            //Other test>
            /*
            $login = $channel->username;
            $password = $channel->password;
            $url = 'http://70.28.14.215/axis-cgi/imagesize.cgi?camera=1';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            $result = curl_exec($ch);
            curl_close($ch);  
            dd($result);
            */

        ////////////////////////////////////
        
        if (strpos($channel->usages, self::CH_CHECK_FR) !== false) $check_FR="checked"; else $check_FR="";
        if (strpos($channel->usages, self::CH_CHECK_FF) !== false) $check_FF="checked"; else $check_FF="";
        if (strpos($channel->usages, self::CH_CHECK_NR) !== false) $check_NR="checked"; else $check_NR="";
        if (strpos($channel->usages, self::CH_CHECK_VR) !== false) $check_VR="checked"; else $check_VR="";
        //Get Camera limit from settings table:
        $settings = Setting::find(1);

        //Check if has area selected coordinates:
        $hasCoords=false;
        $coords=null;
        $area_names = array();
        if (strpos($channel->coordinates, ',') !== false) {
            $hasCoords=true;
            $coords = $this->coordinatesFormatFromDB($channel->coordinates,self::CH_IMG_SCREEN_WIDTH,self::CH_IMG_SCREEN_HEIGHT);
            $area_names = explode(',', $channel->names);
        }

        //Channel camera image size and scale to show area selection:
        //TODO: Pending define if get these values from database, meanwhile default values:
        $ch_img_real_width = self::CH_IMG_REAL_WIDTH;
        $ch_img_real_height = self::CH_IMG_REAL_HEIGHT;

        //$coords = "58,72,76,251,335,322,433,94";

        //Return view:
        return view('private_area.channel_features')
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('channelTypes',$this->getChannelTypes())
            ->with('camera_url',$camera_url)
            ->with('isCameraIP',$isCameraIP)
            ->with('cameraUSB_id',$ch_usb_id)
            ->with('channel',$channel)
            ->with('check_FR',$check_FR)
            ->with('check_FF',$check_FF)
            ->with('check_NR',$check_NR)
            ->with('check_VR',$check_VR)
            ->with('settings',$settings)
            ->with('ch_def_min_face_size',self::CH_DEF_MIN_FACE_SIZE)
            ->with('ch_def_max_face_size',self::CH_DEF_MAX_FACE_SIZE)
            ->with('ch_def_tracker_quality',self::CH_DEF_TRACKER_QUALITY)
            ->with('ch_def_match_threshold',self::CH_DEF_MATCH_THRESHOLD)
            ->with('hasCoords',$hasCoords)
            ->with('coords',$coords)
            ->with('area_names',$area_names)
            ->with('ch_img_real_width',$ch_img_real_width)
            ->with('ch_img_real_height',$ch_img_real_height)
            ->with('ch_img_screen_width',self::CH_IMG_SCREEN_WIDTH)
            ->with('ch_img_screen_height',self::CH_IMG_SCREEN_HEIGHT)
            ->with('cameras',$cameras);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $ch = Channel::find($id);
        if ($request->ch_type==0) { //IP camera features
            //Check if IP address already exists related to another item:
            if ($this->checkCameraIP($request->ch_ip,$ch->id)<0) {
                $ch->ip = $request->ch_ip;
                $ch->portNum = $request->ch_port;
                $ch->username = $request->ch_username;
                $ch->password = $request->ch_password;
                $ch->cameraNum = $request->ch_number;
                $ch->camera_id = $request->ch_model;
            } else {
                $this->showChannelsView(-1,"IP address already exists.");
            }
        } else {
            //Only can be added one USB Camera (IP is 0), so check if already exist one:
            if ($this->checkCameraIP(0,$ch->id)<0) {
                $ch->ip = 0;
                $ch->portNum = 0;
                $ch->username = 0;
                $ch->password = 0;
                $ch->cameraNum = 0;
                $ch->camera_id = 1;
            } else {
                $this->showChannelsView(-1,"USB camera already exists.");
            }
        }

        //Common features:
        if ($this->checkCameraDescription($request->ch_description,$ch->id)<0) 
            $ch->description = $request->ch_description;
        else {
                $this->showChannelsView(-1,"Camera description already exists.");
            }

        //Check if user set valid values:
        if (is_int($request->ch_min_face)) $ch->minFaceSize = $request->ch_min_face;
        if (is_int($request->ch_max_face)) $ch->maxFaceSize = $request->ch_max_face;
        if (is_int($request->ch_match)) $ch->matchPercentage = $request->ch_match;
        if (is_int($request->ch_tracker)) $ch->trackerQuality = $request->ch_tracker;

        //Check filters:
        $usages="";
        if (isset($request->ch_enable_fr)) $usages.=self::CH_CHECK_FR.";";
        if (isset($request->ch_enable_ff)) $usages.=self::CH_CHECK_FF.";";
        if (isset($request->ch_enable_nr)) $usages.=self::CH_CHECK_NR.";";
        if (isset($request->ch_enable_vr)) $usages.=self::CH_CHECK_VR.";";
        $ch->usages = $usages;

        //Channel image areas:
        $ch->coordinates = $this->coordinatesFormatToDB($request->list_coords,self::CH_IMG_SCREEN_WIDTH,self::CH_IMG_SCREEN_HEIGHT,$request->ch_img_real_width,$request->ch_img_real_height);
        if ($request->list_areas)
            $ch->names = $request->list_areas;
        else
            $ch->names = "";
        $ch->update();

        return redirect()->route('channels.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $channel = Channel::find($id);
        $channel->delete();
        return redirect()->route('channels.index');
    }

    public function enableChannel($channel_id) {
        $channel = Channel::find($channel_id);
        //Change current status:
        if ($channel->enabled==0) { //Not enabled, change to enable
            //Check number of channels already enabled:
            $enabled_channels = Channel::where('enabled','=',1)->count();
            //Get Camera limit from settings table:
            $settings = Setting::find(1);
            if ($enabled_channels<$settings->camera_limit) {
                $channel->enabled=1;
                $channel->update();
            } 
        } else { //Enabled, change to disabled:
            $channel->enabled=0;
            $channel->update();
        }
        return redirect()->route('channels.index');
    }

    public function enableFaceFilter($channel_id) {
        $channel = Channel::find($channel_id);
        //Change current status:
        if ($channel->filter_flag==0) { //Not enabled, change to enable
            $channel->filter_flag=1;
        } else { //Enabled, change to disabled:
            $channel->filter_flag=0;
        }
        $channel->update();
        return redirect()->route('channels.index');
    }

    //Validation functions:
    private function checkCameraIP($ip, $ch_id=0) {
        $res = -1;
        $channel = Channel::where('ip','like',$ip)
            ->where('id','<>',$ch_id)
            ->first();
        if (isset($channel) && ($channel->id>0)) {
            $res = $channel->id;
        }
        return $res;
    }
    private function checkCameraDescription($description, $ch_id=0) {
        $res = -1;
        $channel = Channel::where('description','like',"$description")
            ->where('id','<>',$ch_id)
            ->first();
        if (isset($channel) && ($channel->id>0)) {
            $res = $channel->id;
        }
        return $res;
    }

    public function checkIPValue(Request $request) {
        //IP value must be unique:
        return $this->checkCameraIP($request->ip,$request->channel_id);
    }

    public function checkDescription(Request $request) {
        //Description value must be unique:
        return $this->checkCameraDescription($request->description,$request->channel_id);
    }
}
