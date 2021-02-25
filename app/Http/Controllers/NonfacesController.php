<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Channel;
use App\Frame;
use App\Nonfaceidentified;

class NonfacesController extends GlobalController
{
    public function listNonfaces() {
        $_startTime=null;
        $_endTime=null;
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
        return $this->getNonfaces($_startTime,$_endTime,$_topColor,$_bottomColor,$_channel,$_listOrder);
    }

    public function listNonfacesWithFilters(Request $request) {
        $_startTime=null;
        $_endTime=null;
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
        return $this->getNonfaces($_startTime,$_endTime,$_topColor,$_bottomColor,$_channel,$_listOrder);
    }

    public function listNonfacesRemoveFilters() {
        $this->removeSessionFilters();
        return $this->getNonfaces(null,null,null,null,null,null);
    }

    private function getNonfaces($_startTime,$_endTime,$_topColor,$_bottomColor,$_channel,$_listOrder) {
        $startTime=GlobalController::START_TIME;
        $endTime=GlobalController::END_TIME;
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
        $items = $this->dbListNonfaces($minChannel,$maxChannel,$startTime,$endTime,$topColor,$bottomColor,$listOrder);

        //Calculate the area location to center the preview image:
        for($i=0;$i<count($items);$i++) {
            //Get real size of the picture (image live):
            $imgPath = public_path().'/Media/Frames/'.$items[$i]->img_name;
            if (file_exists($imgPath)) {
                list($img_width, $img_height, $img_type, $img_attr) = getimagesize($imgPath);
                $items[$i]->img_available=1;
                //Img preview width (will depends on real image width and the width of the area to select)  
                $ratio = $this->getImgPreviewWidth($items[$i]->width,$img_width)/$img_width;
                $items[$i]->img_width = number_format($img_width*$ratio,0);
                $items[$i]->img_height = number_format($img_height*$ratio,0);
                $items[$i]->img_posX = number_format($items[$i]->topLeftX/$img_width,2)*110;
                $items[$i]->img_posY = number_format($items[$i]->topLeftY/$img_height,2)*125;
            } else {
                $items[$i]->img_available=-1;
                $items[$i]->img_width = 0;
                $items[$i]->img_height = 0;
                $items[$i]->img_posX = 0;
                $items[$i]->img_posY = 0;
            }
        }
        //dd($items);

        $channels = Channel::orderBy('description', 'asc')->get();
        return view('private_area.nonfaces') 
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('channels',$channels)
            ->with('listOrders',$this->getListOrders())
            ->with('alertIcons',$this->getAlertStateIcons())
            ->with('params',$params)
            ->with('bodyColors',$this->getBodyColors())
            ->with('items',$items);
    }

    public function nonfacesStateUpdate(Request $request) {
        $res = 0;
        if (isset($request->alert_id) && isset($request->alert_action)) {
            $identified = Nonfaceidentified::find($request->alert_id);
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