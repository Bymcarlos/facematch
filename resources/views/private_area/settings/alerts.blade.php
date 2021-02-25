@extends('layouts.intranet')
@section('css_custom')


@endsection
@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">Settings</li>
</ol>

<div class="row ml-1">
    <div class="col-6 text-left">
        @if ($auth_user_level<=2) 
        <div class="small text-left">Email Alerts</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="email_flag" data-state="{{$settings->email_flag}}" onclick="fieldState('email_flag',{{$settings->email_flag}})" {{$check_states[$settings->email_flag]}}></td>
                <td class="col-10 text-left small">Sends Alerts to Email Recipients</td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Email Alerts','email_flag')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=1)
        <div class="small text-left mt-2">Database Cleanup</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td class="small text-left col-4">Time to perform Database Cleanup</td>
                <td class="small text-left col-7">
                    <input type="text" class="form-control-inline text-center" id="cleanup_time" size="12" value="{{date('h:i A', strtotime($settings->cleanup_time))}}" data-value="{{date('H:i', strtotime($settings->cleanup_time))}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_time_cleanup" onclick="editTimeCleanup()"></i>
                </td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Database Cleanup','cleanup_time')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=1)
        <div class="small text-left mt-2">Emotion Detection</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="emotion_flag" data-state="{{$settings->emotion_flag}}" onclick="fieldState('emotion_flag',{{$settings->emotion_flag}})" {{$check_states[$settings->emotion_flag]}}></td>
                <td class="col-10 text-left small">Enable Emotion Recognition</td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Emotion Detection','emotion_flag')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=2)
        <div class="small text-left mt-2">BrightSign Device</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="brightsign_flag" data-state="{{$settings->brightsign_flag}}" onclick="fieldState('brightsign_flag',{{$settings->brightsign_flag}})" {{$check_states[$settings->brightsign_flag]}}></td>
                <td class="col-10 text-left small">Enable BrightSign Integration</td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('BrightSign Device','brightsign_flag')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=1)
        <div class="small text-left mt-2">Alt Emotion Detection</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="alt_emotion_flag" data-state="{{$settings->alt_emotion_flag}}" onclick="fieldState('alt_emotion_flag',{{$settings->alt_emotion_flag}},'alt_emotionRecognition_flag','disabled')" {{$check_states[$settings->alt_emotion_flag]}}></td>
                <td class="col-5 text-left small">Enable Alt Emotion Detection</td>
                <td width="1%"><input type="checkbox" class="form-control small" id="alt_emotionRecognition_flag" data-state="{{$settings->alt_emotionRecognition_flag}}" onclick="fieldState('alt_emotionRecognition_flag',{{$settings->alt_emotionRecognition_flag}})" {{$check_states[$settings->alt_emotionRecognition_flag]}} @if ($settings->alt_emotion_flag==0) disabled @endif></td>
                <td class="col-5 text-left small">Enable Alt Emotion Recognition</td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Enable Alt Emotion Detection','alt_emotionRecognition_flag')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=1)
        <div class="small text-left mt-2">Alert Screening</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="alertscreening_flag" data-state="{{$settings->alertscreening_flag}}" onclick="fieldState('alertscreening_flag',{{$settings->alertscreening_flag}})" {{$check_states[$settings->alertscreening_flag]}}></td>
                <td class="col-10 text-left small">Enable Alert Screening</td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Alert Screening','alertscreening_flag')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=2)
        <div class="small text-left mt-2">Alert Cleanup Options</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                @if ($auth_user_level<=1)
                <td width="1%"><input type="checkbox" class="form-control small" id="removeAlert_flag" data-state="{{$settings->removeAlert_flag}}" onclick="fieldState('removeAlert_flag',{{$settings->removeAlert_flag}},'cleanup_number_months_edit','hidden')" {{$check_states[$settings->removeAlert_flag]}}></td>
                @else
                <td width="1%"><input type="checkbox" class="form-control small" {{$check_states[$settings->removeAlert_flag]}} disabled></td>
                @endif
                <td class="col-3 text-left small">Enable Alert Cleanup</td>
                <td class="small text-left col-5" id='maxMonths_alerts'>Number of months of Alerts to keep after Cleanup</td>
                <td class="small text-left col-2">
                    <input type="text" class="form-control-inline text-center" size="4" value="{{$settings->maxMonths_alerts}}" readonly>
                    <i id="cleanup_number_months_edit" class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('maxMonths_alerts',{{$settings->maxMonths_alerts}})" @if ($settings->removeAlert_flag==0) hidden @endif></i>
                </td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Alert Cleanup Options','removeAlert_flag')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=1)
        <div class="small text-left mt-2">Picture Quality</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td class="small text-left col-4" id='pictureQuality_threshold'>Picture Quality Threshold</td>
                <td class="small text-left col-7">
                    <input type="text" class="form-control-inline text-center" size="4" value="{{$settings->pictureQuality_threshold}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('pictureQuality_threshold',{{$settings->pictureQuality_threshold}})"></i>
                </td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Picture Quality','pictureQuality_threshold')"></i></td>
            </tr>
        </table>
        @endif
        <div class="small text-left mt-2">NonFace Alerts</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            @if ($auth_user_level<=1)
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="nonfaceAlert_flag" data-state="{{$settings->nonfaceAlert_flag}}" onclick="fieldState('nonfaceAlert_flag',{{$settings->nonfaceAlert_flag}},'nonface_bodyDetection_threshold_range','disabled')" {{$check_states[$settings->nonfaceAlert_flag]}}></td>
                <td class="col-3 text-left small">Enable Nonface Alert</td>
                <td class="col-4 text-left small">Nonface Body Detection Threshold:<input type="text" id="nonface_bodyDetection_threshold" class="form-control-inline text-center ml-1 mr-1" size="3" value="{{$settings->nonface_bodyDetection_threshold}}%" readonly></td>
                <td class="col-3 text-left small"><input type="range" id="nonface_bodyDetection_threshold_range" class="form-control-inline text-center" min="5" max="100" step="5" oninput="showVal('nonface_bodyDetection_threshold',this.value)" onchange="showVal('nonface_bodyDetection_threshold',this.value,true)" value="{{$settings->nonface_bodyDetection_threshold}}" @if ($settings->nonfaceAlert_flag==0) disabled @endif></td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('NonFace Alerts','nonfaceAlert_flag')"></i></td>
            </tr>
            <tr>
                <td class="small text-left col-4" colspan="2" id="nonfaceMaxUndetectedFrame">Max Undetected Consecutive Frames:</td>
                <td class="small text-left" colspan="3"><input type="text" class="form-control-inline text-center" size="6" value="{{$settings->nonfaceMaxUndetectedFrame}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('nonfaceMaxUndetectedFrame',{{$settings->nonfaceMaxUndetectedFrame}})"></i></td>
            </tr>
            <tr>
                <td class="small text-left col-4" colspan="2" id="nonfaceBodyDetectionFrameInterval">Nonface Direction Line:</td>
                <td class="small text-left" colspan="3"><input type="text" class="form-control-inline text-center" size="6" value="{{$settings->nonfaceBodyDetectionFrameInterval}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('nonfaceBodyDetectionFrameInterval',{{$settings->nonfaceBodyDetectionFrameInterval}})"></i></td>
            </tr>
            <tr>
                <td class="small text-left col-4" colspan="2" id="frame_direction_line">Nonface Direction Line:</td>
                <td class="small text-left" colspan="3"><input type="text" class="form-control-inline text-center" size="6" value="{{$settings->frame_direction_line}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('frame_direction_line',{{$settings->frame_direction_line}})"></i></td>
            </tr>
            @endif
            <tr>
                <td class="small text-left col-4" colspan="2">Alert Options:</td>
                <td class="small text-left" colspan="3">
                    <div class="row">
                        <div class="col-1"><input type="checkbox" id="nonfaceAlertOption_s" class="form-control-inline small" {{$check_nonface_alert_s}} onclick="changeAlertState('nonfaceAlertOption','s')"></div>
                        <div class="col-3 text-left">System</div>                    
                        <div class="col-1"><input type="checkbox" id="nonfaceAlertOption_e" class="form-control-inline small" {{$check_nonface_alert_e}} onclick="changeAlertState('nonfaceAlertOption','e')"></div>
                        <div class="col-3 text-left">Email</div>
                        <div class="col-1"><input type="checkbox" id="nonfaceAlertOption_t" class="form-control-inline small" {{$check_nonface_alert_t}} onclick="changeAlertState('nonfaceAlertOption','t')"></div>
                        <div class="col-3 text-left">Telegram</div>
                    </div>
                </td>
                @if ($auth_user_level>1)
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('NonFace Alerts','nonfaceAlert_flag')"></i>
                </td>
                @endif
            </tr>
        </table>
    </div>
    <div class="col-6">
        @if ($auth_user_level<=2)
        <div class="small text-left">Telegram Alerts</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="bot_flag" data-state="{{$settings->bot_flag}}" onclick="fieldState('bot_flag',{{$settings->bot_flag}})" {{$check_states[$settings->bot_flag]}}></td>
                <td class="col-10 small text-left">Sends Alerts to Telegram App Users</td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Telegram Alerts','bot_flag')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=1)
        <div class="small text-left mt-2">Faces Limit</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td class="small text-left col-4" id="max_faces">Number of Faces to Keep After Cleanup:</td>
                <td class="small text-left col-7">
                    <input type="text" class="form-control-inline text-center" size="4" value="{{$settings->max_faces}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('max_faces',{{$settings->max_faces}})"></i>
                </td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Faces Limit','max_faces')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=2)
        <div class="small text-left mt-2">Trade Show Mode</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="tradeshow_flag" data-state="{{$settings->tradeshow_flag}}" onclick="fieldState('tradeshow_flag',{{$settings->tradeshow_flag}})" {{$check_states[$settings->tradeshow_flag]}}></td>
                <td class="col-10 small text-left">Enable Trade Show Mode</td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Trade Show Mode','tradeshow_flag')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=1)
        <div class="small text-left mt-2">Camera Limit</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td class="small text-left col-6" id="camera_limit">Maximun Number of Cameras to Run Recognition</td>
                <td class="small text-left col-5">
                    <input type="text" class="form-control-inline text-center" size="2" value="{{$settings->camera_limit}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('camera_limit',{{$settings->camera_limit}})"></i>
                </td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Camera Limit','camera_limit')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=2)
        <div class="small text-left mt-3">Alert Frequency</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td class="small text-left col-6" id="person_ignored_s">Time Between Alerts for a Single Person (Seconds)</td>
                <td class="small text-left col-5">
                    <input type="text" class="form-control-inline text-center" size="3" value="{{$settings->person_ignored_s}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('person_ignored_s',{{$settings->person_ignored_s}})"></i>
                </td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Alert Frequency','person_ignored_s')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=2)
        <div class="small text-left mt-2">Face Frequency</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                @if ($auth_user_level<=1)
                <td width="1%"><input type="checkbox" class="form-control small" id="facescreening_flag" data-state="{{$settings->facescreening_flag}}" onclick="fieldState('facescreening_flag',{{$settings->facescreening_flag}},'facescreening_flag_edit','hidden')" {{$check_states[$settings->facescreening_flag]}}></td>
                @else
                <td width="1%"><input type="checkbox" class="form-control small" {{$check_states[$settings->facescreening_flag]}} disabled></td>
                @endif
                <td class="col-3 text-left small">Enable Face Frequency Screening</td>
                <td class="small text-left col-5" id="face_ignored_s">Time Between Faces for a Single Person (Seconds)</td>
                <td class="small text-left col-2">
                    <input type="text" class="form-control-inline text-center" size="4" value="{{$settings->face_ignored_s}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" id="facescreening_flag_edit" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('face_ignored_s',{{$settings->face_ignored_s}})" @if ($settings->facescreening_flag==0) hidden @endif></i>
                </td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Face Frequency','face_ignored_s')"></i></td>
            </tr>
        </table>
        @endif
        @if ($auth_user_level<=1)
        <div class="small text-left mt-2">Backup Files Cleanup Options</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="removeBackup_flag" data-state="{{$settings->removeBackup_flag}}" onclick="fieldState('removeBackup_flag',{{$settings->removeBackup_flag}},'removeBackup_flag_edit','hidden')" {{$check_states[$settings->removeBackup_flag]}}></td>
                <td class="col-3 text-left small">Enable Backup Cleanup</td>
                <td class="small text-left col-5" id="maxWeeks_backups">Number of Weeks of Backups to Keep Afeter Cleanup</td>
                <td class="small text-left col-2">
                    <input type="text" class="form-control-inline text-center" size="4" value="{{$settings->maxWeeks_backups}}" readonly>
                    <i class="fas fa-fw fa-edit fa-lg" id="removeBackup_flag_edit" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('maxWeeks_backups',{{$settings->maxWeeks_backups}})" @if ($settings->removeBackup_flag==0) hidden @endif></i>
                </td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Backup Files Cleanup Options','maxWeeks_backups')"></i></td>
            </tr>
        </table>
        @endif
        <div class="small text-left mt-2">Alert Option</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            <tr>
                <td class="col-11">
                    <div class="row">
                        <div class="col-sm-12 col-xl-6">
                            <table class="table-bordered table-sm" width="100%" cellspacing="0">
                                <tr>
                                    <td class="small" width="1%">VIP:</td>
                                    <td class="small" width="1%"><input type="checkbox" id="vipAlert_s" class="form-control-inline small mr-1" {{$check_vipAlert_s}} onclick="changeAlertState('vipAlert','s')">System</td>
                                    <td class="small" width="1%"><input type="checkbox" id="vipAlert_e" class="form-control-inline small mr-1" {{$check_vipAlert_e}} onclick="changeAlertState('vipAlert','e')">Email</td>
                                    <td class="small" width="1%"><input type="checkbox" id="vipAlert_t" class="form-control-inline small mr-1" {{$check_vipAlert_t}} onclick="changeAlertState('vipAlert','t')">Telegram</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-12 col-xl-6">
                            <table class="table-bordered table-sm" width="100%" cellspacing="0">
                                <tr>
                                    <td class="small" width="1%">Blacklist:</td>
                                    <td class="small" width="1%"><input type="checkbox" id="blacklistAlert_s" class="form-control-inline small mr-1" {{$check_blacklistAlert_s}} onclick="changeAlertState('blacklistAlert','s')">System</td>
                                    <td class="small" width="1%"><input type="checkbox" id="blacklistAlert_e" class="form-control-inline small mr-1" {{$check_blacklistAlert_e}} onclick="changeAlertState('blacklistAlert','e')">Email</td>
                                    <td class="small" width="1%"><input type="checkbox" id="blacklistAlert_t" class="form-control-inline small mr-1" {{$check_blacklistAlert_t}} onclick="changeAlertState('blacklistAlert','t')">Telegram</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-12 col-xl-6">
                            <table class="table-bordered table-sm" width="100%" cellspacing="0">
                                <tr>
                                    <td class="small" width="1%">Staff:</td>
                                    <td class="small" width="1%"><input type="checkbox" id="staffAlert_s" class="form-control-inline small mr-1" {{$check_staffAlert_s}} onclick="changeAlertState('staffAlert','s')">System</td>
                                    <td class="small" width="1%"><input type="checkbox" id="staffAlert_e" class="form-control-inline small mr-1" {{$check_staffAlert_e}} onclick="changeAlertState('staffAlert','e')">Email</td>
                                    <td class="small" width="1%"><input type="checkbox" id="staffAlert_t" class="form-control-inline small mr-1" {{$check_staffAlert_t}} onclick="changeAlertState('staffAlert','t')">Telegram</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Alert Option','vipAlert')"></i></td>
            </tr>
        </table>
        <div class="small text-left mt-2">Vagrancy Alerts</div>
        <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
            @if ($auth_user_level<=1)
            <tr>
                <td width="1%"><input type="checkbox" class="form-control small" id="vagrancyAlert_flag" data-state="{{$settings->vagrancyAlert_flag}}" onclick="fieldState('vagrancyAlert_flag',{{$settings->vagrancyAlert_flag}},'vagrancy_bodyDetection_threshold_range','disabled')" {{$check_states[$settings->vagrancyAlert_flag]}}></td>
                <td class="text-left small">Enable Vagrancy Alert</td>
                <td class="text-center small"><button class="btn btn-sm btn-info mr-2" data-toggle="modal" data-target="#modal_running_time">Set Running Time</button><button class="btn btn-sm btn-info ml-2" data-toggle="modal" data-target="#modal_floor_area">Set Floor Area</button></td>
                <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Vagrancy Alerts','vagrancyAlert_flag')"></i></td>
            </tr>
            <tr>
                <td class="col-11 text-center small" colspan="4">Vagrancy Body Detection Threshold:<input type="text" id="vagrancy_bodyDetection_threshold" class="form-control-inline text-center ml-1 mr-1" size="3" value="{{$settings->vagrancy_bodyDetection_threshold}}%" readonly><input type="range" id="vagrancy_bodyDetection_threshold_range" class="form-control-inline text-center ml-3" min="5" max="100" step="5" oninput="showVal('vagrancy_bodyDetection_threshold',this.value)" onchange="showVal('vagrancy_bodyDetection_threshold',this.value,true)" value="{{$settings->vagrancy_bodyDetection_threshold}}" @if ($settings->vagrancyAlert_flag==0) disabled @endif></td>
            </tr>
            <tr>
                <td colspan="5">
                   <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
                    <tr> 
                        <td class="col-4 text-left small" id="vagrancyMaxUndetectedFrame">Max Undetected Consecutive Frames:</td>
                        <td class="col-2 small"><input type="text" class="form-control-inline text-center ml-1 mr-1" size="2" value="{{$settings->vagrancyMaxUndetectedFrame}}" readonly><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('vagrancyMaxUndetectedFrame',{{$settings->vagrancyMaxUndetectedFrame}})"></i></td>
                        <td class="col-4 text-left small" id="vagrancyBodyDetectionFrameInterval">Body / Blob Dectection Frame Interval:</td>
                        <td class="col-2 small"><input type="text" class="form-control-inline text-center ml-1 mr-1" size="2" value="{{$settings->vagrancyBodyDetectionFrameInterval}}" readonly><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('vagrancyBodyDetectionFrameInterval',{{$settings->vagrancyBodyDetectionFrameInterval}})"></i></td>
                    </tr>
                   </table>
                   <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
                    <tr> 
                        <td class="col-4 text-left small" id="vagrancy_time_s">Body Detection Timer to Show the Vagrancy Alerts (Minutes):</td>
                        <td class="col-2 small"><input type="text" class="form-control-inline text-center ml-1 mr-1" size="1" value="{{$settings->vagrancy_time_s}}" readonly><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('vagrancy_time_s',{{$settings->vagrancy_time_s}})"></i></td>
                        <td class="col-4 text-left small" id="vagrancy_blobIgnored_m">Blob Detection Timer to Show the Vagrancy Alerts (Minutes):</td>
                        <td class="col-2 small"><input type="text" class="form-control-inline text-center ml-1 mr-1" size="2" value="{{$settings->vagrancy_blobIgnored_m}}" readonly><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('vagrancy_blobIgnored_m',{{$settings->vagrancy_blobIgnored_m}})"></i></td>
                    </tr>
                   </table>
                   <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
                    <tr> 
                        <td class="col-4 text-left small" id="vagrancy_ignored_s">Body Vagrancy Alerts Frequency for the Same Body (Minutes):</td>
                        <td class="col-2 small"><input type="text" class="form-control-inline text-center ml-1 mr-1" size="2" value="{{$settings->vagrancy_ignored_s}}" readonly><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('vagrancy_ignored_s',{{$settings->vagrancy_ignored_s}})"></i></td>
                        <td class="col-4 text-left small" id="vagrancy_blobChecking_m">Blob Detection Time Interval (Hour):</td>
                        <td class="col-2 small"><input type="text" class="form-control-inline text-center ml-1 mr-1" size="2" value="{{$settings->vagrancy_blobChecking_m}}" readonly><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('vagrancy_blobChecking_m',{{$settings->vagrancy_blobChecking_m}})"></i></td>
                    </tr>
                   </table>
                   <!--
                   <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
                    <tr> 
                        <td class="col-4 text-left small" id="vagrancyMinBlobArea">Min Blob Area:</td>
                        <td class="col-2 small"><input type="text" class="form-control-inline text-center ml-1 mr-1" size="2" value="{{$settings->vagrancyMinBlobArea}}" readonly><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('vagrancyMinBlobArea',{{$settings->vagrancyMinBlobArea}})"></i></td>
                        <td class="col-4 text-left small" id="vagrancyMaxBlobArea">Max Blob Area:</td>
                        <td class="col-2 small"><input type="text" class="form-control-inline text-center ml-1 mr-1" size="2" value="{{$settings->vagrancyMaxBlobArea}}" readonly><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('vagrancyMaxBlobArea',{{$settings->vagrancyMaxBlobArea}})"></i></td>
                    </tr>
                   </table>
                   -->
                   <table class="table-bordered table-sm bg-light" width="100%" cellspacing="0">
                    <tr> 
                        <td class="col-4 text-left small" id="vagrancyMaxBodyAlert">Body Alerts Maximun Number per Hour:</td>
                        <td class="col-2 small"><input type="text" class="form-control-inline text-center ml-1 mr-1" size="2" value="{{$settings->vagrancyMaxBodyAlert}}" readonly><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_numeric_value" onclick="editNumericValue('vagrancyMaxBodyAlert',{{$settings->vagrancyMaxBodyAlert}})"></i></td>
                        <td class="col-6" colspan="2">
                            <table width="100%">
                                <tr>
                                    <td class="small" width="30%">Alert Options:</td>
                                    <td class="small" width="25%"><input type="checkbox" id="vagrancyAlertOption_s" class="form-control-inline small mr-1" {{$check_vagrancyAlertOption_s}} onclick="changeAlertState('vagrancyAlertOption','s')">System</td>
                                    <td class="small" width="20%"><input type="checkbox" id="vagrancyAlertOption_e" class="form-control-inline small mr-1" {{$check_vagrancyAlertOption_e}} onclick="changeAlertState('vagrancyAlertOption','e')">Email</td>
                                    <td class="small" width="25%"><input type="checkbox" id="vagrancyAlertOption_t" class="form-control-inline small mr-1" {{$check_vagrancyAlertOption_t}} onclick="changeAlertState('vagrancyAlertOption','t')">Telegram</td>
                                    @if ($auth_user_level>=3)
                                    <td class="text-center" width="1%"><i class="fas fa-fw fa-info-circle" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_info" onclick="loadInfo('Vagrancy Alerts','vagrancyAlert_flag')"></i></td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>
                   </table>
                </td>
            </tr>
            @endif
        </table>
    </div>
