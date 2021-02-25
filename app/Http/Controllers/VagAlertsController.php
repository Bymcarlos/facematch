<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Channel;
use App\Knownface;
use App\Identified;
use App\Body;
use App\Frame;
use App\Vagidentified;

class VagAlertsController extends GlobalController
{
    public function listVagAlerts() {
        $_startTime=null;
        $_endTime=null;
        $_vagType=null;
        $_topColor=null;
        $_bottomColor=null;
        $_channel=null;
        $_listOrder=null;

        if (session('startTime')!=null) {
            $_startTime = session('startTime');
        }
        if (session('endTime')!=null) {
            $_endTime = session('endTime');
        }
        if (session('vagType')!=null) {
            $_vagType = session('vagType');
        }
        if (session('topColor')!=null) {
            $_topColor = session('topColor');
        }
        if (session('bottomColor')!=null) {
            $_bottomColor = session('bottomColor');
        }
        if (session('channel')!=null) {
            $_channel = session('channel');
        }
        if (session('listOrder')!=null) {
            $_listOrder = session('listOrder');
        }
        return $this->getVagAlerts($_startTime,$_endTime,$_vagType,$_topColor,$_bottomColor,$_channel,$_listOrder);
    }

    public function listVagAlertsWithFilters(Request $request) {
        $_startTime=null;
        $_endTime=null;
        $_vagType=null;
        $_topColor=null;
        $_bottomColor=null;
        $_channel=null;
        $_listOrder=null;

        if (isset($request->startTime)) {
            $_startTime = $request->startTime;
        }
        if (isset($request->endTime)) {
            $_endTime = $request->endTime;
        }
        if (isset($request->vagType) && ($request->vagType > GlobalController::ALL_VAG_TYPES )) {
            $_vagType = $request->vagType;
        }
        if (isset($request->topColor) && ($request->topColor > GlobalController::ALL_COLOR_RANGES )) {
            $_topColor = $request->topColor;
        }
        if (isset($request->bottomColor) && ($request->bottomColor > GlobalController::ALL_COLOR_RANGES )) {
            $_bottomColor = $request->bottomColor;
        }
        if (isset($request->channel) && ($request->channel> GlobalController::ALL_CHANNEL )) {
            $_channel = $request->channel;
        }
        if (isset($request->listOrder)) {
            $_listOrder = $request->listOrder;
        }
        return $this->getVagAlerts($_startTime,$_endTime,$_vagType,$_topColor,$_bottomColor,$_channel,$_listOrder);
    }

    public function listVagAlertsRemoveFilters() {
        $this->removeSessionFilters();
        return $this->getVagAlerts(null,null,null,null,null,null,null);
    }

