<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Channel;
use App\Person;
use DateTime;
use DatePeriod;
use DateInterval;

use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Style_Alignment;

class ReportsController extends GlobalController {

    public function showReports($section_id=GlobalController::REPORTS_CHARTS) {
        $_timePeriod=null;
    	$_startTime=null;
        $_endTime=null;
        $_chartType=null;
        $_statType=null;
        $_channel=null;
        $_alertType=null;
        $_vagType=null;
        $_personType=null;
        $_person=null;

        if (session('timePeriod')!=null) {
            $_timePeriod = session('timePeriod');
        }
        if (session('startTime')!=null) {
            $_startTime = session('startTime');
        }
        if (session('endTime')!=null) {
            $_endTime = session('endTime');
        }
        if (session('chartType')!=null) {
            $_chartType = session('chartType');
        }
        if (session('statType')!=null) {
            $_statType = session('statType');
        }
        if (session('channel')!=null) {
            $_channel = session('channel');
        }
        if (session('alertType')!=null) {
            $_alertType = session('alertType');
        }
        if (session('vagType')!=null) {
            $_vagType = session('vagType');
        }
        if (session('personType')!=null) {
            $_personType = session('personType');
        }
        if (session('person')!=null) {
            $_person = session('person');
        }
        if ($section_id==GlobalController::REPORTS_CHARTS)
            return $this->getChart($_startTime,$_endTime,$_chartType,$_statType,$_channel,$_alertType,$_vagType,$_personType,$_person);
        else
            return $this->getAlerts(false,$_timePeriod,$_channel,$_personType,$_person,$_alertType,$_vagType);
    }

    public function showChartsWithFilters(Request $request) {
    	$_startTime=null;
        $_endTime=null;
        $_chartType=null;
        $_statType=null;
        $_channel=null;
        $_alertType=null;
        $_vagType=null;
        $_personType=null;
        $_person=null;

        if (isset($request->startTime)) {
            $_startTime = $request->startTime;
        }
        if (isset($request->endTime)) {
            $_endTime = $request->endTime;
        }
        if (isset($request->chartType)) {
            $_chartType = $request->chartType;
        }
        if (isset($request->statType)) {
            $_statType = $request->statType;
        }
        if (isset($request->channel) && ($request->channel> GlobalController::ALL_CHANNEL )) {
            $_channel = $request->channel;
        }
        if (isset($request->alertType) && ($request->alertType > GlobalController::ALL_ALERT_TYPES )) {
            $_alertType = $request->alertType;
        }
        if (isset($request->vagType) && ($request->vagType > GlobalController::ALL_VAG_TYPES )) {
            $_vagType = $request->vagType;
        }
        if (isset($request->personType) && ($request->personType > GlobalController::ALL_PERSON_TYPES )) {
            $_personType = $request->personType;
        }
        if (isset($request->person) && ($request->person > GlobalController::ALL_PERSONS )) {
            $_person = $request->person;
        }
        return $this->getChart($_startTime,$_endTime,$_chartType,$_statType,$_channel,$_alertType,$_vagType,$_personType,$_person);
    }
        
    public function showChartsRemoveFilters() {
    	$this->removeSessionFilters();
        return $this->showReports();
    }

