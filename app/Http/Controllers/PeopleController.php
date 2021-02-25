<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Person;
use App\Knownface;

class PeopleController extends GlobalController
{
    public function listPeople() {
        $_listOrder=null;
        $_personName=null;
        $_personType=null;

        if (session('listOrder')!=null) {
            $_listOrder = session('listOrder');
        }
        if (session('personName')!=null) {
            $_personType = session('personType');
        }

        if (session('personType')!=null) {
            $_personType = session('personType');
        }
        return $this->getPeople($_personName,$_personType,$_listOrder);
    }

    public function listPeopleWithFilters(Request $request) {
        $_listOrder=null;
        $_personName=null;
        $_personType=null;

        if (isset($request->listOrder)) {
            $_listOrder = $request->listOrder;
        }
        if (isset($request->personName)) {
            $_personName = $request->personName;
        }
        if (isset($request->personType) && ($request->personType > GlobalController::ALL_PERSON_TYPES )) {
            $_personType = $request->personType;
        }
        return $this->getPeople($_personName,$_personType,$_listOrder);
    }

    public function listPeopleRemoveFilters() {
        $this->removeSessionFilters();
        return $this->getPeople(null,null,null);
    }

    public function getPeople($_personName,$_personType,$_listOrder) {
        $listOrder=GlobalController::LIST_ORDER;
        $personName="";
        $minPersonType=GlobalController::MIN_PERSON_TYPE;
        $maxPersonType=GlobalController::MAX_PERSON_TYPE;

        $params = array();
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

        $persons = Person::where('person_type', '>=', $minPersonType)
            ->where('person_type', '<=', $maxPersonType)
            ->where('description', 'like', "%$personName%")
            ->orderby('id',$listOrder)
            ->paginate(GlobalController::PAGE_SIZE);
        //dd($persons);

        $knownfaces = Knownface::all()->keyby("person_id");
        //dd($knownfaces);

        return view('private_area.people')
            ->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('personTypes',$this->getPersonTypes())
            ->with('personTypeColors',$this->getPersonTypeColors())
            ->with('listOrders',$this->getListOrders())
            ->with('personCount',$this->dbCountPersonsByType())
            ->with('params',$params)
            ->with('knownfaces',$knownfaces)
            ->with('persons',$persons);

    }

    public function getPersonPictures($person_id) {

        $items = $this->dbGetPersonPictures($person_id);
        return json_encode($items);
    }

    public function personStore(Request $request) {
        $person = new Person();
        $person->description = $request->person_name;
        $person->person_type = $request->person_type;
        $person->address = $request->address;
        $person->info = $request->info;
        $person->date_created = date('Y-m-d H:i:s');
        $person->save();

        return redirect()->route('people');
    } 

    public function personUpdate(Request $request) {
        $res = array();
        if (isset($request->person_id)) {
            $person = Person::find($request->person_id);
            if (isset($person)) {
                $person->description = $request->person_name;
                $person->person_type = $request->person_type;
                $person->address = $request->address;
                $person->info = $request->info;
                $person->update();
                $res["res"] = 1;
                $res["data"] = $person;
                $res["person_type_value"] = $this->getPersonTypes()[$person->person_type];
                $res["person_type_color"] = $this->getPersonTypeColors()[$person->person_type];
                $res["person_type_counts"] = $this->dbCountPersonsByType();
                
            }
        } else 
            $res["res"] = -1;
        return json_encode($res);
    } 

    public function personDelete(Request $request) {
        if (isset($request->person_id)) {
            $person = Person::find($request->person_id);
            if (isset($person)) {
                //Remove file picture:
                $knownfaces = Knownface::where('person_id','=',$person->id)->get();
                foreach ($knownfaces as $knownface) {
                    unlink(public_path().'/Media/KnownFaces/'.$knownface->img_name);
                }
                //Remove from database:
                $person->delete();
            }
        }
        return redirect()->route('people');
    } 

    public function personPictureAdd(Request $request) {
        $res=0;
        $person_id = $request->person_id;
        $person = Person::find($person_id);

        if ($request->hasFile('selectedFile')) {
            //$file_original_name = $request->selectedFile->getClientOriginalName();
            //The next instruction save the file in 'newPersons' folder with a hash name:
            $request->file('selectedFile')->store('Media/newPersons','public_path');
            //Get the file name in the server:
            $file_server_name = $request->file('selectedFile')->hashName();
            
            $file_server_path = public_path()."/Media/newPersons/".$file_server_name;

            $res = $person_id ;
        } else {
            $res=-1;
        } 
        return $res;
    }

    public function personTypes() {
        return json_encode($this->getPersonTypes());
    }
}