    private function getVagAlerts($_startTime,$_endTime,$_vagType,$_topColor,$_bottomColor,$_channel,$_listOrder) {
        $startTime=GlobalController::START_TIME;
        $endTime=GlobalController::END_TIME;
        $minVagType=GlobalController::MIN_VAG_TYPE;
        $maxVagType=GlobalController::MAX_VAG_TYPE;
        $topColor=GlobalController::ALL_COLORS;
        $bottomColor=GlobalController::ALL_COLORS;
        $minChannel=GlobalController::MIN_CHANNEL;
        $maxChannel=GlobalController::MAX_CHANNEL;
        $listOrder=GlobalController::LIST_ORDER;

        $params = array();
        if (isset($_startTime)) {
            $startTime = date("Y-m-d H:i:s", strtotime($_startTime));
            $params["startTime"] = $_startTime;
            session(['startTime' => $_startTime]);
        }
        if (isset($_endTime)) {
            $endTime = date("Y-m-d H:i:s", strtotime($_endTime));
            $params["endTime"] = $_endTime;
            session(['endTime' => $_endTime]);
        }
        if (isset($_vagType) && ($_vagType > GlobalController::ALL_VAG_TYPES )) {
            $minVagType = $_vagType;
            if ($_vagType==GlobalController::MIN_VAG_TYPE) $maxVagType = $_vagType;
            $params["vagType"] = $_vagType;
            session(['vagType' => $_vagType]);
        }
        if (isset($_topColor) && ($_topColor > GlobalController::ALL_COLOR_RANGES )) {
            $topColor = $this->getBodyColors()[$_topColor];
            $params["topColor"] = $_topColor;
            session(['topColor' => $_topColor]);
        }
        if (isset($_bottomColor) && ($_bottomColor > GlobalController::ALL_COLOR_RANGES )) {
            $bottomColor = $this->getBodyColors()[$_bottomColor];
            $params["bottomColor"] = $_bottomColor;
            session(['bottomColor' => $_bottomColor]);
        }
        if (isset($_channel) && ($_channel> GlobalController::ALL_CHANNEL )) {
            $minChannel = $_channel;
            $maxChannel = $_channel;
            $params["channel"] = $_channel;
            session(['channel' => $_channel]);
        }
        if (isset($_listOrder)) {
            $listOrder = $_listOrder;
            $params["listOrder"] = $_listOrder;
            session(['listOrder' => $_listOrder]);
        }
        $items = $this->dbListVagAlerts($minChannel,$maxChannel,$startTime,$endTime,$minVagType,$maxVagType,$topColor,$bottomColor,$listOrder);
        //dd($items);
        //Calculate the area location to center the preview image:
        for($i=0;$i<count($items);$i++) {
            //Get real size of the picture (image live):
            $imgPath = public_path().'/Media/Frames/'.$items[$i]->img_name;
            if (file_exists($imgPath)) {
                list($img_width, $img_height, $img_type, $img_attr) = getimagesize($imgPath);
                $items[$i]->img_live_available=1;
                //Img preview width (will depends on real image width and the width of the area to select)  
                $ratio = $this->getImgPreviewWidth($items[$i]->width,$img_width)/$img_width;
                $items[$i]->img_live_width = number_format($img_width*$ratio,0);
                $items[$i]->img_live_height = number_format($img_height*$ratio,0);
                $items[$i]->img_live_posX = number_format($items[$i]->topLeftX/$img_width,2)*110;
                $items[$i]->img_live_posY = number_format($items[$i]->topLeftY/$img_height,2)*125;
            } else {
                $items[$i]->img_live_available=-1;
                $items[$i]->img_live_width = 0;
                $items[$i]->img_live_height = 0;
                $items[$i]->img_live_posX = 0;
                $items[$i]->img_live_posY = 0;
            }
        }
        //dd($items);

        $channels = Channel::all()->keyBy('id');
        
        //List for bodies related to the vagrancy alerts and frames with image and other fields:
        $bodies = array();
        $frames = array();
        for($i=0;$i<count($items);$i++) {
            $body = Body::find($items[$i]->firstBody_id);
            if ($body) {
                $bodies[$items[$i]->firstBody_id] = $body;
                $frame = Frame::find($body->frame_id);
                if ($frame) {
                    $frames[$body->frame_id] = $frame;
                    //Get real size of the picture (image captured):
                    $imgPath = public_path().'/Media/Frames/'.$frame->img_name;
                    if (file_exists($imgPath)) {
                        list($img_width, $img_height, $img_type, $img_attr) = getimagesize($imgPath);
                        $items[$i]->img_capt_available=1;
                        //$items[$i]->img_capt_name = $frame->img_name;
                        //Img preview width (will depends on real image width and the width of the area to select)  
                        $ratio = $this->getImgPreviewWidth($items[$i]->width,$img_width)/$img_width;
                        $items[$i]->img_capt_width = number_format($img_width*$ratio,0);
                        $items[$i]->img_capt_height = number_format($img_height*$ratio,0);
                        $items[$i]->img_capt_posX = number_format($body->topLeftX/$img_width,2)*110;
                        $items[$i]->img_capt_posY = number_format($body->topLeftY/$img_height,2)*125;
                    } else {
                        $items[$i]->img_capt_available=-1;
                        $items[$i]->img_capt_width = 0;
                        $items[$i]->img_capt_height = 0;
                        $items[$i]->img_capt_posX = 0;
                        $items[$i]->img_capt_posY = 0;
                    }
                    $items[$i]->CH_CAPT = $channels[$frame->channel_id]->description;
                }
            }
        }
        //dd($items);
        $channels = Channel::orderBy('description', 'asc')->get();
        return view('private_area.vagalerts') 
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('channels',$channels)
            ->with('bodies',$bodies)
            ->with('frames',$frames)
            ->with('listOrders',$this->getListOrders())
            ->with('alertIcons',$this->getAlertStateIcons())
            ->with('params',$params)
            ->with('vagTypes',$this->getVagrancyTypes())
            ->with('bodyColors',$this->getBodyColors())
            ->with('items',$items);
    }

    public function vagAlertStateUpdate(Request $request) {
        $res = 0;
        if (isset($request->alert_id) && isset($request->alert_action)) {
            $identified = Vagidentified::find($request->alert_id);
            if (isset($identified)) {
                switch ($request->alert_action) {
                    case -1:    //Reject
                        $identified->confirmed=10;
                        break;
                    case 1:    //Confirm
                        $identified->confirmed=20;
                        break;
                }
                $identified->update();
                $res = $identified->confirmed;
            }
        }
        //Return values: 0 (params missing or Identified item not found) -1 (unknown) 10: rejected, 20: confirmed
        return $res;
    }
}