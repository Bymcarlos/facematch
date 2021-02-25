<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;
use App\Channel;
use App\Camera;
use App\Vagfloorarea;
use Auth;

class SettingsController extends GlobalController {
    const CH_CHECK_S = "S"; //Alert System
    const CH_CHECK_E = "E"; //Alert Email
    const CH_CHECK_T = "T"; //Alert Telegram

    //Camera preview screen sizes: 640*360 / 960*540
    const CH_IMG_SCREEN_WIDTH = 960;
    const CH_IMG_SCREEN_HEIGHT = 540;


    public function index() {
    	$settings = Setting::find(1)->first();
    	$check_states = array();
    	$check_states = [0 => '',1 => 'checked'];

        if (strpos($settings->nonfaceAlertOption, self::CH_CHECK_S) !== false) $check_nonface_alert_s="checked"; else $check_nonface_alert_s="";
        if (strpos($settings->nonfaceAlertOption, self::CH_CHECK_E) !== false) $check_nonface_alert_e="checked"; else $check_nonface_alert_e="";
        if (strpos($settings->nonfaceAlertOption, self::CH_CHECK_T) !== false) $check_nonface_alert_t="checked"; else $check_nonface_alert_t="";

        if (strpos($settings->vipAlert, self::CH_CHECK_S) !== false) $check_vipAlert_s="checked"; else $check_vipAlert_s="";
        if (strpos($settings->vipAlert, self::CH_CHECK_E) !== false) $check_vipAlert_e="checked"; else $check_vipAlert_e="";
        if (strpos($settings->vipAlert, self::CH_CHECK_T) !== false) $check_vipAlert_t="checked"; else $check_vipAlert_t="";

        if (strpos($settings->blacklistAlert, self::CH_CHECK_S) !== false) $check_blacklistAlert_s="checked"; else $check_blacklistAlert_s="";
        if (strpos($settings->blacklistAlert, self::CH_CHECK_E) !== false) $check_blacklistAlert_e="checked"; else $check_blacklistAlert_e="";
        if (strpos($settings->blacklistAlert, self::CH_CHECK_T) !== false) $check_blacklistAlert_t="checked"; else $check_blacklistAlert_t="";

        if (strpos($settings->staffAlert, self::CH_CHECK_S) !== false) $check_staffAlert_s="checked"; else $check_staffAlert_s="";
        if (strpos($settings->staffAlert, self::CH_CHECK_E) !== false) $check_staffAlert_e="checked"; else $check_staffAlert_e="";
        if (strpos($settings->staffAlert, self::CH_CHECK_T) !== false) $check_staffAlert_t="checked"; else $check_staffAlert_t="";

        if (strpos($settings->vagrancyAlertOption, self::CH_CHECK_S) !== false) $check_vagrancyAlertOption_s="checked"; else $check_vagrancyAlertOption_s="";
        if (strpos($settings->vagrancyAlertOption, self::CH_CHECK_E) !== false) $check_vagrancyAlertOption_e="checked"; else $check_vagrancyAlertOption_e="";
        if (strpos($settings->vagrancyAlertOption, self::CH_CHECK_T) !== false) $check_vagrancyAlertOption_t="checked"; else $check_vagrancyAlertOption_t="";

        $channels = Channel::where('usages','like','%VR%')->get();
        $vagfloorareas = Vagfloorarea::all()->keyBy('channel_id');
    	return view('private_area.settings.alerts')
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('check_states',$check_states)
            ->with('check_nonface_alert_s',$check_nonface_alert_s)
            ->with('check_nonface_alert_e',$check_nonface_alert_e)
            ->with('check_nonface_alert_t',$check_nonface_alert_t)
            ->with('check_vipAlert_s',$check_vipAlert_s)
            ->with('check_vipAlert_e',$check_vipAlert_e)
            ->with('check_vipAlert_t',$check_vipAlert_t)
            ->with('check_blacklistAlert_s',$check_blacklistAlert_s)
            ->with('check_blacklistAlert_e',$check_blacklistAlert_e)
            ->with('check_blacklistAlert_t',$check_blacklistAlert_t)
            ->with('check_staffAlert_s',$check_staffAlert_s)
            ->with('check_staffAlert_e',$check_staffAlert_e)
            ->with('check_staffAlert_t',$check_staffAlert_t)
            ->with('check_vagrancyAlertOption_s',$check_vagrancyAlertOption_s)
            ->with('check_vagrancyAlertOption_e',$check_vagrancyAlertOption_e)
            ->with('check_vagrancyAlertOption_t',$check_vagrancyAlertOption_t)
            ->with('auth_user_level',$this->getUsersLevels()[Auth::user()->level])
            ->with('channels',$channels)
            ->with('vagfloorareas',$vagfloorareas)
            ->with('settings',$settings);
    }

    public function changeState(Request $request) {
    	$field = $request->field;
    	$settings = Setting::find(1)->first();

    	if ($settings->$field == 0)
            $settings->$field = 1;
        else
            $settings->$field = 0;
        $settings->update();

    	return $settings->$field;
    }

    public function changeAlertState(Request $request) {

        $list=array();
        if ($request->alert_s==1) $list[]=self::CH_CHECK_S;
        if ($request->alert_e==1) $list[]=self::CH_CHECK_E;
        if ($request->alert_t==1) $list[]=self::CH_CHECK_T;
        $value = implode (";", $list);
        
        $field = $request->field;
        $settings = Setting::find(1)->first();
        $settings->$field = $value;
        $settings->update();

        return $settings->$field;
    }

    public function cleanupTime(Request $request) {
    	$settings = Setting::find(1)->first();
    	$settings->cleanup_time = date("H:i:s", strtotime($request->value));
    	$settings->update();

    	return redirect()->route('settings.index');
    }