    private function getChart($_startTime,$_endTime,$_chartType,$_statType,$_channel,$_alertType,$_vagType,$_personType,$_person) {
    	$startTime=GlobalController::START_TIME;
        $endTime=GlobalController::END_TIME;
        $chartType=null;
        $statType=null;
        $chartTitle="";
        $minChannel=GlobalController::MIN_CHANNEL;
        $maxChannel=GlobalController::MAX_CHANNEL;

        $alertTypeFilter = false;
        $minVagType=GlobalController::BLOB_VAG_TYPE;
        $maxVagType=GlobalController::BODY_VAG_TYPE;

        $minPersonType=GlobalController::MIN_PERSON_TYPE;
        $maxPersonType=GlobalController::MAX_PERSON_TYPE;
        $minPerson=GlobalController::MIN_PERSON;
        $maxPerson=GlobalController::MAX_PERSON;
        $showChart=false;

        $params = array();
        if (isset($_startTime)) {
            $startTime = date("Y-m-d", strtotime($_startTime));
            $params["startTime"] = $_startTime;
            session(['startTime' => $_startTime]);
        }
        if (isset($_endTime)) {
            $endTime = date("Y-m-d", strtotime($_endTime));
            $params["endTime"] = $_endTime;
            session(['endTime' => $_endTime]);
        }
        if (isset($_chartType)) {
        	$chartType = $_chartType;
        	$params["chartType"] = $_chartType;
        	session(['chartType' => $_chartType]);
        }
        if (isset($_statType)) {
        	$statType = $_statType;
        	$params["statType"] = $_statType;
        	session(['statType' => $_statType]);
        	$chartTitle = strtoupper($this->getStatisticTypes()[$_statType]);
        }
        if (isset($_channel) && ($_channel> GlobalController::ALL_CHANNEL )) {
            $minChannel = $_channel;
            $maxChannel = $_channel;
            $params["channel"] = $_channel;
            session(['channel' => $_channel]);
        }

        
        if (isset($_alertType) && ($_alertType> GlobalController::ALERT_TYPE_PEOPLE )) {
            $alertTypeFilter = true;
            $params["alertType"] = $_alertType;
            session(['alertType' => $_alertType]);
            if ($_alertType == GlobalController::ALERT_TYPE_VAGRANCY) {
                //Check vagrancy type:
                if (isset($_vagType) && ($_vagType > GlobalController::ALL_VAG_TYPES )) {
                    //$_vagType possible values: -1=>all, 0=>blob, 1=>body
                    //If $_vagType is 0 => min=0 and max=0
                    //If $_vagType is 1 => min=1 and max=100
                    $minVagType = $_vagType;    
                    if ($_vagType==GlobalController::BLOB_VAG_TYPE) $maxVagType = $_vagType;
                    $params["vagType"] = $_vagType;
                    session(['vagType' => $_vagType]);
                }
            }            
        }

        $personTypeFilter = false;
        if (isset($_personType) && ($_personType> GlobalController::ALL_PERSON_TYPES )) {
        	$personTypeFilter = true;
            $minPersonType = $_personType;
            $maxPersonType = $_personType;
            $params["personType"] = $_personType;
            session(['personType' => $_personType]);
        }
        $personFilter = false;
        if (isset($_person) && ($_person> GlobalController::ALL_PERSONS )) {
            $personFilter = true;
            $minPerson = $_person;
            $maxPerson = $_person;
            $params["person"] = $_person;
            session(['person' => $_person]);
        }

        $array = null;
        $colors = null;
        switch ($chartType) {
        	case 0:	//Pie chart:
        		//Format data for google charts:
		        $array[] = array();
		        //Check statistic type:
        		switch($statType) {
        			case 0:	//Age Statistics
                        //Colors:
                        $colors = "'SkyBlue', 'DeepPink', 'Yellow', 'Red', 'Gray', 'Maroon'";
                        $array[0] = ['Age', 'Number'];
        				$age_ranges = $this->getAgeRanges();
                        //Get data:
        				$items = $this->dbChartByAge($minChannel,$maxChannel,$startTime,$endTime,$personTypeFilter,$minPersonType,$maxPersonType);
        				//Create data array:
				        foreach ($items as $key => $value) {
				        	$array[] = ["$age_ranges[$key]",(int)$value->TOTAL];
				        }
        				break;
        			case 1:	//Gender Statistics
                        //Colors:
                        $colors = "'DeepPink', 'DodgerBlue', 'Maroon'";
        				$gender_types = $this->getGenders();
                        //Get data:
        				$items = $this->dbChartByGender($minChannel,$maxChannel,$startTime,$endTime,$personTypeFilter,$minPersonType,$maxPersonType);
        				$array[0] = ['Gender', 'Number'];
        				//Create data array:
				        foreach ($items as $key => $value) {
				        	$array[] = ["$gender_types[$key]",(int)$value->TOTAL];
				        }
        				break;
        			case 2:	//Emotional Statistics
                        //Colors
                        $colors = "'SpringGreen', 'Red', 'Gray', 'DodgerBlue','Orange','Maroon'";
                        //Get data:
                        $items = $this->dbChartByEmotion($minChannel,$maxChannel,$startTime,$endTime,$personTypeFilter,$minPersonType,$maxPersonType);
                        $array[0] = ['State', 'Percent'];
                        //Create data array:
                        $array[] = ["Happy",(float)$items[0]];
                        $array[] = ["Angry",(float)$items[1]];
                        $array[] = ["Dissatisfied",(float)$items[2]];
                        $array[] = ["Neutral",(float)$items[3]];
                        $array[] = ["Surprised",(float)$items[4]];
                        $array[] = ["Error Rate",(float)$items[5]];
        				break;
                    case 3: //Alert Statistis
                        //If Alert Type is People:
                        if (!$alertTypeFilter) {
                            //Colors
                            $colors = "'SpringGreen', 'Red', 'Yellow'";
                            $items = $this->dbChartByAlerts($minChannel,$maxChannel,$startTime,$endTime,$minPersonType,$maxPersonType,$minPerson,$maxPerson);
                            $array[0] = ['Person Type', 'Number'];
                            //Create data array:
                            $array[] = ["VIP",$items[0]];
                            $array[] = ["Blacklist",$items[1]];
                            $array[] = ["Staff",$items[2]];
                        } else {
                            switch ($_alertType) {
                                case GlobalController::ALERT_TYPE_VAGRANCY:
                                    $colors = "'Red', 'Blue'";
                                    $items = $this->dbChartByAlertsVagrancy($minChannel,$maxChannel,$startTime,$endTime,$minVagType,$maxVagType);
                                    $array[0] = ['Vagrancy Type', 'Number'];
                                    //Create data array:
                                    $array[] = ["Body",$items[0]];
                                    $array[] = ["Blob",$items[1]];
                                    break;
                                case GlobalController::ALERT_TYPE_NONFACE:
                                    $colors = "'Red'";
                                    $items = $this->dbChartByAlertsNonface($minChannel,$maxChannel,$startTime,$endTime);
                                    $array[0] = ['Alerts', 'Number'];
                                    //Create data array:
                                    $array[] = ["Nonfaces",$items[0]];
                                    break;
                            }
                        }
                        break;
        		}	
        		//dd($array);
        		break;
        	case 1:	//Bar (Column) chart:
        	case 2:	//Line chart:
				$period = new DatePeriod(
				     new DateTime($startTime),
				     new DateInterval('P1D'),
				     new DateTime(date('Y-m-d', strtotime($endTime . ' +1 day')))
				);
                //Define labels and colors;
                switch($statType) {
                    case 0: //Age Statistics
                        $colors = "'SkyBlue', 'DeepPink', 'Yellow', 'Red', 'Gray', 'Maroon'";
                        $array[0] = ['Date', '16- Years','17-35 Years','35-55 Years','55+ Years'];
                        break;
                    case 1: //Gender Statistics
                        $colors = "'DeepPink', 'DodgerBlue', 'Maroon'";
                        $array[0] = ['Date', 'Female','Male','Unknown'];
                        break;
                    case 2: //Emotional Statistics
                        $colors = "'SpringGreen', 'Red', 'Gray', 'DodgerBlue','Orange','Maroon'";
                        $array[0] = ['Date','Happy', 'Angry','Dissatisfied','Neutral','Surprised','Error Rate'];
                        break;
                    case 3: //Alert Statistis
                        if (!$alertTypeFilter) {
                            $colors = "'SpringGreen', 'Red', 'Yellow'";
                            $array[0] = ['Date','VIP', 'Blacklist','Staff'];   
                        } else {
                            $colors = "'Red', 'Blue'";
                            $array[0] = ['Date','Body', 'Blob'];   
                        } 
                        break;
                }
                //Add date range values:
				foreach ($period as $key => $value) {
					//echo $value->format('Y-m-d');
					$dayPeriod = $value->format('Y-m-d');
					
					switch($statType) {
	        			case 0:	//Age Statistics
	        				$items = $this->dbChartByAge($minChannel,$maxChannel,$dayPeriod,$dayPeriod,$personTypeFilter,$minPersonType,$maxPersonType);
                            $array_item = [date('m/d/Y H:i:s', strtotime($dayPeriod)),0,0,0,0];
	        				foreach ($items as $key => $value) {
								$array_item[$key+1] = (int)$value->TOTAL;
							}
	        				break;
	        			case 1:	//Gender Statistics
	        				$items = $this->dbChartByGender($minChannel,$maxChannel,$dayPeriod,date('Y-m-d', strtotime($dayPeriod . ' +1 day')),$personTypeFilter,$minPersonType,$maxPersonType);
                            $array_item = [date('m/d/Y H:i:s', strtotime($dayPeriod)),0,0,0];
	        				foreach ($items as $key => $value) {
								switch ($key) {
									case 0:	//Female
										$array_item[1] = (int)$value->TOTAL;
										break;
									case 1:	//Male
										$array_item[2] = (int)$value->TOTAL;
										break;
									default:	//Unknown
										$array_item[3] = (int)$value->TOTAL;
										break;
								}
							}
	        				break;
	        			case 2:	//Emotional Statistics
                            $items = $this->dbChartByEmotion($minChannel,$maxChannel,$dayPeriod,date('Y-m-d', strtotime($dayPeriod . ' +1 day')),$personTypeFilter,$minPersonType,$maxPersonType);

                            $array_item = [date('m/d/Y H:i:s', strtotime($dayPeriod)),0,0,0,0];
                            foreach ($items as $key => $value) {
                                $array_item[$key+1] = (float)$value;
                            }
	        				break;
                        case 3: //Alert Statistis
                            //If Alert Type is People:
                            if (!$alertTypeFilter) {
                                $items = $this->dbChartByAlerts($minChannel,$maxChannel,$dayPeriod,date('Y-m-d', strtotime($dayPeriod . ' +1 day')),$minPersonType,$maxPersonType,$minPerson,$maxPerson);
                                $array_item = [date('m/d/Y H:i:s', strtotime($dayPeriod)),0,0,0];
                                foreach ($items as $key => $value) {
                                    $array_item[$key+1] = $value;
                                }
                            } else {
                                $items = $this->dbChartByAlertsVagrancy($minChannel,$maxChannel,$dayPeriod,date('Y-m-d', strtotime($dayPeriod . ' +1 day')),$minVagType,$maxVagType);
                                $array_item = [date('m/d/Y H:i:s', strtotime($dayPeriod)),0,0];
                                foreach ($items as $key => $value) {
                                    $array_item[$key+1] = $value;
                                }
                            }
                            break;
	        		}
	        		$array[] = $array_item;
				}  //end foreach
        		break;
        	default:
        		# code...
        		break;
        }
    	
    	//dd($array);
        
    	$channels = Channel::orderBy('description', 'asc')->get();
        $persons = Person::orderBy('id','Asc')->get();

    	if (isset($chartType) && count($array)>0) $showChart=true;

    	return view('private_area.reports.charts')
            ->with('section_id',GlobalController::REPORTS_CHARTS)
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
    		->with('channels',$channels)
            ->with('personTypes',$this->getPersonTypes())
            ->with('alertTypes',$this->getAlertTypes())
            ->with('vagTypes',$this->getVagrancyTypes())
            ->with('persons',$persons)
    		->with('chartTypes', $this->getChartTypes())
    		->with('statTypes', $this->getStatisticTypes())
    		->with('params', $params)
    		->with('chartTitle', $chartTitle)
    		->with('showChart', $showChart)
            ->with('chart_data', json_encode($array))
    		->with('chart_colors', $colors);
    }


