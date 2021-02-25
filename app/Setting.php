<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'fs_settings';
	public $timestamps = false;

    protected $fillable = [
        'detection_threshold', 'bot_flag', 'email_flag', 'max_faces', 'cleanup_time', 'repaint_frame_ms', 'repaint_alert_ms', 'print_channels_ms', 'alert_killer_s', 'person_ignored_s', 'person_grabber_s', 'frame_list_max', 'frame_list_processed_max', 'stats_interval', 'tradeshow_flag', 'emotion_flag', 'camera_limit', 'face_ignored_s', 'brightsign_flag', 'alertscreening_flag', 'alt_emotion_flag', 'facescreening_flag', 'tracker_quality_threshold', 'alt_emotionRecognition_flag', 'staffAlert', 'vipAlert', 'maxMonths_alerts', 'maxWeeks_backups', 'removeAlert_flag', 'removeBackup_flag', 'nonfaceAlert_flag', 'pictureQuality_threshold', 'nonfaceAlert_channel_id', 'person_detection_threshold', 'vagrancyAlert_flag', 'vagrancy_ignored_s', 'vagrancy_time_s', 'frame_direction_line', 'nonface_bodyDetection_threshold', 'vagrancy_bodyDetection_threshold', 'nonfaceAlertOption', 'vagrancyAlertOption', 'vagrancyMaxBlobArea', 'vagrancyMinBlobArea', 'blacklistAlert','nonfaceBodyDetectionFrameInterval','nonfaceMaxUndetectedFrame','vagrancyMaxUndetectedFrame','vagrancyBodyDetectionFrameInterval','vagrancy_blobIgnored_m','vagrancyMaxBlobAlert','vagrancyMaxBodyAlert','vagrancy_blobChecking_m'
    ];
}
