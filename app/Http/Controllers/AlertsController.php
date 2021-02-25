<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Channel;
use App\Knownface;
use App\Identified;

class AlertsController extends GlobalController
{
    public function listAlerts() {
        $_startTime=null;
        $_endTime=null;
        $_minAge=null;
        $_maxAge=null;
        $_gender=null;
        $_channel=null;
        $_listOrder=null;
        $_personName=null;
        $_personType=null;

        if (session('startTime')!=null) {
            $_startTime = session('startTime');
        }
        if (session('endTime')!=null) {
            $_endTime = session('endTime');
        }
        if (session('minAge')!=null) {
            $_minAge = session('minAge');
        }
        if (session('maxAge')!=null) {
            $_maxAge = session('maxAge');
        }
        if (session('gender')!=null) {
            $_gender=session('gender');
        }
        if (session('channel')!=null) {
            $_channel = session('channel');
        }
        if (session('listOrder')!=null) {
            $_listOrder = session('listOrder');
        }
        if (session('personName')!=null) {
            $_personName = session('personName');
        }
        if (session('personType')!=null) {
            $_personType = session('personType');
        }
        return $this->getAlerts($_startTime,$_endTime,$_minAge,$_maxAge,$_gender,$_channel,$_personName,$_personType,$_listOrder);
    }

    public function listAlertsWithFilters(Request $request) {
        //dd($request);
        $_startTime=null;
        $_endTime=null;
        $_minAge=null;
        $_maxAge=null;
        $_gender=null;
        $_channel=null;
        $_listOrder=null;
        $_personName=null;
        $_personType=null;

        if (isset($request->startTime)) {
            $_startTime = $request->startTime;
        }
        if (isset($request->endTime)) {
            $_endTime = $request->endTime;
        }
        if (isset($request->minAge) && ($request->minAge > GlobalController::ALL_AGE_RANGES )) {
            $_minAge = $request->minAge;
        }
        if (isset($request->maxAge) && ($request->maxAge > GlobalController::ALL_AGE_RANGES )) {
            $_maxAge = $request->maxAge;
        }
        if (isset($request->gender) && ($request->gender > GlobalController::ALL_GENDER )) {
            $_gender=$request->gender;
        }
        if (isset($request->channel) && ($request->channel > GlobalController::ALL_CHANNEL )) {
            $_channel = $request->channel;
        }
        if (isset($request->listOrder)) {
            $_listOrder = $request->listOrder;
        }
        if (isset($request->personName)) {
            $_personName = $request->personName;
        }
        if (isset($request->personType) && ($request->personType > GlobalController::ALL_PERSON_TYPES )) {
            $_personType = $request->personType;
        }
        return $this->getAlerts($_startTime,$_endTime,$_minAge,$_maxAge,$_gender,$_channel,$_personName,$_personType,$_listOrder);
    }

    public function listAlertsRemoveFilters() {
        $this->removeSessionFilters();
        return $this->getAlerts(null,null,null,null,null,null,null,null,null);
    }

    private function getAlerts($_startTime,$_endTime,$_minAge,$_maxAge,$_gender,$_channel,$_personName,$_personType,$_listOrder) {
        $startTime=GlobalController::START_TIME;
        $endTime=GlobalController::END_TIME;
        $minAge=GlobalController::MIN_AGE;
        $maxAge=GlobalController::MAX_AGE;
        $minGender=GlobalController::MIN_GENDER;
        $maxGender=GlobalController::MAX_GENDER;
        $minChannel=GlobalController::MIN_CHANNEL;
        $maxChannel=GlobalController::MAX_CHANNEL;
        $listOrder=GlobalController::LIST_ORDER;
        $personName="";
        $minPersonType=GlobalController::MIN_PERSON_TYPE;
        $maxPersonType=GlobalController::MAX_PERSON_TYPE;

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
        if (isset($_minAge) && ($_minAge > GlobalController::ALL_AGE_RANGES )) {
            $minAge = $_minAge;
            $params["minAge"] = $_minAge;
            session(['minAge' => $_minAge]);
        }
        if (isset($_maxAge) && ($_maxAge > GlobalController::ALL_AGE_RANGES )) {
            $maxAge = $_maxAge;
            $params["maxAge"] = $_maxAge;
            session(['maxAge' => $_maxAge]);
        }
        if (isset($_gender) && ($_gender > GlobalController::ALL_GENDER )) {
            $minGender=$_gender;
            $maxGender=$_gender;
            $params["gender"] = $_gender;
            // Store in the session...
            session(['gender' => $_gender]);
        }
        if (isset($_channel) && ($_channel> GlobalController::ALL_CHANNEL )) {
            $minChannel = $_channel;
            $maxChannel = $_channel;
            $params["channel"] = $_channel;
            session(['channel' => $_channel]);
        }
        if (isset($_personName)) {
            $personName = $_personName;
            $params["personName"] = $_personName;
            session(['personName' => $_personName]);
        }
        if (isset($_personType) && ($_personType> GlobalController::ALL_PERSON_TYPES )) {
            $minPersonType = $_personType;
            $maxPersonType = $_personType;
            $params["personType"] = $_personType;
            session(['personType' => $_personType]);
        }
        if (isset($_listOrder)) {
            $listOrder = $_listOrder;
            $params["listOrder"] = $_listOrder;
            session(['listOrder' => $_listOrder]);
        }
        $items = $this->dbListAlerts($minChannel,$maxChannel,$startTime,$endTime,$minAge,$maxAge,$minGender,$maxGender,$minPersonType,$maxPersonType,$personName,$listOrder);

        //Calculate the area location to center the preview image:
        for($i=0;$i<count($items);$i++) {
            //Get real size of the picture:
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

        //Camera filter -> fs_channel table
        $channels = Channel::orderBy('description', 'asc')->get();
        $knownfaces = Knownface::all()->keyby("id");

        return view('private_area.alerts') 
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('channels',$channels)
            ->with('personTypes',$this->getPersonTypes())
            ->with('personTypeColors',$this->getPersonTypeColors())
            ->with('age_ranges',$this->getAgeRanges())
            ->with('genders',$this->getGenders())
            ->with('listOrders',$this->getListOrders())
            ->with('alertIcons',$this->getAlertStateIcons())
            ->with('params',$params)
            ->with('knownfaces',$knownfaces)
            ->with('items',$items);
    }

    public function alertStateUpdate(Request $request) {
        $res = 0;
        if (isset($request->alert_id) && isset($request->alert_action)) {
            $identified = Identified::find($request->alert_id);
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