    public function showAlertsRemoveFilters() {
        $this->removeSessionFilters();
        return $this->showReports(GlobalController::REPORTS_ALERTS);
    }

    public function showAlertsWithFilters(Request $request) {
        $_timePeriod=null;
        $_channel=null;
        $_personType=null;
        $_person=null;
        $_export=false;
        $_alertType=null;
        $_vagType=null;

        if (isset($request->export) && ($request->export == 1 )) {
            $_export=true;
        }
        if (isset($request->timePeriod) && ($request->timePeriod> GlobalController::ALL_PERIOD )) {
            $_timePeriod = $request->timePeriod;
        }
        if (isset($request->channel) && ($request->channel> GlobalController::ALL_CHANNEL )) {
            $_channel = $request->channel;
        }
        if (isset($request->personType) && ($request->personType > GlobalController::ALL_PERSON_TYPES )) {
            $_personType = $request->personType;
        }
        if (isset($request->person) && ($request->person > GlobalController::ALL_PERSONS )) {
            $_person = $request->person;
        }
        if (isset($request->alertType) && ($request->alertType > GlobalController::ALL_ALERT_TYPES )) {
            $_alertType = $request->alertType;
        }
        if (isset($request->vagType) && ($request->vagType > GlobalController::ALL_VAG_TYPES )) {
            $_vagType = $request->vagType;
        }
        return $this->getAlerts($_export,$_timePeriod,$_channel,$_personType,$_person,$_alertType,$_vagType);
    }

