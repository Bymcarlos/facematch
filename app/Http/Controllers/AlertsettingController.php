<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Emailrecepient;
use App\Bot;
use App\Telegramuser;
use Telegram\Bot\Api;

class AlertsettingController extends GlobalController {

    public function showAlertSetting($section_id=GlobalController::ALERTSETTINGS_EMAIL) {
    	if ($section_id==GlobalController::ALERTSETTINGS_EMAIL)
            return $this->getAlertSettingsEmails();
        else
            return $this->getAlertSettingsTelegram();
    }

    private function getAlertSettingsEmails() {
        $emails = Emailrecepient::orderBy('email_address', 'asc')->get();
    	return view('private_area.alert.setting.emails')
        	->with('section_id',GlobalController::ALERTSETTINGS_EMAIL)
            ->with('section_tel',GlobalController::ALERTSETTINGS_TELEGRAM)
        	->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice())
            ->with('emails',$emails);
    }

    private function getAlertSettingsTelegram() {
        $bot = Bot::find(1);
        $telUsers = Telegramuser::orderBy("firstName")->get();
    	return view('private_area.alert.setting.telegram')
        	->with('section_id',GlobalController::ALERTSETTINGS_TELEGRAM)
            ->with('section_em',GlobalController::ALERTSETTINGS_EMAIL)
            ->with('bot',$bot)
            ->with('telUsers',$telUsers)
        	->with('engine_status',$this->checkEngineStatus())
            ->with('brightsign_enabled',$this->checkBrightSignDevice());
    }

    public function emailStore(Request $request) {
        $email = new Emailrecepient();
        $email->email_address = $request->em_email;
        $email->description = $request->em_description;

        $email->save();
        return redirect()->route('alert.setting');
    }

    public function emailUpdate(Request $request, $email_id) {
        $email = Emailrecepient::find($email_id);
        $email->email_address = $request->em_email;
        $email->description = $request->em_description;

        $email->update();
        return redirect()->route('alert.setting');
    }

    public function emailDestroy(Emailrecepient $emailrec) {
        $emailrec->delete();
        return redirect()->route('alert.setting');
    }

    public function telegramBotUpdate(Request $request, $bot_id) {
        $bot = Bot::find($bot_id);
        $bot->token = $request->tel_token;
        $bot->update();
        return redirect()->route('alert.setting',['section'=>GlobalController::ALERTSETTINGS_TELEGRAM]);
    }

    public function telegramUserDestroy($tel_user_id) {
        $tel_user = Telegramuser::find($tel_user_id);
        $tel_user->delete();
        return redirect()->route('alert.setting',['section'=>GlobalController::ALERTSETTINGS_TELEGRAM]);
    }

    public function telegramUserTrusted(Request $request) {
        $tel_user = Telegramuser::find($request->user_id);
        if ($tel_user->trusted == 0)
            $tel_user->trusted = 1;
        else
            $tel_user->trusted = 0;
        $tel_user->update();

        return $tel_user->trusted;
    }
    public function testBot(Request $request) {
        $bot = Bot::find($request->bot_id);
        $telegram = new Api($bot->token);

        $response = $telegram->getMe();
        return $response->getFirstName();

        /*
        //Return more info about bot service test?
        $result = array();
        $result['id'] = $response->getId();
        $result['firstName'] = $response->getFirstName();
        $result['userName'] = $response->getUsername();
        return response()->json($result);
        */
        
    }
}