    public function numericValueUpdate(Request $request) {
    	$field = $request->field;
    	$settings = Setting::find(1)->first();
    	$settings->$field = $request->value;
    	$settings->update();
        if (isset($request->ws))
            return $settings->$field;
        else
    	   return redirect()->route('settings.index');
    }

    public function fieldInfo(Request $request) {
        $field = $request->field;
        $info = __("settings_info.$field");
        return $info;
    }

    public function channelRunningTimeSave(Request $request) {
        $channels = Channel::where('usages','like','%VR%')->get();
        foreach ($channels as $channel) {
            $field = "weekday_start_time_".$channel->id;
            $wk_st = $request->$field;
            $field = "weekday_end_time_".$channel->id;
            $wk_et = $request->$field;
            $field = "weekend_start_time_".$channel->id;
            $we_st = $request->$field;
            $field = "weekend_end_time_".$channel->id;
            $we_et = $request->$field;
            $vagfloorarea = Vagfloorarea::where('channel_id','=',$channel->id)->first();
            if ($vagfloorarea) {
                $vagfloorarea->weekdayStartTime=$wk_st;
                $vagfloorarea->weekdayEndTime=$wk_et;
                $vagfloorarea->weekendStartTime=$we_st;
                $vagfloorarea->weekendEndTime=$we_et;
                $vagfloorarea->update();
            } else {
                $vagfloorarea = new Vagfloorarea();
                $vagfloorarea->channel_id=$channel->id;
                $vagfloorarea->weekdayStartTime=$wk_st;
                $vagfloorarea->weekdayEndTime=$wk_et;
                $vagfloorarea->weekendStartTime=$we_st;
                $vagfloorarea->weekendEndTime=$we_et;
                $vagfloorarea->coordinates="0";
                $vagfloorarea->save();
            }
        }
        return redirect()->route('settings.index');
    }

    public function channelFloorAreaShow(Request $request) {
        $channel = Channel::find($request->channel);
        $isCameraIP = false;
        $ch_usb_id = -1;
        if ($channel->ip == "0") {
            //USB Camera
            $ch_usb_id = $channel->id;
        } else  {
            //IP Camera
            $isCameraIP = true;
            //check if exist an USB camera and get ID
            //$ch_usb_id = $this->checkCameraIP(0,$channel->id);
        }
        $camera = Camera::find($channel->camera_id);
        $camera_url = null; //asset('channel_area.jpg');
        if ($isCameraIP && $camera->id>0 && strlen($camera->http_url)>0) {
            $camera_url = "http://".$channel->username.":".$channel->password."@".$channel->ip.$camera->http_url;
            //list($width, $height, $type, $attr) = getimagesize($camera_url);
            //dd("W: $width H: $height");
        }

        //Check if has area floor area coordinates:
        $hasCoords=false;
        $coords="";
        $area_names = array();
        $vagfloorarea = Vagfloorarea::where('channel_id','=',$channel->id)->first();
        if (($vagfloorarea) && (strpos($vagfloorarea->coordinates, ',') !== false)) {
            $hasCoords=true;
            $coords = $this->coordinatesFormatFromDB($vagfloorarea->coordinates,self::CH_IMG_SCREEN_WIDTH,self::CH_IMG_SCREEN_HEIGHT);
            if ($vagfloorarea->names) $area_names = explode(',', $vagfloorarea->names);
        }
        //dd($coords);

        //Channel camera image size and scale to show floor area selection:
        //TODO: Pending define if get these values from database, meanwhile default values:
        $ch_img_real_width = self::CH_IMG_REAL_WIDTH;
        $ch_img_real_height = self::CH_IMG_REAL_HEIGHT;

        return view('private_area.settings.floor_area')
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('camera_url',$camera_url)
            ->with('vagfloorarea',$vagfloorarea)
            ->with('hasCoords',$hasCoords)
            ->with('coords',$coords)
            ->with('area_names',$area_names)
            ->with('ch_img_real_width',$ch_img_real_width)
            ->with('ch_img_real_height',$ch_img_real_height)
            ->with('ch_img_screen_width',self::CH_IMG_SCREEN_WIDTH)
            ->with('ch_img_screen_height',self::CH_IMG_SCREEN_HEIGHT)
            ->with('channel',$channel);
    }

    public function channelFloorAreaSave(Request $request) {
        //Floor area selected coordinates:

        $str_coords = $this->coordinatesFormatToDB($request->list_coords,self::CH_IMG_SCREEN_WIDTH,self::CH_IMG_SCREEN_HEIGHT,$request->ch_img_real_width,$request->ch_img_real_height);
        $area_names = "";
        if ($request->list_areas) $area_names = $request->list_areas;

        $vagfloorarea = Vagfloorarea::where('channel_id','=',$request->channel_id)->first();
        if ($vagfloorarea) {
            $vagfloorarea->coordinates = $str_coords;
            $vagfloorarea->names = $area_names;
            $vagfloorarea->update();
        } else {
            $defaultTime = date('h:i', strtotime("00:00"));
            $vagfloorarea = new Vagfloorarea();
            $vagfloorarea->channel_id = $request->channel_id;
            $vagfloorarea->weekdayEndTime = $defaultTime;
            $vagfloorarea->weekdayStartTime = $defaultTime;
            $vagfloorarea->weekendEndTime = $defaultTime;
            $vagfloorarea->weekendStartTime = $defaultTime;
            $vagfloorarea->coordinates = $str_coords;
            $vagfloorarea->names = $area_names;
            $vagfloorarea->save();
        }
        return redirect()->route('settings.index');
    }
}