</div>

<!-- Edit Time Cleanup -->
<div class="modal fade" id="modal_time_cleanup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" method="POST" action="{{route('settings.cleanup.time')}}">
                    {{ csrf_field() }}
                    <input type="hidden" id="_method" name="_method" value="PUT"/>
                    <div class="form-group mb-1">
                        <label class="control-label" for="value" id="title">Time to perform Database Cleanup:</label>
                    </div>
                    <div class="form-group mt-1 row">
                        <div class="col-4"></div>
                        <div class="col-4">
                            <input type="time" id="value" name="value" class="form-control" min="00:00" max="23:59" required/>
                        </div>
                        <div class="col-4"></div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Submit</button>
                        <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Numeric Value -->
<div class="modal fade" id="modal_numeric_value" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" method="POST" action="{{route('settings.value.numeric.update')}}">
                    {{ csrf_field() }}
                    <input type="hidden" id="_method" name="_method" value="PUT"/>
                    <input type="hidden" id="field" name="field"/>
                    <div class="form-group mb-1">
                        <label class="control-label" for="value" id="title"></label>
                    </div>
                    <div class="form-group mt-1 row">
                        <div class="col-4"></div>
                        <div class="col-4">
                            <input type="number" id="value" name="value" class="form-control" required/>
                        </div>
                        <div class="col-4"></div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Submit</button>
                        <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Info dialog -->
