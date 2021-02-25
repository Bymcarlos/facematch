@extends('layouts.intranet')
@section('css_custom')
<style type="text/css">
    .frame{
        width:{{$ch_img_screen_width}}px;
        height:{{$ch_img_screen_height}}px;
        margin:0px;
        padding:0px;
        border: 1px solid black;
        @if (!is_null($camera_url))
        background-image: url("{{$camera_url}}");
        @endif
        background-size: {{$ch_img_screen_width}}px {{$ch_img_screen_height}}px;
    }
</style>
@endsection

@section('content')
@if ($channel->id>0)
    @php($mnu_label="Edit channel")
    @php($action=route('channels.update',['id'=>$channel->id]))
@else
    @php($mnu_label="New channel")
    @php($action=route('channels.store'))
@endif
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
    <li class="breadcrumb-item"><a href="{{url('/channels')}}"><span class="nav-link-text">Channels</span></a></li>
    <li class="breadcrumb-item active">{{$mnu_label}}</li>
</ol>
<div class="container-fluid">
    <div class="row">
        <div class="col-6 bg-light p-4 rounded-sm">
            <form id="form_channel" method="POST" action="{{$action}}">
                {{ csrf_field() }}
                @if ($channel->id>0)
                <input type="hidden" id="_method" name="_method" value="PUT"/>
                @endif
                <input type="hidden" id="person_id"/>
                <div class="row form-groups mb-0 small">
                    <div class="col-4">
                        <label class="control-label" for="ch_type">Type:</label>
                    </div>
                    <div class="col-5">
                        <label class="control-label" for="ch_ip">IP (X.X.X.X):</label>
                    </div>
                    <div class="col-3">
                        <label class="control-label" for="ch_port">PORT:</label>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-4">
                        <select class="form-control" id="ch_type" name="ch_type" required onchange="checkChannelType()">
                            @if ($channel->id>0)
                                <option value="-1" disabled>Select</option>
                                <option value="0" @if ($isCameraIP) selected @endif>IP Camera</option>
                                @php ($st="")
                                @if ($isCameraIP and $cameraUSB_id>0) @php ($st="disabled")
                                    <option value="1" disabled>USB Camera</option>
                                @else
                                    <option value="1" @if (!$isCameraIP) selected @endif>USB Camera</option>
                                @endif
                            @else
                                <option value="-1" disabled selected>Select</option>
                                @foreach ($channelTypes as $key => $value)
                                    @php ($st="")
                                    @if ($key==1 and $cameraUSB_id>0) @php ($st="disabled") @endif
                                <option value="{{$key}}" {{$st}}>{{$value}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-5">
                        <input type="text" id="ch_ip" name="ch_ip" class="form-control" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$" value="{{$channel->ip}}" @if (!$isCameraIP) disabled @endif/>
                    </div>
                    <div class="col-3">
                        <input type="number" id="ch_port" name="ch_port" class="form-control" value="{{$channel->portNum}}" @if (!$isCameraIP) disabled @endif/>
                    </div>
                </div>
                <div class="row form-group mb-0 small">
                    <div class="col-6">
                        <label class="control-label" for="ch_username">Username:</label>
                    </div>
                    <div class="col-6">
                        <label class="control-label" for="ch_password">Password:</label>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-6">
                        <input type="text" id="ch_username" name="ch_username" class="form-control" placeholder="Username" value="{{$channel->username}}" @if (!$isCameraIP) disabled @endif/>
                    </div>
                    <div class="col-6">
                        <input type="text" id="ch_password" name="ch_password" class="form-control" placeholder="Password" value="{{$channel->password}}" @if (!$isCameraIP) disabled @endif/>
                    </div>
                </div>
                <div class="row form-group mb-0 small">
                    <div class="col-3">
                        <label class="control-label" for="ch_number">Number:</label>
                    </div>
                    <div class="col-4">
                        <label class="control-label" for="ch_model">Model:</label>
                    </div>
                    <div class="col-5">
                        <label class="control-label" for="ch_description">Description:</label>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3">
                        <input type="number" id="ch_number" name="ch_number" class="form-control small" value="{{$channel->cameraNum}}" @if (!$isCameraIP) disabled @endif/>
                    </div>
                    <div class="col-4">
                        <select class="form-control small" id="ch_model" name="ch_model" @if (!$isCameraIP) disabled @endif>
                            <option value="-1" disabled selected>Select</option>
                            @foreach ($cameras as $camera)
                            @php ($sel="")
                            @if ($channel->camera_id == $camera->id) @php ($sel="selected") @endif
                            <option value="{{$camera->id}}" {{$sel}}>{{$camera->camera_model}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-5">
                        <input type="text" id="ch_description" name="ch_description" class="form-control small" value="{{$channel->description}}" required/>
                    </div>
                </div>
                <div class="row form-group mb-0 small">
                    <div class="col-3">
                        <label class="control-label" for="ch_min_face">Minimun Face Size (30-80):</label>
                    </div>
                    <div class="col-3">
                        <label class="control-label" for="ch_max_face">Maximun Face Size (100-1000):</label>
                    </div>
                    <div class="col-3">
                        <label class="control-label" for="ch_match">Match Threshold (60-100)%:</label>
                    </div>
                    <div class="col-3">
                        <label class="control-label" for="ch_tracker">Tracker Quality (0-60):</label>
                    </div>
                </div>
                <div class="row form-group mt-1">
                    <div class="col-3">
                        <input type="number" id="ch_min_face" name="ch_min_face" class="form-control small" min="30" max="80" placeholder="{{$ch_def_min_face_size}}" value="{{$channel->minFaceSize}}" />
                    </div>
                    <div class="col-3">
                        <input type="number" id="ch_max_face" name="ch_max_face" class="form-control small" min="100" max="500" placeholder="{{$ch_def_max_face_size}}" value="{{$channel->maxFaceSize}}" />
                    </div>
                    <div class="col-3">
                        <input type="number" id="ch_match" name="ch_match" class="form-control small" min="60" max="100"  placeholder="{{$ch_def_match_threshold}}" value="{{$channel->matchPercentage}}" />
                    </div>
                    <div class="col-3">
                        <input type="number" id="ch_tracker" name="ch_tracker" class="form-control small" min="0" max="60"  placeholder="{{$ch_def_tracker_quality}}" value="{{$channel->trackerQuality}}" />
                    </div>
                </div>
                <div class="row form-group mb-0 small">
                    <div class="col-3">
                        <label class="control-label" for="ch_min_face">Enable Face Recog.:</label>
                    </div>
                    <div class="col-3">
                        <label class="control-label" for="ch_max_face">Enable Face Filter:</label>
                    </div>
                    <div class="col-3">
                        <label class="control-label" for="ch_match">Enable Nonface Recog.:</label>
                    </div>
                    <div class="col-3">
                        <label class="control-label" for="ch_tracker">Enable Vagrancy Recog.:</label>
                    </div>
                </div>
                <div class="row form-group mt-1 mb-0">
                    <div class="col-3">
                        <input type="checkbox" class="form-control small" id="ch_enable_fr" name="ch_enable_fr" {{$check_FR}}>
                    </div>
                    <div class="col-3">
                        <input type="checkbox" class="form-control small" id="ch_enable_ff" name="ch_enable_ff" {{$check_FF}}>
                    </div>
                    <div class="col-3">
                        <input type="checkbox" class="form-control small" id="ch_enable_nr" name="ch_enable_nr" {{$check_NR}}>
                    </div>
                    <div class="col-3">
                        <input type="checkbox" class="form-control small" id="ch_enable_vr" name="ch_enable_vr" {{$check_VR}}>
                    </div>
                </div>
                @if (!is_null($camera_url))
                <input type="hidden" name="ch_img_real_width" value="{{$ch_img_real_width}}">
                <input type="hidden" name="ch_img_real_height" value="{{$ch_img_real_height}}">
                <div class="row mt-1 small">
                    <div class="col-12">Area selection coordinates:</div>
                </div>
                <div class="row form-group mt-1">
                    <div class="col-12">
                        <select id="coordinates" name="coordinates" size="6" onchange="select_area(this.selectedIndex)" style="width: 250px;">
                            @if ($hasCoords)
                                @foreach($area_names as $index => $area_name)
                                    <option id="{{$index}}">{{$area_name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                @else
                <div class="row mt-1 mb-2">
                    <div class="col-12">No http URL available</div>
                </div>
                @endif
                <input type="hidden" id="list_coords" name="list_coords"/>
                <input type="hidden" id="list_areas" name="list_areas"/>
                <div class="form-group mt-1">
                    <button type="button" class="btn crud-submit btn-success" onclick="checkForm({{$channel->id}})">Submit</button>
                    <a href="{{route('channels.index')}}" class="btn btn-default btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
        <div class="col-6 text-left">
        @if (!is_null($camera_url))
          <p class="small">Press <strong>Left Click</strong> to draw a point. <strong>CTRL+Click</strong> or <strong>Right Click</strong> to close the polygon. You need at least three points.</p>
            <div id="YDR-Frame" class="frame">
              <canvas id="jPolygon" width="{{$ch_img_screen_width}}" height="{{$ch_img_screen_height}}" style="cursor:crosshair" data-imgsrc="{{asset('images/canvas.png')}}" onmousedown="point_it(event)" oncontextmenu="return false;">
                Your browser does not support the HTML5 canvas tag.
              </canvas>
            </div>
            <div class="mt-2 text-left">
                    <button type="button" class="btn btn-secondary" onclick="clear_coords()">Clear</button>
                    <button type="button" class="btn btn-success" onclick="refresh()">Refresh</button>
                    <button type="button" id="btnRemove" class="btn btn-danger" onclick="removeShape()" disabled="true">Remove</button>
            </div>
        @endif
        </div>
    </div>
</div>
@endsection

@section('js_custom')
<script type="text/javascript">
function checkChannelType(){
    var type = $('#form_channel #ch_type').val();
    if (type==0) { //IP Camera
        $('#form_channel #ch_ip').removeAttr('disabled');
        $('#form_channel #ch_ip').attr('required','true');

        $('#form_channel #ch_port').removeAttr('disabled');
        $('#form_channel #ch_port').attr('required','true');

        $('#form_channel #ch_username').removeAttr('disabled');
        $('#form_channel #ch_username').attr('required','true');

        $('#form_channel #ch_password').removeAttr('disabled');
        $('#form_channel #ch_password').attr('required','true');

        $('#form_channel #ch_number').removeAttr('disabled');
        $('#form_channel #ch_number').attr('required','true');

        $('#form_channel #ch_model').removeAttr('disabled');
        $('#form_channel #ch_model').attr('required','true');
    } else {
        //USB Camera
        $('#form_channel #ch_ip').attr('disabled','true');
        $('#form_channel #ch_ip').removeAttr('required');

        $('#form_channel #ch_port').attr('disabled','true');
        $('#form_channel #ch_port').removeAttr('required');

        $('#form_channel #ch_username').attr('disabled','true');
        $('#form_channel #ch_username').removeAttr('required');

        $('#form_channel #ch_password').attr('disabled','true');
        $('#form_channel #ch_password').removeAttr('required');

        $('#form_channel #ch_number').attr('disabled','true');
        $('#form_channel #ch_number').removeAttr('required');

        $('#form_channel #ch_model').val(-1);
        $('#form_channel #ch_model').attr('disabled','true');
        $('#form_channel #ch_model').removeAttr('required');
    }
}

function checkForm(ch_id) {
    /*
    ////////TEST to get the natural size of the camera image:
    getNaturalSize();
    return;
    */
    //Set list_coords and list_areas:
    $("#list_coords").val(list_coords.join("#"));
    $("#list_areas").val(list_areas.join(","));

    if (!$('#form_channel #ch_type').val()) {
        $('#form_channel #ch_type').focus();
        alert("Please select camera type.");
        return;
    }
    var ch_type = $('#form_channel #ch_type').val();
    if (ch_type==0) {
        //Check IP required values:
        if (!$('#form_channel #ch_ip').val()) {
            $('#form_channel #ch_ip').focus();
            alert("Please set IP value.");
            return;
        }
        //Check the IP address format:
        if (!/((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$/.test($('#form_channel #ch_ip').val())) {
            $('#form_channel #ch_ip').focus();
            alert("Please set a valid IP address.");
            return;
        }
        if (!$('#form_channel #ch_port').val()) {
            $('#form_channel #ch_port').focus();
            alert("Please set port value.");
            return;
        }
        if (!$('#form_channel #ch_username').val()) {
            $('#form_channel #ch_username').focus();
            alert("Please set username value.");
            return;
        }
        if (!$('#form_channel #ch_password').val()) {
            $('#form_channel #ch_password').focus();
            alert("Please set password value.");
            return;
        }
        if (!$('#form_channel #ch_number').val()) {
            $('#form_channel #ch_number').focus();
            alert("Please set number value.");
            return;
        }
        if (!$('#form_channel #ch_model').val()) {
            $('#form_channel #ch_model').focus();
            alert("Please select the model.");
            return;
        }
    }
    
    if (!$('#form_channel #ch_description').val()) {
        $('#form_channel #ch_description').focus();
        alert("Please set description value.");
        return;
    }
    checkFieldIP(ch_id);
}
function checkFieldIP(ch_id) {
    var ip_value = $('#form_channel #ch_ip').val();
    $.ajax({
        type: 'POST',
        url: "{{route('channel.check.ip')}}",
        data: { _token: "{{ csrf_token() }}", ip: ip_value, channel_id: ch_id},
        success: function(data){
            console.log("IP OK: "+data);
            if (data>0) {
                $('#form_channel #ch_ip').focus();
                alert("The IP value already exists on database. Please change and try again.");
                return;
            } else
                checkFieldDescription(ch_id);
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
            return 0;
        }
    });
}
function checkFieldDescription(ch_id) {
    var description_value = $('#form_channel #ch_description').val();
    $.ajax({
        type: 'POST',
        url: "{{route('channel.check.description')}}",
        data: { _token: "{{ csrf_token() }}", description: description_value, channel_id: ch_id},
        success: function(data){
            console.log("Desc ok: "+data);
            if (data>0) {
                $('#form_channel #ch_description').focus();
                alert("The description value already exists on database. Please change and try again.");
                return;
            } else
                $("#form_channel").submit();
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
            return 0;
        }
    });
}
function getNaturalSize() {
    $.ajax({
        type: 'GET',
        headers: {
                    'Access-Control-Allow-Origin': '*'
                },
        //url: "http://root:Mnbv1234%@70.28.14.215/axis-cgi/imagesize.cgi?camera=1",
        url: "http://70.28.14.215/axis-cgi/imagesize.cgi?camera=1",
        //dataType : 'jsonp',   //you may use jsonp for cross origin request
        //crossDomain:true,

        success: function(data){
            console.log(data);
            //alert(xhr.getResponseHeader('Location'));
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
            return 0;
        }
    });
}
</script>
<script type="text/javascript">
/*
   jPolygon - a ligthweigth javascript library to draw polygons over HTML5 canvas images.
   Project URL: http://www.matteomattei.com/projects/jpolygon
   Author: Matteo Mattei <matteo.mattei@gmail.com>
   Version: 1.0
   License: MIT License
*/
var list_areas = new Array();
var list_coords = new Array();
var perimeter = new Array();
var complete = false;
var canvas = document.getElementById("jPolygon");
var ctx;
var area_selected = -1;

function line_intersects(p0, p1, p2, p3) {
    var s1_x, s1_y, s2_x, s2_y;
    s1_x = p1['x'] - p0['x'];
    s1_y = p1['y'] - p0['y'];
    s2_x = p3['x'] - p2['x'];
    s2_y = p3['y'] - p2['y'];

    var s, t;
    s = (-s1_y * (p0['x'] - p2['x']) + s1_x * (p0['y'] - p2['y'])) / (-s2_x * s1_y + s1_x * s2_y);
    t = ( s2_x * (p0['y'] - p2['y']) - s2_y * (p0['x'] - p2['x'])) / (-s2_x * s1_y + s1_x * s2_y);

    if (s >= 0 && s <= 1 && t >= 0 && t <= 1)
    {
        // Collision detected
        return true;
    }
    return false; // No collision
}

function point(x, y){
    ctx.fillStyle="white";
    ctx.strokeStyle = "white";
    ctx.fillRect(x-2,y-2,4,4);
    ctx.moveTo(x,y);
}
/*
function undo(){
    $("#btnRemove").attr('disabled',true);
    ctx = undefined;
    perimeter.pop();
    complete = false;
    start(true);
}
*/


function new_canvas(list_coords=null,area_selected = -1) {
    console.log("new canvas (area:"+area_selected+")");
    ctx = undefined;
    perimeter = new Array();
    complete = false;
    var img = new Image();
    img.src = canvas.getAttribute('data-imgsrc');

    img.onload = function(){
        ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        console.log(ctx);
        if (list_coords != null) {
            list_coords.forEach(function(area_coords,index) {
                if (area_selected == index)
                    print_area(area_coords,true);
                else
                    print_area(area_coords);
            });
        }
    }
}

function print_area(area_coords,selected=false) {
    console.log("Coords:"+area_coords);
    perimeter = new Array();
    var points = area_coords.split(',');
    for (i=0;i<points.length;i+=2) {
        perimeter.push({'x':points[i],'y':points[i+1]});
    }
    draw(true,selected);
}


function draw(end,selected=false){
    //console.log("draw "+ end);
    ctx.lineWidth = 1;
    ctx.strokeStyle = "white";
    ctx.lineCap = "square";
    ctx.beginPath();

    for(var i=0; i<perimeter.length; i++){
        if(i==0){
            ctx.moveTo(perimeter[i]['x'],perimeter[i]['y']);
            end || point(perimeter[i]['x'],perimeter[i]['y']);
        } else {
            ctx.lineTo(perimeter[i]['x'],perimeter[i]['y']);
            end || point(perimeter[i]['x'],perimeter[i]['y']);
        }
    }
    if(end){
        ctx.lineTo(perimeter[0]['x'],perimeter[0]['y']);
        ctx.closePath();
        if (selected)
            ctx.fillStyle = 'rgba(0, 255, 0, 0.5)';
        else
            ctx.fillStyle = 'rgba(255, 0, 0, 0.5)';
        ctx.fill();
        if (selected)
            ctx.strokeStyle = 'yellow';
        else
            ctx.strokeStyle = 'blue';
        //complete = true;

        //TODO: Save coordinates: Cask for the area name and call a WS to save this coords-name:

        //Clear perimeter array for a new area selection:
        perimeter = new Array();
    }
    ctx.stroke();
}

function print_coordinates() {
    var coords="";
    var coordX,coordY;
    var list = [];
    perimeter.forEach(function(item){
        coordX = Math.round(item['x']); //parseFloat(item['x']).toFixed(2);
        list.push(coordX);
        coordY = Math.round(item['y']); //parseFloat(item['y']).toFixed(2);
        list.push(coordY);
    });
    coords = list.join(",");
    document.getElementById('coordinates').value = coords;
}

function check_intersect(x,y){
    if(perimeter.length < 4){
        return false;
    }
    var p0 = new Array();
    var p1 = new Array();
    var p2 = new Array();
    var p3 = new Array();

    p2['x'] = perimeter[perimeter.length-1]['x'];
    p2['y'] = perimeter[perimeter.length-1]['y'];
    p3['x'] = x;
    p3['y'] = y;

    for(var i=0; i<perimeter.length-1; i++){
        p0['x'] = perimeter[i]['x'];
        p0['y'] = perimeter[i]['y'];
        p1['x'] = perimeter[i+1]['x'];
        p1['y'] = perimeter[i+1]['y'];
        if(p1['x'] == p2['x'] && p1['y'] == p2['y']){ continue; }
        if(p0['x'] == p3['x'] && p0['y'] == p3['y']){ continue; }
        if(line_intersects(p0,p1,p2,p3)==true){
            return true;
        }
    }
    return false;
}

function point_it(event) {
    if(complete){
        alert('Polygon already created');
        return false;
    }
    var rect, x, y;

    if(event.ctrlKey || event.which === 3 || event.button === 2){
        if(perimeter.length<=1){
            alert('You need at least three points for a polygon');
            return false;
        }
        x = perimeter[0]['x'];
        y = perimeter[0]['y'];
        if(check_intersect(x,y)){
            alert('The line you are drowing intersect another line');
            return false;
        }

        add_area_coords();
        draw(true);
        //Polygon closed
        var area_name = prompt("Please enter area_name:", "Undefined");
        add_area_name(area_name);

        event.preventDefault();
        return false;
    } else {
        rect = canvas.getBoundingClientRect();
        x = event.clientX - rect.left;
        y = event.clientY - rect.top;
        if (perimeter.length>0 && x == perimeter[perimeter.length-1]['x'] && y == perimeter[perimeter.length-1]['y']){
            // same point - double click
            return false;
        }
        if(check_intersect(x,y)){
            alert('The line you are drowing intersect another line');
            return false;
        }
        perimeter.push({'x':x,'y':y});
        draw(false);
        return false;
    }
}

function add_area_coords() {
    var coords="";
    var coordX,coordY;
    var list = [];
    perimeter.forEach(function(item){
        coordX = Math.round(item['x']); //parseFloat(item['x']).toFixed(2);
        list.push(coordX);
        coordY = Math.round(item['y']); //parseFloat(item['y']).toFixed(2);
        list.push(coordY);
    });
    coords = list.join(",");
    //console.log(coords);
    list_coords.push(coords);
    //console.log(list_coords);
}
function add_area_name(area_name) {
    console.log("add_area_name");
    if (area_name == null) area_name = "Undefined";
    list_areas.push(area_name);
    $("#coordinates").empty();
    list_areas.forEach(function(item){
        console.log(item);
        $("#coordinates").append('<option>'+item+'</option');
    });
}

function start(with_draw, with_end = false) {
    //console.log("Draw: "+with_draw+ " End; "+with_end);
    var img = new Image();
    img.src = canvas.getAttribute('data-imgsrc');

    img.onload = function(){
        ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        //console.log(ctx);
    }
}

function clear_coords() {
    $("#btnRemove").attr('disabled',true);
    console.log("clear");
    list_areas = new Array();
    list_coords = new Array();
    $("#coordinates").empty();
    new_canvas();
}

function refresh() {
    area_selected = -1;
    new_canvas(list_coords);
    $("#coordinates").val(-1);
    $("#btnRemove").attr('disabled',true);
}

function select_area(index) {
    console.log("Selected: "+index);
    area_selected = index;
    new_canvas(list_coords,index);
    $("#btnRemove").attr('disabled',false);
}

function removeShape() {
    if (area_selected>=0) {
        console.log(list_coords[area_selected]);
        list_coords.splice(area_selected, 1);
        list_areas.splice(area_selected, 1);
        console.log(list_coords);
        $("#coordinates option:selected").remove();
        refresh();
    }
}

$( document ).ready(function() {
console.log( "ready!" );
$("#coordinates").val(-1);
@if ($hasCoords)
    list_areas = {!!json_encode($area_names)!!};
    list_coords ={!!json_encode($coords)!!};
    new_canvas(list_coords);
@else
    new_canvas();
@endif
});
</script>
@endsection