    private function getAlerts($_export,$_timePeriod,$_channel,$_personType,$_person,$_alertType,$_vagType) {
        $timePeriod=GlobalController::ALL_PERIOD;
        $startTime=GlobalController::START_TIME;
        $endTime=GlobalController::END_TIME;
        $minChannel=GlobalController::MIN_CHANNEL;
        $maxChannel=GlobalController::MAX_CHANNEL;
        $minPersonType=GlobalController::MIN_PERSON_TYPE;
        $maxPersonType=GlobalController::MAX_PERSON_TYPE;
        $minPerson=GlobalController::MIN_PERSON;
        $maxPerson=GlobalController::MAX_PERSON;

        //By default, no alert type filter:
        $alertType=GlobalController::ALERT_TYPE_PEOPLE;
        $alertTypeFilter = false;
        $minVagType=GlobalController::BLOB_VAG_TYPE;
        $maxVagType=GlobalController::BODY_VAG_TYPE;

        $params = array();
        if (isset($_timePeriod) && ($_timePeriod > GlobalController::ALL_PERIOD )) {
            $params["timePeriod"] = $_timePeriod;
            session(['timePeriod' => $_timePeriod]);
            $endTime = date('Y-m-d H:i:s');
            //$endTime = date('Y-m-d h:i:s a', time());
            switch ($_timePeriod) {
                case 0: //Past Hour:
                    $startTime = date('Y-m-d H:i:s', strtotime($endTime . ' -1 hour'));
                    break;
                case 1: //Past Day:
                    $startTime = date('Y-m-d H:i:s', strtotime($endTime . ' -1 day'));
                    break;
                case 2: //Past Week:
                    $startTime = date('Y-m-d H:i:s', strtotime($endTime . ' -1 week'));
                    break;
                case 3: //Past Month:
                    $startTime = date('Y-m-d H:i:s', strtotime($endTime . ' -1 month'));
                    break;
            }  
        }
        if (isset($_channel) && ($_channel> GlobalController::ALL_CHANNEL )) {
            $minChannel = $_channel;
            $maxChannel = $_channel;
            $params["channel"] = $_channel;
            session(['channel' => $_channel]);
        }
        if (isset($_alertType) && ($_alertType> GlobalController::ALERT_TYPE_PEOPLE )) {
            $alertTypeFilter = true;
            $alertType = $_alertType;
            $params["alertType"] = $_alertType;
            session(['alertType' => $_alertType]);
            if ($_alertType == GlobalController::ALERT_TYPE_VAGRANCY) {
                //Check vagrancy type:
                if (isset($_vagType) && ($_vagType > GlobalController::ALL_VAG_TYPES )) {
                    //$_vagType possible values: -1=>all, 0=>blob, 1=>body
                    //If $_vagType is 0 => min=0 and max=0
                    //If $_vagType is 1 => min=1 and max=100
                    $minVagType = $_vagType;    
                    if ($_vagType==GlobalController::BLOB_VAG_TYPE) $maxVagType = $_vagType;
                    $params["vagType"] = $_vagType;
                    session(['vagType' => $_vagType]);
                }
            }      
        }

        if ($alertTypeFilter) {
            switch ($_alertType) {
                case GlobalController::ALERT_TYPE_VAGRANCY:
                    $items = $this->dbAlertsVagrancy($minChannel,$maxChannel,$startTime,$endTime,$minVagType,$maxVagType);
                    //dd($items);
                    break;
                case GlobalController::ALERT_TYPE_NONFACE:
                    $items = $this->dbAlertsNonface($minChannel,$maxChannel,$startTime,$endTime);
                    //dd($items);
                    break;
            }
        } else {
            if (isset($_personType) && ($_personType> GlobalController::ALL_PERSON_TYPES )) {
                $minPersonType = $_personType;
                $maxPersonType = $_personType;
                $params["personType"] = $_personType;
                session(['personType' => $_personType]);
            }

            if (isset($_person) && ($_person> GlobalController::ALL_PERSONS )) {
                $personFilter = true;
                $minPerson = $_person;
                $maxPerson = $_person;
                $params["person"] = $_person;
                session(['person' => $_person]);
            }

            $items = $this->dbAlerts($minChannel,$maxChannel,$startTime,$endTime,$minPersonType,$maxPersonType,$minPerson,$maxPerson);
        }

        $channels = Channel::orderBy('description', 'asc')->get()->keyBy("id");
        $persons = Person::orderBy('id','Asc')->get();

        //Check if export to CSV or show on screen:
        //TODO: At the momment, only PEOPLE filter
        if ($_export && !$alertTypeFilter) {
            return $this->exportAlerts($items,$channels,$persons);
        } else 
            return view('private_area.reports.alerts')
                ->with('section_id',GlobalController::REPORTS_ALERTS)
                ->with('engine_status',$this->checkEngineStatus())
                ->with('brightsign_enabled',$this->checkBrightSignDevice())
                ->with('items',$items)
                ->with('channels',$channels)
                ->with('confirmValues',$this->getConfirmedValues())
                ->with('timePeriods',$this->getReportsTimePeriod())
                ->with('personTypes',$this->getPersonTypes())
                ->with('alertTypes',$this->getAlertTypes())
                ->with('vagTypes',$this->getVagrancyTypes())
                ->with('persons',$persons)
                ->with('params',$params)
                ->with('alertType',$alertType);
    }