<div class="modal" id="modal_info" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <span id="description"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Running time dialog -->
<div class="modal" id="modal_running_time" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form id="form_edit" method="POST" action="{{route('settings.channel.running_time.saves')}}">
        {{ csrf_field() }}
            <div class="modal-header">
                <h5 class="modal-title" id="title">Vagrancy Alerts Running Time</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
                    <thead class="bg-light small">
                        <tr>
                            <th>Channels</th>
                            <th>Weekday Start Time</th>
                            <th>Weekday End Time</th>
                            <th>Weekend Start Time</th>
                            <th>Weekend End Time</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @foreach($channels as $channel)
                        @php ($wd_st=date('H:i', strtotime("00:00")))
                        @php ($wd_et=date('H:i', strtotime("00:00")))
                        @php ($wk_st=date('H:i', strtotime("00:00")))
                        @php ($wk_et=date('H:i', strtotime("00:00")))
                        @if (isset($vagfloorareas[$channel->id]))
                            @php ($wd_st=date('H:i', strtotime($vagfloorareas[$channel->id]->weekdayStartTime)))
                            @php ($wd_et=date('H:i', strtotime($vagfloorareas[$channel->id]->weekdayEndTime)))
                            @php ($wk_st=date('H:i', strtotime($vagfloorareas[$channel->id]->weekendStartTime)))
                            @php ($wk_et=date('H:i', strtotime($vagfloorareas[$channel->id]->weekendEndTime)))
                        @endif
                        <tr>
                            <td>{{$channel->description}}</td>
                            <td><input type="time" name="weekday_start_time_{{$channel->id}}" class="form-control" min="00:00" max="23:59" value="{{$wd_st}}" required/></td>
                            <td><input type="time" name="weekday_end_time_{{$channel->id}}" class="form-control" min="00:00" max="23:59" value="{{$wd_et}}" required/></td>
                            <td><input type="time" name="weekend_start_time_{{$channel->id}}" class="form-control" min="00:00" max="23:59" value="{{$wk_st}}" required/></td>
                            <td><input type="time" name="weekend_end_time_{{$channel->id}}" class="form-control" min="00:00" max="23:59" value="{{$wk_et}}" required/></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- Set Floor Area dialog -->
