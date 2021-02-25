<?php

namespace App\Http\Controllers;

use DB;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GlobalController extends Controller
{
    const PAGE_SIZE = 8;
    const ALL_CHANNEL = -1;
    const MIN_CHANNEL = 0;
    const MAX_CHANNEL =1000;
    const START_TIME="2000-01-01 00:00:00";
    const END_TIME="2200-01-01 00:00:00";
    const ALL_AGE_RANGES=-1;
    const MIN_AGE=0;
    const MAX_AGE=200;
    const ALL_GENDER=-1;
    const MIN_GENDER=0;
    const MAX_GENDER=1;
    const LIST_ORDER="Desc";
    //Alert Types:
    const ALL_ALERT_TYPES=-1;
    const MIN_ALERT_TYPE=0;
    const MAX_ALERT_TYPE=1;

    const ALL_PERSON_TYPES=-1;
    const MIN_PERSON_TYPE=0;
    const MAX_PERSON_TYPE=2;
    const ALL_PERSONS=-1;
    const MIN_PERSON=0;
    const MAX_PERSON=999999999;
    const REPORTS_CHARTS = 0;
    const REPORTS_ALERTS = 1;
    const ALL_PERIOD = -1;
    const MIN_PERIOD = 0;
    const MAX_PERIOD = 4;
    const ALL_COLOR_RANGES=-1;
    const ALL_COLORS="%";

    const ALERTSETTINGS_EMAIL = 0;
    const ALERTSETTINGS_TELEGRAM = 1;

    //Default width / height for the channel camera image preview using in>
    //Tabs Channels - Area selection coordinates and Settings - Select floor area
    const CH_IMG_REAL_WIDTH = 1920;
    const CH_IMG_REAL_HEIGHT = 1080;
    
    //At least we need 2 coordinates (x,y) -> 4 points:
    const MIN_NUM_OF_POINTS = 4; 

    //Added on Phase 3 for reports vagrancy
    const ALERT_TYPE_PEOPLE = -1;
    const ALERT_TYPE_VAGRANCY = 0;
    const ALERT_TYPE_NONFACE = 1;
    //Vagrancy types:
    const ALL_VAG_TYPES = -1;
    const MIN_VAG_TYPE = 0;
    const MAX_VAG_TYPE =100000; 
    //Probability is 0 => blob
    const BLOB_VAG_TYPE = 0;
    //Probability is >0 => body
    const BODY_VAG_TYPE =100; 
    
    protected function checkEngineStatus() {
        $status = -1;

        //Private process
        return $status;
    }

    protected function checkBrightSignDevice() {
        $settings = DB::table('fs_settings')
            ->select('brightsign_flag')->first();
        //Return true if this flag is enable:
        return $settings->brightsign_flag>0;
    }

    public function startEngine() {

        try {
            //Private process   
            
            return 1;
        } catch(\Error $err) {
            return "Error: ".$err->getMessage();
        }
    }

    protected function stopEngine() {
        $status = -1;

        //Private process
        return $status;
    }
    
    protected function getAgeRanges() {
        $age_ranges = array();
        $age_ranges = [-1 => 'All',0 => '16- Years Old',1 => '17-35 Years Old',2 => '35-55 Years Old',3 => '55+ Years Old'];
        return $age_ranges;
    }

    protected function getGenders() {
    	$genders = array();
    	$genders = [-1 => 'All', 0 => 'Female', 1 => 'Male'];
    	return $genders;
    }

    protected function getListOrders() {
    	$listOrders = array();
    	$listOrders = ['Desc' => 'Newest', 'Asc' => 'Oldest'];
    	return $listOrders;
    }

    protected function getPersonTypes() {
        $personTypes = array();
        $personTypes = [-1 => 'All',0 => 'VIP', 1 => 'Blacklist', 2 => 'Staff'];
        return $personTypes;
    }

    protected function getPersonTypeColors() {
        $personTypeColors = array();
        $personTypeColors = [0 => 'LightCyan', 1 => 'MistyRose', 2 => 'Beige'];
        return $personTypeColors;
    }

    protected function getAlertTypes() {
        $alertTypes = array();
        $alertTypes = [-1 => 'People',0 => 'Vagrancy Alerts', 1 => 'Nonface Alerts'];
        return $alertTypes;
    }

    protected function getChartTypes() {
        $chartTypes = array();
        $chartTypes = [0=>'Pie Chart',1=>'Bar Chart',2=>'Line Chart'];
        return $chartTypes;
    }

    protected function getStatisticTypes() {
        $statTypes = array();
        $statTypes = [0=>'Age Statistics',1=>'Gender Statistics',2=>'Emotional Statistics',3=>'Alert Statistics'];
        return $statTypes;
    }

    protected function getAlertStateIcons() {
        $alertStateIcons = array();
        $alertStateIcons = [-1 => 'question.png', 10 => 'cross.png', 20 => 'checkmark.png'];
        return $alertStateIcons;
    }

    protected function getConfirmedValues() {
        $values = array();
        $values = [-1 => 'Unconfirmed', 10 => 'Rejected', 20 => 'Confirmed'];
        return $values;
    }

    protected function getImgPreviewWidth($area_width,$img_width) {
        $img_preview_width=600;
        //Check the ratio between the real image width and the width of the area to select:
        $area_width_ratio = number_format($area_width/$img_width,2)*100;
        if ($area_width_ratio>10 && $area_width_ratio<=15) $img_preview_width=500;
        if ($area_width_ratio>15 && $area_width_ratio<=20) $img_preview_width=380;
        if ($area_width_ratio>20 && $area_width_ratio<=25) $img_preview_width=280;
        if ($area_width_ratio>25 && $area_width_ratio<=30) $img_preview_width=200;
        if ($area_width_ratio>30) $img_preview_width=140;

        return $img_preview_width;
    }

    protected function getReportsTimePeriod() {
        $timePeriods = array();
        $timePeriods = [-1=>'All time',0=>'Past Hour',1=>'Past Day',2=>'Past Week',3=>'Past Month'];
        return $timePeriods;
    }

    protected function getChannelTypes() {
        $channelTypes = array();
        $channelTypes = [0 => 'IP Camera', 1 => 'USB Camera'];
        return $channelTypes;
    }

    protected function getBodyColors() {
        $personTypeColors = array();
        $personTypeColors = [-1 => 'All', 0 => 'black', 1 => 'blue', 2 => 'cyan', 3 => 'purple', 4 => 'red', 5 => 'white', 6 => 'yellow'];
        return $personTypeColors;
    }

    protected function getUsersLevels() {
        $usersLevels = array();
        $usersLevels = ['Admin'=>1,'Manager'=>2,'Staff'=>3];
        return $usersLevels;
    }

    protected function getVagrancyTypes() {
        $vagTypes = array();
        $vagTypes = [-1 => 'All', 0 => 'Blob', 1 => 'Body'];
        return $vagTypes;
    }

    protected function getApiIntegrateList() {
        $list = array();
        $list = ['Axis','MarchNetworks','Milestone','Genetech'];
        return $list;
    }

    protected function getApiAlertTypes() {
        $list = array();
        $list = ['VIP','Blacklist','Staff','Nonface','Vagrancy-Body','Vagrancy-Blob'];
        return $list;
    }

    protected function removeSessionFilters() {
        session()->forget('startTime');
        session()->forget('endTime');
        session()->forget('minAge');
        session()->forget('maxAge');
        session()->forget('gender');
        session()->forget('channel');
        session()->forget('listOrder');
        session()->forget('chartType');
        session()->forget('statType');
        session()->forget('alertType');
        session()->forget('vagrancyType');
        session()->forget('personType');
        session()->forget('personName');
        session()->forget('chartType');
        session()->forget('statType');
        session()->forget('person');
        session()->forget('timePeriod');
        session()->forget('vagType');
        session()->forget('topColor');
        session()->forget('bottomColor');
    }

    //Function to format channel image area coordinates to save on Database. Used in ChannelsController and SettingsController:
    protected function coordinatesFormatToDB($req_coords,$ch_img_screen_width,$ch_img_screen_height,$ch_img_real_width=self::CH_IMG_REAL_WIDTH,$ch_img_real_height=self::CH_IMG_REAL_HEIGHT) {
        //Calculate the ratio to scale the coordinates:
        $ch_scale_width = $ch_img_real_width/$ch_img_screen_width;
        $ch_scale_height = $ch_img_real_height/$ch_img_screen_height;
        
        
        if (($req_coords) && (strlen($req_coords)>0)) {
            $str_coords = "";
            $list_coords = explode("#",$req_coords);
            foreach ($list_coords as $item_coords) {
                $coords=array();
                $points = explode(",",$item_coords);
                $total_points = count($points);
                if ($total_points>=self::MIN_NUM_OF_POINTS) {
                    $i = 0;
                    do {
                        $x = round($points[$i]*$ch_scale_width);
                        $y = round($points[$i+1]*$ch_scale_height);
                        $coords[]="($x,$y)";
                        $i+=2;
                    } while ($i<$total_points);
                }
                $str_coords .= "[".implode(" ",$coords)."]";
            }
        } else
            $str_coords = "0";
        //dd($str_coords);
        return $str_coords;
    }

    //Function to format channel image area coordinates to save on Database. Used in ChannelsController and SettingsController:
    protected function coordinatesFormatToDB_old($req_coords,$ch_img_screen_width,$ch_img_screen_height,$ch_img_real_width=self::CH_IMG_REAL_WIDTH,$ch_img_real_height=self::CH_IMG_REAL_HEIGHT) {
        //Calculate the ratio to scale the coordinates:
        $ch_scale_width = $ch_img_real_width/$ch_img_screen_width;
        $ch_scale_height = $ch_img_real_height/$ch_img_screen_height;
        $str_coords = "0";
        $coords=array();
        if (($req_coords) && (strlen($req_coords)>0)) {
            $points = explode(",",$req_coords);
            $total_points = count($points);
            if ($total_points>=self::MIN_NUM_OF_POINTS) {
                $i = 0;
                do {
                    $x = round($points[$i]*$ch_scale_width);
                    $y = round($points[$i+1]*$ch_scale_height);
                    $coords[]="($x,$y)";
                    $i+=2;
                } while ($i<$total_points);
            }
            $str_coords = implode(" ",$coords);
        }
        //dd($str_coords);
        return $str_coords;
    }

    protected function splitCoords($db_coords) {
        $coords_areas = array();
        $state = 0;
        $chars = str_split($db_coords);
        foreach ($chars as $char) {
            switch ($char) {
                case '[': //Starting group of coordinates (one area)
                    $state = 1;
                    $area="";
                    break;
                case ']':   //End of a group of coordinates (area closed)
                    $state=4;
                    $coords_areas[] = $area;
                    break;
                case '(':   //Starting a coordinate
                    if ($state==3) $area.=",";
                    $state=2;
                    break;
                case ')':   //End of coordinate
                    $state=3;
                    break;
                default:
                    if ($state == 2 && (is_numeric($char) || $char==',')) $area.=$char;
                    break;
            }
        }
        return $coords_areas;
    }

    protected function coordinatesFormatFromDB_new($db_coords,$ch_img_screen_width,$ch_img_screen_height,$ch_img_real_width=self::CH_IMG_REAL_WIDTH,$ch_img_real_height=self::CH_IMG_REAL_HEIGHT) {
        //Calculate the ratio to scale the coordinates:
        $ch_scale_width = $ch_img_real_width/$ch_img_screen_width;
        $ch_scale_height = $ch_img_real_height/$ch_img_screen_height;
        $items = explode("][",$db_coords);
        $all_coords = array();
        foreach ($items as $item) {
            $item = str_replace("[", "", $item);
            $item = str_replace("]", "", $item);
            $all_coords[] = $item;
        }

        $str_coords = array();
        foreach ($all_coords as $item_coord) {
            $coordinates = explode(" ",$item_coord);
            $cds=implode(",",$coordinates);
            $cds = str_replace("(", "", $cds);
            $cds = str_replace(")", "", $cds);

            $points = explode(",",$cds);
            $total_points = count($points);
            if ($total_points>=self::MIN_NUM_OF_POINTS) {
                $i = 0;
                $coords = array();
                do {
                    $x = round($points[$i]/$ch_scale_width);
                    $y = round($points[$i+1]/$ch_scale_height);
                    $coords[]="$x,$y";
                    $i+=2;
                } while ($i<$total_points);
                $str_coords[] = implode(",",$coords);
            }
            
        }
        dd($str_coords);
        return $str_coords;
    }

    protected function coordinatesFormatFromDB($db_coords,$ch_img_screen_width,$ch_img_screen_height,$ch_img_real_width=self::CH_IMG_REAL_WIDTH,$ch_img_real_height=self::CH_IMG_REAL_HEIGHT) {
        //Calculate the ratio to scale the coordinates:
        $ch_scale_width = $ch_img_real_width/$ch_img_screen_width;
        $ch_scale_height = $ch_img_real_height/$ch_img_screen_height;
        /*
        $coordinates = explode(" ",$db_coords);
        $cds=implode(",",$coordinates);
        $cds = str_replace("(", "", $cds);
        $cds = str_replace(")", "", $cds);
        */
        $str_coords = array();
        $cd = $this->splitCoords($db_coords);
        foreach ($cd as $cds) {
            $coords = array();
            $points = explode(",",$cds);
            $total_points = count($points);
            if ($total_points>=self::MIN_NUM_OF_POINTS) {
                $i = 0;
                do {
                    $x = round($points[$i]/$ch_scale_width);
                    $y = round($points[$i+1]/$ch_scale_height);
                    $coords[]="$x,$y";
                    $i+=2;
                } while ($i<$total_points);
                $str_coords[] = implode(",",$coords);
            }
        }
        //dd($str_coords);
        return $str_coords;
    }

    protected function coordinatesFormatFromDB_OLD($db_coords,$ch_img_screen_width,$ch_img_screen_height,$ch_img_real_width=self::CH_IMG_REAL_WIDTH,$ch_img_real_height=self::CH_IMG_REAL_HEIGHT) {
        //Calculate the ratio to scale the coordinates:
        $ch_scale_width = $ch_img_real_width/$ch_img_screen_width;
        $ch_scale_height = $ch_img_real_height/$ch_img_screen_height;
        $coordinates = explode(" ",$db_coords);
        $cds=implode(",",$coordinates);
        $cds = str_replace("(", "", $cds);
        $cds = str_replace(")", "", $cds);

        $points = explode(",",$cds);
        $total_points = count($points);
        if ($total_points>=self::MIN_NUM_OF_POINTS) {
            $i = 0;
            do {
                $x = round($points[$i]/$ch_scale_width);
                $y = round($points[$i+1]/$ch_scale_height);
                $coords[]="$x,$y";
                $i+=2;
            } while ($i<$total_points);
        }
        $str_coords = implode(",",$coords);
        return $str_coords;
    }

    protected function dbListFaces($minChannel,$maxChannel,$startTime,$endTime,$minAge,$maxAge,$minGender,$maxGender, $listOrder) {
        /*
    	$query = "SELECT fs_face.id,gender,age,angry,happy,neutral,surprise,description,confidence,topLeftX,topLeftY,width,height,fs_face.accuracy,img_name,captureTime FROM ((fs_face INNER JOIN fs_frame ON fs_face.frame_id = fs_frame.id) INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) WHERE fs_channel.id >= $minChannel AND fs_channel.id <= $maxChannel AND fs_frame.captureTime >= '$startTime' AND fs_frame.captureTime <= '$endTime' AND fs_face.age >= $minAge AND fs_face.age <= $maxAge AND fs_face.gender >= $minGender AND fs_face.gender <= $maxGender ORDER BY fs_face.id $listOrder";
        //dd($query);
        $items = DB::select($query);
        //dd($items);
        */
        
        $items = DB::table('fs_face')
            ->join('fs_frame', 'fs_face.frame_id', '=', 'fs_frame.id')
            ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
            ->select('fs_face.id','gender','age','angry','happy','neutral','surprise','description','confidence','fs_face.topLeftX','fs_face.topLeftY','fs_face.width','fs_face.height','fs_face.accuracy','img_name','captureTime')
            ->where('fs_channel.id','>=',"$minChannel")
            ->where('fs_channel.id','<=',"$maxChannel")
            ->where('fs_frame.captureTime','>=',"$startTime")
            ->where('fs_frame.captureTime','<=',"$endTime")
            ->where('fs_face.age','>=',"$minAge")
            ->where('fs_face.age','<=',"$maxAge")
            ->where('fs_face.gender','>=',"$minGender")
            ->where('fs_face.gender','<=',"$maxGender")
            ->orderBy('fs_face.id',"$listOrder")->paginate(GlobalController::PAGE_SIZE);
        //dd($items);
    	return $items;
    }

    protected function dbListBodies($minChannel,$maxChannel,$startTime,$endTime,$topColor,$bottomColor,$listOrder) {
        /*
        $query = "SELECT fs_body.id,probability,description,fs_body.topColor,fs_body.bottomColor,fs_body.topLeftX,fs_body.topLeftY,fs_body.width,fs_body.height,img_name,captureTime FROM ((fs_body INNER JOIN fs_frame ON fs_body.frame_id = fs_frame.id) INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) WHERE fs_channel.id >= $minChannel AND fs_channel.id <= $maxChannel AND fs_frame.captureTime >= '$startTime' AND fs_frame.captureTime <= '$endTime' AND fs_body.topColor like '$topColor' AND fs_body.bottomColor like '$bottomColor' ORDER BY fs_body.id $listOrder";
        //dd($query);
        $items = DB::select($query);
        //dd($items);
        */
        
        $items = DB::table('fs_body')
            ->join('fs_frame', 'fs_body.frame_id', '=', 'fs_frame.id')
            ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
            ->select('fs_body.id','probability','description','fs_body.topColor','fs_body.bottomColor','fs_body.topLeftX','fs_body.topLeftY','fs_body.width','fs_body.height','img_name','captureTime')
            ->where('fs_channel.id','>=',"$minChannel")
            ->where('fs_channel.id','<=',"$maxChannel")
            ->where('fs_frame.captureTime','>=',"$startTime")
            ->where('fs_frame.captureTime','<=',"$endTime")
            ->where('fs_body.topColor','like',"$topColor")
            ->where('fs_body.bottomColor','like',"$bottomColor")
            ->orderBy('fs_body.id',"$listOrder")->paginate(GlobalController::PAGE_SIZE);
        //dd($items);
        return $items;
    }

    protected function dbListAlerts($minChannel,$maxChannel,$startTime,$endTime,$minAge,$maxAge,$minGender,$maxGender, $minPersonType, $maxPersonType,$personName,$listOrder) {
        /*
        $query = "SELECT fs_face.id as FACE_ID, fs_face.confidence, fs_face.width, fs_face.height, fs_face.topLeftX, fs_face.topLeftY, fs_identified.id as IDENT_ID, fs_identified.confirmed, fs_identified.known_face_id, fs_person.id as PERSON_ID, fs_person.person_type, fs_person.description as PERSON_DESC, fs_person.info, fs_channel.description as CH_DESC, fs_frame.captureTime, fs_frame.img_name  FROM ((((fs_identified INNER JOIN fs_face ON fs_identified.face_id = fs_face.id) INNER JOIN fs_frame ON fs_face.frame_id = fs_frame.id) INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) INNER JOIN fs_person ON fs_identified.person_id = fs_person.id) WHERE fs_channel.id >= $minChannel AND fs_channel.id <= $maxChannel AND fs_frame.captureTime >= '$startTime' AND fs_frame.captureTime <= '$endTime' AND fs_face.age >= $minAge AND fs_face.age <= $maxAge AND fs_face.gender >= $minGender AND fs_face.gender <= $maxGender AND fs_person.person_type >= $minPersonType AND fs_person.person_type <= $maxPersonType AND fs_person.description LIKE '%$personName%' ORDER BY fs_identified.id $listOrder";
        //dd($query);
        //$items = DB::select($query);
        //dd($items);
        */
        
        $items = DB::table('fs_identified')
            ->join('fs_face', 'fs_identified.face_id', '=', 'fs_face.id')
            ->join('fs_frame', 'fs_face.frame_id', '=', 'fs_frame.id')
            ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
            ->join('fs_person', 'fs_identified.person_id', '=', 'fs_person.id')
            ->select(DB::raw('fs_face.id as FACE_ID'), 'fs_face.confidence', 'fs_face.width', 'fs_face.height', 'fs_face.topLeftX', 'fs_face.topLeftY', DB::raw('fs_identified.id as IDENT_ID'), 'fs_identified.confirmed', 'fs_identified.known_face_id', DB::raw('fs_person.id as PERSON_ID'), 'fs_person.person_type', DB::raw('fs_person.description as PERSON_DESC'), 'fs_person.info', DB::raw('fs_channel.description as CH_DESC'), 'fs_frame.captureTime', 'fs_frame.img_name')
            ->where('fs_channel.id','>=',"$minChannel")
            ->where('fs_channel.id','<=',"$maxChannel")
            ->where('fs_frame.captureTime','>=',"$startTime")
            ->where('fs_frame.captureTime','<=',"$endTime")
            ->where('fs_face.age','>=',"$minAge")
            ->where('fs_face.age','<=',"$maxAge")
            ->where('fs_face.gender','>=',"$minGender")
            ->where('fs_face.gender','<=',"$maxGender")
            ->where('fs_person.person_type','>=',"$minPersonType")
            ->where('fs_person.person_type','<=',"$maxPersonType")
            ->where('fs_person.description','like',"%$personName%")
            ->orderBy('fs_identified.id',"$listOrder")->paginate(GlobalController::PAGE_SIZE);
        //dd($items);
        return $items;
    }

    protected function dbListVagAlerts($minChannel,$maxChannel,$startTime,$endTime,$minVagType,$maxVagType,$topColor,$bottomColor,$listOrder) {
        /*
        $query = "SELECT * FROM (((fs_vagrancyidentified INNER JOIN fs_body ON fs_vagrancyidentified.body_id = fs_body.id) 
        INNER JOIN fs_frame ON fs_body.frame_id = fs_frame.id) 
        INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) 
        WHERE fs_channel.id >= $minChannel AND fs_channel.id <= $maxChannel AND fs_frame.captureTime >= '$startTime' 
        AND fs_frame.captureTime <= '$endTime' 
        AND fs_body.probability >= $minVagType AND fs_body.probability <= $maxVagType
        AND fs_body.topColor LIKE '$topColor' AND fs_body.bottomColor LIKE '$bottomColor' ORDER BY fs_vagrancyidentified.id";
        //dd($query);
        $items = DB::select($query);
        //dd($items);
        */
        
        
        $items = DB::table('fs_vagrancyidentified')
            ->join('fs_body', 'fs_vagrancyidentified.body_id', '=', 'fs_body.id')
            ->join('fs_frame', 'fs_body.frame_id', '=', 'fs_frame.id')
            ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
            ->select('fs_vagrancyidentified.id','fs_vagrancyidentified.confirmed','fs_vagrancyidentified.body_id','fs_vagrancyidentified.firstBody_id','fs_body.topLeftX','fs_body.topLeftY','fs_body.width','fs_body.height','fs_body.topColor','fs_body.bottomColor','fs_body.probability','fs_body.frame_id','fs_frame.img_name','fs_frame.captureTime','fs_frame.channel_id',DB::raw('fs_channel.description as CH_DESC'))
            ->where('fs_channel.id','>=',"$minChannel")
            ->where('fs_channel.id','<=',"$maxChannel")
            ->where('fs_frame.captureTime','>=',"$startTime")
            ->where('fs_frame.captureTime','<=',"$endTime")
            ->where('fs_body.probability','>=',"$minVagType")
            ->where('fs_body.probability','<=',"$maxVagType")
            ->where('fs_body.topColor','like',"$topColor")
            ->where('fs_body.bottomColor','like',"$bottomColor")
            ->orderBy('fs_vagrancyidentified.id',"$listOrder")->paginate(GlobalController::PAGE_SIZE);
        //dd($items);
        return $items;
        
    }

    protected function dbListNonfaces($minChannel,$maxChannel,$startTime,$endTime,$topColor,$bottomColor,$listOrder) {
        /*
        $query = "SELECT * FROM ((fs_nonfaceidentified INNER JOIN fs_frame ON fs_nonfaceidentified.frame_id = fs_frame.id) 
            INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) 
            WHERE fs_channel.id >= 0 AND fs_channel.id <= 1000 
            AND fs_frame.captureTime >= '2000-01-01 00:00:00' AND fs_frame.captureTime <= '2200-01-01 00:00:00' 
            AND fs_nonfaceidentified.topColor LIKE '%' 
            AND fs_nonfaceidentified.bottomColor LIKE '%' 
            ORDER BY fs_nonfaceidentified.id";
        //dd($query);
        $items = DB::select($query);
        //dd($items);
        */
        
        
        $items = DB::table('fs_nonfaceidentified')
            ->join('fs_frame', 'fs_nonfaceidentified.frame_id', '=', 'fs_frame.id')
            ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
            ->select('fs_nonfaceidentified.id','fs_nonfaceidentified.confirmed','fs_nonfaceidentified.topColor','fs_nonfaceidentified.bottomColor','fs_nonfaceidentified.probability','fs_nonfaceidentified.topLeftX','fs_nonfaceidentified.topLeftY','fs_nonfaceidentified.width','fs_nonfaceidentified.height','fs_nonfaceidentified.frame_id','fs_frame.img_name','fs_frame.captureTime','fs_frame.channel_id',DB::raw('fs_channel.description as CH_DESC'))
            ->where('fs_channel.id','>=',"$minChannel")
            ->where('fs_channel.id','<=',"$maxChannel")
            ->where('fs_frame.captureTime','>=',"$startTime")
            ->where('fs_frame.captureTime','<=',"$endTime")
            ->where('fs_nonfaceidentified.topColor','like',"$topColor")
            ->where('fs_nonfaceidentified.bottomColor','like',"$bottomColor")
            ->orderBy('fs_nonfaceidentified.id',"$listOrder")->paginate(GlobalController::PAGE_SIZE);
        //dd($items);
        return $items;
        
    }

    protected function dbGetKnownFace($face_id) {
        $query = "SELECT * from fs_knownface where id=$face_id";
        //dd($query);
        $items = DB::select($query);
        dd($items);
        return $items;
    }

    public function dbGetPersonPictures($person_id) {
        $items = DB::table('fs_knownface')
            ->select('id','img_name')
            ->where('person_id','=',"$person_id")->get();
        return $items;
    }

    protected function dbCountPersonsByType() {
        $items = [0,0,0];

        //Count person types:
        $person_type_count = DB::table('fs_person')
            ->select(DB::raw('person_type,count(*) as TOTAL'))
            ->groupBy('person_type')
            ->get()->keyBy('person_type');
        //Update $items array positions:
        foreach ($person_type_count as $key => $value) {
            $items[$key] = (int)$value->TOTAL;
        }
        //Add array item with total persons:
        $counters['types'] = $items;
        $persons = DB::table('fs_person')
            ->select(DB::raw('count(*) as TOTAL'))
            ->first();
        $counters['total'] = $persons->TOTAL;
        return $counters;
    }

    protected function dbChartByAge($minChannel,$maxChannel,$startTime,$endTime,$personTypeFilter,$minPersonType,$maxPersonType) {
        if ($personTypeFilter) {
            /*
            $query = "SELECT age,count(*) as TOTAL FROM (((((fs_intervalface INNER JOIN fs_face ON fs_intervalface.face_id = fs_face.id) INNER JOIN fs_frame ON fs_face.frame_id = fs_frame.id) INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) INNER JOIN fs_identified ON fs_face.id = fs_identified.face_id) INNER JOIN fs_person ON fs_person.id = fs_identified.person_id) WHERE fs_channel.id >= '$minChannel' AND fs_channel.id <= '$maxChannel' AND fs_frame.captureTime >= '$startTime' AND fs_frame.captureTime <= '$endTime' AND fs_person.person_type >= $minPersonType AND fs_person.person_type <= $maxPersonType group by age";
            dd($query);
            $items = DB::select($query);
            */
            
            $items = DB::table('fs_intervalface')
                ->join('fs_face', 'fs_intervalface.face_id', '=', 'fs_face.id')
                ->join('fs_frame', 'fs_face.frame_id', '=', 'fs_frame.id')
                ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
                ->join('fs_identified', 'fs_face.id', '=', 'fs_identified.face_id')
                ->join('fs_person', 'fs_person.id', '=', 'fs_identified.person_id')
                ->select('age',DB::raw('count(*) as TOTAL'))
                ->where('fs_channel.id','>=',"$minChannel")
                ->where('fs_channel.id','<=',"$maxChannel")
                ->where(DB::raw('date(fs_frame.captureTime)'),'>=',"$startTime")
                ->where(DB::raw('date(fs_frame.captureTime)'),'<=',"$endTime")
                ->where('fs_person.person_type','>=',"$minPersonType")
                ->where('fs_person.person_type','<=',"$maxPersonType")
                ->groupBy('age')
                ->get()->keyBy('age');
        } else {
            /*
            $query = "SELECT age,count(*) as TOTAL FROM ((((fs_intervalface INNER JOIN fs_face ON fs_intervalface.face_id = fs_face.id) INNER JOIN fs_frame ON fs_face.frame_id = fs_frame.id) INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) INNER JOIN fs_customer ON fs_intervalface.customer_id = fs_customer.id) WHERE fs_channel.id >= '$minChannel' AND fs_channel.id <= '$maxChannel' AND date(fs_frame.captureTime) >= '$startTime' AND date(fs_frame.captureTime) <= '$endTime' group by age";
            dd($query);
            //$items = DB::select($query);
            */
            
            $items = DB::table('fs_intervalface')
                ->join('fs_face', 'fs_intervalface.face_id', '=', 'fs_face.id')
                ->join('fs_frame', 'fs_face.frame_id', '=', 'fs_frame.id')
                ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
                ->join('fs_customer', 'fs_intervalface.customer_id', '=', 'fs_customer.id')
                ->select('age',DB::raw('count(*) as TOTAL'))
                ->where('fs_channel.id','>=',"$minChannel")
                ->where('fs_channel.id','<=',"$maxChannel")
                ->where(DB::raw('date(fs_frame.captureTime)'),'>=',"$startTime")
                ->where(DB::raw('date(fs_frame.captureTime)'),'<=',"$endTime")
                ->groupBy('age')
                ->get()->keyBy('age');
        }
        
        //dd($items);
        return $items;
    }

    protected function dbChartByGender($minChannel,$maxChannel,$startTime,$endTime,$personTypeFilter,$minPersonType,$maxPersonType) {
        if ($personTypeFilter) {
            /*
            $query = "SELECT gender,count(*) as TOTAL FROM (((((fs_intervalface INNER JOIN fs_face ON fs_intervalface.face_id = fs_face.id) INNER JOIN fs_frame ON fs_face.frame_id = fs_frame.id) INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) INNER JOIN fs_identified ON fs_face.id = fs_identified.face_id) INNER JOIN fs_person ON fs_person.id = fs_identified.person_id) WHERE fs_channel.id >= '$minChannel' AND fs_channel.id <= '$maxChannel' AND fs_frame.captureTime >= '$startTime' AND fs_frame.captureTime <= '$endTime' AND fs_person.person_type >= $minPersonType AND fs_person.person_type <= $maxPersonType group by gender";
            dd($query);
            $items = DB::select($query);
            */
            
            $items = DB::table('fs_intervalface')
                ->join('fs_face', 'fs_intervalface.face_id', '=', 'fs_face.id')
                ->join('fs_frame', 'fs_face.frame_id', '=', 'fs_frame.id')
                ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
                ->join('fs_identified', 'fs_face.id', '=', 'fs_identified.face_id')
                ->join('fs_person', 'fs_person.id', '=', 'fs_identified.person_id')
                ->select('gender',DB::raw('count(*) as TOTAL'))
                ->where('fs_channel.id','>=',"$minChannel")
                ->where('fs_channel.id','<=',"$maxChannel")
                ->where(DB::raw('date(fs_frame.captureTime)'),'>=',"$startTime")
                ->where(DB::raw('date(fs_frame.captureTime)'),'<=',"$endTime")
                ->where('fs_person.person_type','>=',"$minPersonType")
                ->where('fs_person.person_type','<=',"$maxPersonType")
                ->groupBy('gender')
                ->get()->keyBy('gender');
        } else {
            /*
            $query = "SELECT gender,count(*) as TOTAL FROM ((((fs_intervalface INNER JOIN fs_face ON fs_intervalface.face_id = fs_face.id) INNER JOIN fs_frame ON fs_face.frame_id = fs_frame.id) INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) INNER JOIN fs_customer ON fs_intervalface.customer_id = fs_customer.id) WHERE fs_channel.id >= '$minChannel' AND fs_channel.id <= '$maxChannel' AND fs_frame.captureTime >= '$startTime' AND fs_frame.captureTime <= '$endTime' group by gender";
            dd($query);
            //$items = DB::select($query);
            */
            
            $items = DB::table('fs_intervalface')
                ->join('fs_face', 'fs_intervalface.face_id', '=', 'fs_face.id')
                ->join('fs_frame', 'fs_face.frame_id', '=', 'fs_frame.id')
                ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
                ->join('fs_customer', 'fs_intervalface.customer_id', '=', 'fs_customer.id')
                ->select('gender',DB::raw('count(*) as TOTAL'))
                ->where('fs_channel.id','>=',"$minChannel")
                ->where('fs_channel.id','<=',"$maxChannel")
                ->where(DB::raw('date(fs_frame.captureTime)'),'>=',"$startTime")
                ->where(DB::raw('date(fs_frame.captureTime)'),'<=',"$endTime")
                ->groupBy('gender')
                ->get()->keyBy('gender');
        }
        
        //dd($items);
        return $items;
    }

    protected function dbChartByEmotion($minChannel,$maxChannel,$startTime,$endTime,$personTypeFilter,$minPersonType,$maxPersonType) {
        if ($personTypeFilter) {
            $db_items = DB::table('fs_face')
                ->join('fs_frame', 'fs_face.frame_id', '=', 'fs_frame.id')
                ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
                ->join('fs_identified', 'fs_face.id', '=', 'fs_identified.face_id')
                ->join('fs_person', 'fs_person.id', '=', 'fs_identified.person_id')
                ->where('fs_channel.id','>=',"$minChannel")
                ->where('fs_channel.id','<=',"$maxChannel")
                ->where(DB::raw('date(fs_frame.captureTime)'),'>=',"$startTime")
                ->where(DB::raw('date(fs_frame.captureTime)'),'<=',"$endTime")
                ->where('fs_person.person_type','>=',"$minPersonType")
                ->where('fs_person.person_type','<=',"$maxPersonType")
                ->get();
        } else {
            $db_items = DB::table('fs_face')
                ->join('fs_frame', 'fs_face.frame_id', '=', 'fs_frame.id')
                ->join('fs_channel', 'fs_frame.channel_id', '=', 'fs_channel.id')
                ->where('fs_channel.id','>=',"$minChannel")
                ->where('fs_channel.id','<=',"$maxChannel")
                ->where(DB::raw('date(fs_frame.captureTime)'),'>=',"$startTime")
                ->where(DB::raw('date(fs_frame.captureTime)'),'<=',"$endTime")
                ->get();
            $total = count($db_items);
            //Happy, Angry, Dissat, Neutral, Surprise, Error Rate
            $items = [0,0,0,0,0,0];
            foreach ($db_items as $item) {
                $items[0] += $item->happy;
                if ($item->angry>=60)
                    $items[1] += $item->angry;
                else
                    $items[2] += $item->angry;
                $items[3] += $item->neutral;
                $items[4] += $item->surprise;
            }
            for ($i=0;$i<count($items);$i++) {
                if (count($db_items)>0)
                    $items[$i] = number_format($items[$i]/count($db_items),1);
                else
                    $items[$i] = 0;
            }
            
        }
        return $items;
    }

    protected function dbAlerts($minChannel,$maxChannel,$startTime,$endTime,$minPersonType,$maxPersonType,$minPerson,$maxPerson) {

        $query = "SELECT fs_identified.id as ALERT_ID,fs_frame.captureTime,fs_face.id as FACE_ID,fs_person.description as PERSON_NAME,fs_person.person_type,fs_face.confidence, fs_channel.id as CHANNEL_ID,fs_identified.confirmed FROM ((((fs_identified INNER JOIN fs_face ON fs_identified.face_id = fs_face.id) INNER JOIN fs_frame ON fs_face.frame_id = fs_frame.id) INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id) INNER JOIN fs_person ON fs_identified.person_id = fs_person.id) WHERE fs_channel.id >= $minChannel AND fs_channel.id <= $maxChannel AND fs_frame.captureTime >= '$startTime' AND fs_frame.captureTime <= '$endTime' AND fs_person.person_type >= $minPersonType AND fs_person.person_type <= $maxPersonType AND fs_person.id >= $minPerson AND fs_person.id <= $maxPerson ORDER BY fs_identified.id";
        //dd($query);
        $db_items = DB::select($query);
        //dd($db_items);
        return $db_items;
    }
    protected function dbChartByAlerts($minChannel,$maxChannel,$startTime,$endTime,$minPersonType,$maxPersonType,$minPerson,$maxPerson) {

        $db_items = $this->dbAlerts($minChannel,$maxChannel,$startTime,$endTime,$minPersonType,$maxPersonType,$minPerson,$maxPerson);
        //dd($db_items);
        //0 => 'VIP', 1 => 'Blacklist', 2 => 'Staff'
        $items = [0,0,0];
        foreach ($db_items as $item) {
            $items[$item->person_type]++;
        }
        //dd($items);
        return $items;
    }

    protected function dbAlertsVagrancy($minChannel,$maxChannel,$startTime,$endTime,$minVagType,$maxVagType) {        
        $query = "SELECT fs_vagrancyidentified.id AS ALERT_ID,fs_body.id as body_id,captureTime,probability,channel_id,confirmed FROM (((fs_vagrancyidentified INNER JOIN fs_body  ON fs_vagrancyidentified.body_id = fs_body.id)  INNER JOIN fs_frame ON fs_body.frame_id = fs_frame.id)  INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id)  WHERE fs_channel.id >= $minChannel AND fs_channel.id <= $maxChannel AND fs_frame.captureTime >= '$startTime' AND fs_frame.captureTime <= '$endTime' AND fs_body.probability >= $minVagType AND fs_body.probability <= $maxVagType  ORDER BY fs_vagrancyidentified.id";
        //dd($query);
        $db_items = DB::select($query);
        //dd($db_items);
        return $db_items;
    }

    protected function dbChartByAlertsVagrancy($minChannel,$maxChannel,$startTime,$endTime,$minVagType,$maxVagType) {

        $db_items = $this->dbAlertsVagrancy($minChannel,$maxChannel,$startTime,$endTime,$minVagType,$maxVagType);
        //dd($db_items);
        //0 => 'Body', 1 => 'Blob'
        $items = [0,0];
        foreach ($db_items as $item) {
            if ($item->probability>0)
                $items[0]++;
            else
                $items[1]++;
        }
        //dd($items);
        return $items;
    }

    protected function dbAlertsNonface($minChannel,$maxChannel,$startTime,$endTime) {
        $query = "SELECT fs_nonfaceidentified.id as ALERT_ID,fs_frame.id as frame_id,captureTime,matchPercentage,channel_id,confirmed FROM ((fs_nonfaceidentified INNER JOIN fs_frame  ON fs_nonfaceidentified.frame_id = fs_frame.id)  INNER JOIN fs_channel ON fs_frame.channel_id = fs_channel.id)  WHERE fs_channel.id >= 0 AND fs_channel.id <= 1000  AND fs_frame.captureTime >= '$startTime' AND fs_frame.captureTime <= '$endTime' ORDER BY fs_nonfaceidentified.id ";
        //dd($query);
        $db_items = DB::select($query);
        //dd($db_items);
        return $db_items;
    }

    protected function dbChartByAlertsNonface($minChannel,$maxChannel,$startTime,$endTime) {
        //TODO: Check if this is ok, just count the alerts (no filters), in this case, recode with a Select count() query:
        $db_items = $this->dbAlertsNonface($minChannel,$maxChannel,$startTime,$endTime);
        //dd($db_items);
        //0 => 'Nonface'
        $items = [0];
        foreach ($db_items as $item) {
            $items[0]++;
        }
        //dd($items);
        return $items;
    }

}
