<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Don't need registration, so comment the Auth:routes() method, and add only login/pass routes:
//Auth::routes();

//Login routes:
Route::get('login', function () {
    return view('welcome');
})->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');


Route::get('/home', 'HomeController@index')->name('home');

Route::put('engine/start','GlobalController@startEngine')->name('engine.start');
Route::put('engine/stop','GlobalController@stopEngine')->name('engine.stop');
Route::get('engine/check','GlobalController@checkEngineStatus')->name('engine.check');

Route::get('faces', 'FacesController@listFaces')->name('faces');
Route::post('faces', 'FacesController@listFacesWithFilters')->name('faces.filters');
Route::delete('faces', 'FacesController@listFacesRemoveFilters')->name('faces.filters.remove');
Route::post('faces/person/add/', 'FacesController@addPersonFace')->name('faces.person.add');

Route::get('bodies', 'BodiesController@listBodies')->name('bodies');
Route::post('bodies', 'BodiesController@listBodiesWithFilters')->name('bodies.filters');
Route::delete('bodies', 'BodiesController@listBodiesRemoveFilters')->name('bodies.filters.remove');
Route::get('body/export/save/{body_id}', 'BodiesController@bodyExportSave')->name('body.export.save');

Route::get('alerts', 'AlertsController@listAlerts')->name('alerts');
Route::post('alerts', 'AlertsController@listAlertsWithFilters')->name('alerts.filters');
Route::delete('alerts', 'AlertsController@listAlertsRemoveFilters')->name('alerts.filters.remove');
Route::put('alerts', 'AlertsController@alertStateUpdate')->name('alerts.state.update');

Route::get('vagalerts', 'VagAlertsController@listVagAlerts')->name('vagalerts');
Route::post('vagalerts', 'VagAlertsController@listVagAlertsWithFilters')->name('vagalerts.filters');
Route::delete('vagalerts', 'VagAlertsController@listVagAlertsRemoveFilters')->name('vagalerts.filters.remove');
Route::put('vagalerts', 'VagAlertsController@vagAlertStateUpdate')->name('vagalerts.state.update');

Route::get('nonfaces', 'NonfacesController@listNonfaces')->name('nonfaces');
Route::post('nonfaces', 'NonfacesController@listNonfacesWithFilters')->name('nonfaces.filters');
Route::delete('nonfaces', 'NonfacesController@listNonfacesRemoveFilters')->name('nonfaces.filters.remove');
Route::put('nonfaces', 'NonfacesController@nonfacesStateUpdate')->name('nonfaces.state.update');

Route::get('people', 'PeopleController@listPeople')->name('people');
Route::post('people', 'PeopleController@listPeopleWithFilters')->name('people.filters');
Route::delete('people', 'PeopleController@listPeopleRemoveFilters')->name('people.filters.remove');

Route::get('person/pictures/{person_id}','PeopleController@getPersonPictures')->name('person.pictures');
Route::delete('person', 'PeopleController@personDelete')->name('person.delete');
Route::put('person', 'PeopleController@personUpdate')->name('person.update');
Route::post('person', 'PeopleController@personStore')->name('person.store');
Route::post('person/picture/add', 'PeopleController@personPictureAdd')->name('person.picture.add');

Route::get('reports/{section?}', 'ReportsController@showReports')->name('reports');
Route::post('reports/charts', 'ReportsController@showChartsWithFilters')->name('reports.charts.filters');
Route::delete('reports/charts', 'ReportsController@showChartsRemoveFilters')->name('reports.charts.filters.remove');

Route::post('reports/alerts', 'ReportsController@showAlertsWithFilters')->name('reports.alerts.filters');
Route::delete('reports/alerts', 'ReportsController@showAlertsRemoveFilters')->name('reports.alerts.filters.remove');

Route::resource('channels', 'ChannelsController');
Route::get('channel/enable/{channel_id}', 'ChannelsController@enableChannel')->name('channel.enable');
Route::get('channel/facefilter/{channel_id}', 'ChannelsController@enableFaceFilter')->name('channel.facefilter');
Route::post('channel/check/ip', 'ChannelsController@checkIPValue')->name('channel.check.ip');
Route::post('channel/check/description', 'ChannelsController@checkDescription')->name('channel.check.description');

Route::get('alert/setting/{section?}','AlertsettingController@showAlertSetting')->name('alert.setting');
Route::post('alert/setting/email','AlertsettingController@emailStore')->name('alert.setting.email.store');
Route::put('alert/setting/email/{emailred}','AlertsettingController@emailUpdate')->name('alert.setting.email.update');
Route::delete('alert/setting/email/{emailrec}','AlertsettingController@emailDestroy')->name('alert.setting.email.destroy');

Route::put('alert/setting/telegram/bot/{bot}','AlertsettingController@telegramBotUpdate')->name('alert.setting.telegram.bot.update');
Route::put('alert/setting/telegram/user/trusted/{user}','AlertsettingController@telegramUserTrusted')->name('alert.setting.telegram.user.trusted');
Route::delete('alert/setting/telegram/user/{user}','AlertsettingController@telegramUserDestroy')->name('alert.setting.telegram.user.destroy');
Route::put('alert/setting/telegram/test/bot','AlertsettingController@testBot')->name('alert.setting.telegram.test.bot');

Route::resource('users', 'UsersController');
Route::put('user/level/update/{user_id}', 'UsersController@changeUserLevel')->name('users.level.update');
Route::post('user/check/username', 'UsersController@checkUsernameValue')->name('user.check.username');

Route::get('settings', 'SettingsController@index')->name('settings.index');
Route::put('settings/state/change', 'SettingsController@changeState')->name('settings.state.change');
Route::put('settings/alert/state/change', 'SettingsController@changeAlertState')->name('settings.alert.state.change');
Route::put('settings/cleanup/time', 'SettingsController@cleanupTime')->name('settings.cleanup.time');
Route::put('settings/value/numeric/update', 'SettingsController@numericValueUpdate')->name('settings.value.numeric.update');
Route::put('settings/field/info', 'SettingsController@fieldInfo')->name('settings.field.info');
Route::post('settings/channel/running_time/save', 'SettingsController@channelRunningTimeSave')->name('settings.channel.running_time.saves');
Route::post('settings/channel/floor_area/show', 'SettingsController@channelFloorAreaShow')->name('settings.channel.floor_area.show');
Route::post('settings/channel/floor_area/save', 'SettingsController@channelFloorAreaSave')->name('settings.channel.floor_area.save');

Route::resource('api', 'ApiController');
Route::get('api/enable/{api_id}', 'ApiController@enableApi')->name('api.enable');

Route::put('brightsign/value/field/update', 'BrightsignController@fieldValueUpdate')->name('brightsign.value.field.update');
Route::post('brightsign/device', 'BrightsignController@deviceUpdate')->name('brightsign.device.update');
Route::resource('brightsign', 'BrightsignController');
/*
Route::get('brightsign','BrightsignController@index')->name('brightsign.index');
Route::post('brightsign','BrightsignController@store')->name('brightsign.store');
Route::put('brightsign/{$id}','BrightsignController@update')->name('brightsign.update');
Route::delete('brightsign/{id}','BrightsignController@destroy')->name('brightsign.remove');
*/