<div class="modal" id="modal_floor_area" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title">Set Floor Area</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-left">
        <form id="form_floor_area" method="POST" action="{{route('settings.channel.floor_area.show')}}">
            {{ csrf_field() }}
            <div class="form-group mt-1 row">
                <div class="col-4">Select Channel:</div>
                <div class="col-8">
                    <select id="channel" name="channel" class="form-control" required>
                        <option disabled selected>-Select the channel</option>
                        @foreach($channels as $channel)
                            <option value="{{$channel->id}}">{{$channel->description}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group mt-2">
                <button type="submit" class="btn crud-submit btn-success">Select</button>
                <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


@section('js_custom')
<script type="text/javascript">
function fieldState(db_field,curr_state,db_field_related=null,attr=null) {
    var curr_state = $('#'+db_field).data("state");
    var new_state = 0;
    if (curr_state==0) new_state = 1;
    $.ajax({
        type: 'PUT',
        url: "{{route('settings.state.change')}}",
        data: { _token: "{{ csrf_token() }}", field: db_field, state: curr_state },
        success: function(data){
            console.log("OK: "+data);
            if (data!=new_state) {
                alert("Can not change field state, please retry.");
                if (curr_state==1) {
                    $('#'+db_field).prop('checked', true);
                }
                else {
                    $('#'+db_field).prop('checked', false);
                }
            } else {
                //Changed ok:
                if (db_field_related) {
                    if (new_state==1)
                        $('#'+db_field_related).removeAttr(attr);
                    else
                        $('#'+db_field_related).attr(attr, true);
                }
            }
            $('#'+db_field).data("state",data);
            //Check if it is brightsign_flag to show/hide main menu brightsign tab:
            if (db_field=="brightsign_flag") {
                if (data==1)
                    $('#mnu_brightsign').removeAttr('style');
                else
                    $('#mnu_brightsign').css('display','none');
            }
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
        }
    });
}
function editTimeCleanup() {
    var time = $('#cleanup_time').data("value");
    $('#modal_time_cleanup #form_edit #value').val(time);
}
function editNumericValue(field,value,min,max) {
    $('#modal_numeric_value #form_edit #title').text($('#'+field).text());
    $('#modal_numeric_value #form_edit #field').val(field);
    $('#modal_numeric_value #form_edit #value').val(value);
    $('#modal_numeric_value #form_edit #value').attr('min',min);
    $('#modal_numeric_value #form_edit #value').attr('max',max);
}
function showVal(db_field,newVal,saveVal=false){
    $('#'+db_field).val(newVal+"%");
    if (saveVal) {
        $.ajax({
            type: 'PUT',
            url: "{{route('settings.value.numeric.update')}}",
            data: { _token: "{{ csrf_token() }}", field: db_field, value: newVal, ws: true },
            success: function(data){
                console.log("OK: "+data);
                if (data!=newVal) {
                    alert("Can not change field value, please retry.");
                }
                $('#'+db_field).val(data+"%");
                $('#'+db_field+'_range').val(data);
            },
            error: function (xhr, status, error) {
                //var err = eval("(" + xhr.responseText + ")");
                console.log("error:"+error.Message);
            }
        });
    }
}
function changeAlertState(db_field,alertType) {
    var state_s = $('#'+db_field+'_s').is(":checked") ? 1 : 0;
    var state_e = $('#'+db_field+'_e').is(":checked") ? 1 : 0;
    var state_t = $('#'+db_field+'_t').is(":checked") ? 1 : 0;

    $.ajax({
        type: 'PUT',
        url: "{{route('settings.alert.state.change')}}",
        data: { _token: "{{ csrf_token() }}", field: db_field, alert_s: state_s, alert_e: state_e, alert_t: state_t },
        success: function(data){
            //console.log("OK: "+data);
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
        }
    });
}
function loadInfo(title,db_field) {
    $('#modal_info #title').text(title);
    $.ajax({
        type: 'PUT',
        url: "{{route('settings.field.info')}}",
        data: { _token: "{{ csrf_token() }}", field: db_field },
        success: function(data){
            console.log(data);
            $('#modal_info #description').html(data);
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
        }
    });
}
</script>
@endsection