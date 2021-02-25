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
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
    <li class="breadcrumb-item"><a href="{{url('/settings')}}"><span class="nav-link-text">Settings</span></a></li>
    <li class="breadcrumb-item active">Set Floor Area</li>
</ol>

<div class="container-fluid horizontal-scrollable">
    <form id="form_area_selection" method="post" action="{{route('settings.channel.floor_area.save')}}">
      {{ csrf_field() }}
      <input type="hidden" name="channel_id" value="{{$channel->id}}"/>
      <input type="hidden" name="ch_img_real_width" value="{{$ch_img_real_width}}">
      <input type="hidden" name="ch_img_real_height" value="{{$ch_img_real_height}}">
      <input type="hidden" id="list_coords" name="list_coords"/>
      <input type="hidden" id="list_areas" name="list_areas"/>
      <div class="row">
          <div class="col-8 text-left">
              @if (!is_null($camera_url))
                <div class="row mt-2 mb-2">
                  <div class="small">Press <strong>Left Click</strong> to draw a point. <strong>CTRL+Click</strong> or <strong>Right Click</strong> to close the polygon.</div>
                </div>
                <div id="YDR-Frame" class="frame">
                  <canvas id="jPolygon" width="{{$ch_img_screen_width}}" height="{{$ch_img_screen_height}}" style="cursor:crosshair" data-imgsrc="{{asset('images/canvas.png')}}" onmousedown="point_it(event)" oncontextmenu="return false;">
                    Your browser does not support the HTML5 canvas tag.
                  </canvas>
                </div>
                <div class="row mt-2">
                  <div class="col-6 text-left">
                      <button type="button" class="btn btn-secondary" onclick="clear_coords()">Clear</button>
                      <button type="button" class="btn btn-success" onclick="refresh()">Refresh</button>
                      <button type="button" id="btnRemove" class="btn btn-danger" onclick="removeShape()" disabled="true">Remove</button>
                      <button type="button" onclick="saveCoords()" class="btn btn-primary">Save coordinates</button>
                      <a href="{{url('/settings')}}" class="btn btn-secondary">Cancel</a>
                  </div>
                  <div class="col-6 small">Press <strong>Save coordinates</strong> to save the coordinates on database.</div>
                </div>
          
              @else
                <p class="small">The url of the camera is not available</p><a href="{{url('/settings')}}" class="btn btn-secondary">Back</a>
              @endif
          </div>
          <div class="col-4 text-left">
            @if (!is_null($camera_url))
              <div>List areas:</div>
              <select id="coordinates" name="coordinates" size="10" onchange="select_area(this.selectedIndex)" style="width: 400px;">
                @if ($hasCoords)
                    @foreach($area_names as $index => $area_name)
                        <option id="{{$index}}">{{$area_name}}</option>
                    @endforeach
                @endif
              </select>
            @endif
          </div>
      </div>
    </form>
</div>
@endsection

@section('js_custom')
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

function saveCoords() {
    //Set list_coords and list_areas:
    $("#list_coords").val(list_coords.join("#"));
    $("#list_areas").val(list_areas.join(","));

    $('#form_area_selection').submit();
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