    public function exportAlerts($items,$channels,$persons) {
        $file_name = "AlertsLog_".time();
        ob_end_clean();
        ob_start();
        $excel_file = Excel::create($file_name, function ($excel) use ($items,$channels,$persons) {
            //Input from TS:
            $this->createAlertsLogSheet($excel,$items,$channels,$persons);
        })->download("csv");
    }

    private function createAlertsLogSheet($excel,$items,$channels,$persons) {
        $excel->sheet("Log-".date("Y-m-d"), function($sheet) use($items,$channels,$persons) {
            $style_center = array(
                'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );
            $LAST_COL="I";
            $row=1;
            $row_labels=array();
            $row_labels[]="DATE";
            $row_labels[]="TIME";
            $row_labels[]="ALERT ID";
            $row_labels[]="FACE ID";
            $row_labels[]="PERSON NAME";
            $row_labels[]="PERSON TYPE";
            $row_labels[]="CONFIDENCE";
            $row_labels[]="CAMERA";
            $row_labels[]="CONFIRMATION";
            $sheet->row($row, $row_labels);
            $sheet->getStyle("A$row:$LAST_COL$row")->getFont()->setBold(true);
            $sheet->getStyle("A$row:$LAST_COL$row")->getFont()->setSize(10);

            $personTypes = $this->getPersonTypes();
            $confirmValues= $this->getConfirmedValues();
            foreach ($items as $item) {
                $row++;
                $row_data=array();
                $row_data[]=date("d-m-Y", strtotime($item->captureTime));
                $row_data[]=date("H:i:s", strtotime($item->captureTime));
                $row_data[]=$item->ALERT_ID;
                $row_data[]=$item->FACE_ID;
                $row_data[]=$item->PERSON_NAME;
                $row_data[]=$personTypes[$item->person_type];
                $row_data[]=number_format($item->confidence,1)."%";
                $row_data[]=$channels[$item->CHANNEL_ID]->description;
                $row_data[]=$confirmValues[$item->confirmed];
                $sheet->row($row, $row_data);
            }
        });

    }

}
