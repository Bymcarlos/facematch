@extends('layouts.intranet')
@section('css_custom')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
<style type="text/css">
.bgimg {
    display: inline-block;
    height: 150px;
    text-align: center;
    width: 130px;
    border-radius: 5px;
    background-repeat: no-repeat;
}
</style>
@endsection

@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">Alerts</li>
</ol>
<!-- Filters -->
<form id="filters" data-toggle="validator" action="{{ route('alerts.filters') }}" method="POST">
{{ csrf_field() }}
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="small border">
        <td>Start Date-Time:</td>
        <td>End Date-Time:</td>
        <td>Name:</td>
        <td>Person Type:</td>
        <td>Minimum age:</td>
        <td>Maximum age:</td>
        <td>Gender:</td>
        <td>Camera:</td>
        <td>Sort by:</td>
        <td></td>
        <td></td>
    </tr>
    <tr class="small border">
        <td><input type="text" class="form-control form-control-sm" style="width: 150px" id="startTime" name="startTime"></td>
        <td><input type="text" class="form-control form-control-sm" style="width: 150px" id="endTime" name="endTime"/></td>
        <td><input type="text" class="form-control form-control-sm" style="width: 90px" id="personName" name="personName" @isset ($params["personName"]) value="{{$params['personName']}}" @endisset/></td>
        <td><select class="form-control form-control-sm" id="personType" name="personType">
                @foreach ($personTypes as $id => $value)
                    @php ($sel="")
                    @if (isset($params["personType"]) and $params["personType"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td><select class="form-control form-control-sm" id="minAge" name="minAge">
                @foreach ($age_ranges as $id => $value)
                    @php ($sel="")
                    @if (isset($params["minAge"]) and $params["minAge"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td><select class="form-control form-control-sm" id="maxAge" name="maxAge">
                @foreach ($age_ranges as $id => $value)
                    @php ($sel="")
                    @if (isset($params["maxAge"]) and $params["maxAge"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td><select class="form-control form-control-sm" id="gender" name="gender">
                @foreach ($genders as $id => $value)
                    @php ($sel="")
                    @if (isset($params["gender"]) and $params["gender"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td><select class="form-control form-control-sm" id="channel" name="channel">
                @if (isset($params["channel"]))
                    <option value="-1">All</option>
                    @foreach ($channels as $channel)
                        @php ($sel="")
                        @if ($params["channel"] == $channel->id) @php ($sel="selected") @endif
                        <option value="{{ $channel->id }}" {{$sel}}>{{ $channel->description }}</option>
                    @endforeach
                @else
                    <option selected value="-1">All</option>
                    @foreach ($channels as $channel)
                        <option value="{{ $channel->id }}">{{ $channel->description }}</option>
                    @endforeach
                @endif
            </select></td>
        <td><select class="form-control form-control-sm" id="listOrder" name="listOrder">
                @foreach ($listOrders as $id => $value)
                    @php ($sel="")
                    @if (isset($params["listOrder"]) and $params["listOrder"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td><button type="button" class="btn crud-submit btn-success" onclick="checkAgeRange()">Apply</button></td>
        <td><button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button></td>
    </tr>
</table>
</form>
<form id="form_reset" action="{{ route('alerts.filters.remove') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
</form>
<!-- List Alerts -->
<div class="row">

@foreach ($items as $item)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <div class="tile" style="background-color:{{$personTypeColors[$item->person_type]}}">
            <div class="wrapper">
                <div class="header"><img id="icon_alert_{{$item->IDENT_ID}}" src="{{ asset('images/')}}/{{$alertIcons[$item->confirmed]}}" width="20"/>
                    <span class="small mr-2">{{date('m/d/Y H:i:s', strtotime($item->captureTime))}}</span></div>
                <div class="dates">
                    <div class="start">
                        W: {{$item->width}} - {{number_format($item->confidence,0)}}%<strong>LIVE:</strong>
                    </div>
                    <div class="ends">
                        W: {{$knownfaces[$item->known_face_id]->height}} - Q: {{number_format($knownfaces[$item->known_face_id]->quality,0)}}<strong>ARCHIVED:</strong>
                    </div>
                </div>
                <div>
                    <div id="item_{{$item->IDENT_ID}}" class="bgimg" style="cursor: pointer; background-image: url('{{ asset('Media/Frames/')}}/{{$item->img_name}}');
                          background-size: {{$item->img_width}}px {{$item->img_height}}px; resize: both; background-position: {{$item->img_posX}}% {{$item->img_posY}}%" data-toggle="modal" data-target="#modal-picture" data-img_available="{{$item->img_available}}" data-imgurl="{{ asset('Media/Frames/')}}/{{$item->img_name}}" data-topx="{{$item->topLeftX}}" data-topy="{{$item->topLeftY}}" data-areaw="{{$item->width}}" data-areah="{{$item->height}}" data-camera="{{$item->CH_DESC}}" data-time="{{date('m/d/Y H:i:s', strtotime($item->captureTime))}}" data-person_id="{{$item->PERSON_ID}}" data-person_name="{{$item->PERSON_DESC}}" data-person_type="{{$personTypes[$item->person_type]}}" data-person_color="{{$personTypeColors[$item->person_type]}}" data-person_info="{{$item->info}}" data-match="{{number_format($item->confidence,0)}}" data-img_archived_url="{{ asset('Media/KnownFaces/')}}/{{$knownfaces[$item->known_face_id]->img_name}}" data-person_type_id="{{$item->person_type}}" onclick="showImage({{$item->IDENT_ID}},'FaceMatch')"></div>
                      <div class="bgimg" style="cursor: pointer; background-image: url('{{ asset('Media/KnownFaces/')}}/{{$knownfaces[$item->known_face_id]->img_name }}');
                          background-size: cover;" data-toggle="modal" data-target="#modal-picture" onclick="showImage({{$item->IDENT_ID}},'FaceMatch')"></div>
                </div>
                <div class="stats">
                    <div>
                        <strong>ALERT#</strong>{{$item->IDENT_ID}}
                    </div>
                    <div>
                        <strong>TYPE</strong>{{$personTypes[$item->person_type]}}
                    </div>
                    <div>
                        <strong>NAME</strong>{{$item->PERSON_DESC}}
                    </div>
                </div>

                <div class="stats">
                    <div>
                        <strong>FACE#</strong>{{$item->FACE_ID}}
                    </div>
                    <div>
                        <strong>CHANNEL</strong>{{$item->CH_DESC}}
                    </div>
                    <div>
                        <strong></strong>
                    </div>
                </div>
                <div class="footer">
                    <a href="#" class="btn btn-success" data-toggle="modal" data-target="#modal_alert_state" onclick="alertStateDialog({{$item->IDENT_ID}},1)">Confirm</a>
                    <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#modal_alert_state" onclick="alertStateDialog({{$item->IDENT_ID}},-1)">Reject</a>
                </div>
            </div>
        </div> 
    </div>
@endforeach
</div>
{{ $items->links('private_area.pagination')}}
<!-- Modal zoom picture -->
<div class="modal fade" id="modal-picture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" on>
    <div class="modal-dialog modal-lg" role="document">
        <div id="zoom_content" class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-9">
                        <div class="form-group wrapper">
                            <img class="modal-content" id="img-zoom">
                            <!-- Next div is for drawing the select area over the picture. Necessary ml-3 to adjust the 0,0 coordinate with the picture -->
                            <div class="ml-3" id="pic_area"></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card small">
                          <ul id="picFeatures" class="list-group list-group-flush p-0">
                            <li class="list-group-item p-1 text-light bg-secondary small">LIVE VIDEO CAPTURED</li>
                            <li class="list-group-item p-1">Camera: <span id="feat_camera"></span></li>
                            <li class="list-group-item p-1">Date: <span id="feat_time"></span></li>
                          </ul>
                        </div>
                        <div class="card small mt-2">
                          <ul id="picFeatures" class="list-group list-group-flush p-0">
                            <li class="list-group-item p-1 text-light bg-secondary small">ARCHIVED</li>
                            <li class="list-group-item p-1">ID#: <span id="feat_id"></span></li>
                            <li class="list-group-item p-1">Name: <span id="feat_name"></span></li>
                            <li class="list-group-item p-1">Match: <span id="feat_match"></span>%</li>
                            <li class="list-group-item p-1">Type: <span id="feat_persontype"></span></li>
                            <li class="list-group-item p-1 mx-auto"><div id="img_archived" class="bgimg"></div></li>
                          <li class="list-group-item p-1"><span id="feat_info"></span></li>
                          </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Alert actions modal -->
<div class="modal fade" id="modal_alert_state" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title"></h5>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="alert_id"/>
                    <input type="hidden" id="alert_action"/>
                    <div class="form-group">
                        <button type="button" class="btn crud-submit btn-success" data-dismiss="modal" onclick="alertStateChange()">Yes</button>
                        <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_custom')
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script>
$(function () {
    $('#startTime').datetimepicker();
    @isset($params['startTime'])
        $('#startTime').data("DateTimePicker").defaultDate("{{$params['startTime']}}");
    @endisset
    $('#endTime').datetimepicker();
    @isset($params['endTime'])
        $('#endTime').data("DateTimePicker").defaultDate("{{$params['endTime']}}");
    @endisset
});

function resetFilters() {
  $('#form_reset').submit();
}

function checkAgeRange() {
    var min_range = $("#filters #minAge").val();
    var max_range = $("#filters #maxAge").val();
    console.log(min_range + " " + max_range);
    if (max_range<0 || min_range<0 || (min_range>-1 && min_range <= max_range))
        $("#filters").submit();
    else
        alert("Minimum age must be younger than Maximum age.");
}

function alertStateDialog(id_item,action) {
    //action 1->Confirm, -1->Reject
    switch (action) {
        case -1:
            $('#modal_alert_state #title').text("Rejecting this alert will override all Telegram responses. Do you wish to continue?");
            break;
        case 1:
            $('#modal_alert_state #title').text("Confirming this alert will override all Telegram responses. Do you wish to continue?");
            break;
    }
    $('#modal_alert_state #alert_id').val(id_item);
    $('#modal_alert_state #alert_action').val(action);
}

function alertStateChange() {
    var alertID = $('#modal_alert_state #alert_id').val();
    $.ajax({
        type: 'PUT',
        url: "{{route('alerts.state.update')}}",
        data: { _token: "{{ csrf_token() }}", alert_id: alertID, alert_action: $('#modal_alert_state #alert_action').val() },
        success: function(data){
            console.log("OK: "+data);
            switch (parseInt(data)) {
                case 10:
                    console.log("10");
                    $('#icon_alert_'+alertID).attr('src',"{{ asset('images/cross.png')}}");
                    break;
                case 20:
                    console.log("20");
                    $('#icon_alert_'+alertID).attr('src',"{{ asset('images/checkmark.png')}}");
                    break;
                default:
                    $('#icon_alert_'+alertID).attr('src',"{{ asset('images/question.png')}}");
                    break;
            }
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
        }
    });
}

function showImage(id_item,file_title) {
    var img_src = $("#item_"+id_item).data("imgurl");
    var img_available = $("#item_"+id_item).data("img_available");
    //console.log(img_available);
    if (img_available>0)
        $("#modal-picture #img-zoom").attr("src",img_src);
    else
        $("#modal-picture #img-zoom").attr("src","{{asset('images/not-available.jpg')}}");

    $("#modal-picture #img-zoom").data("item_id",id_item);
    $("#modal-picture #img-zoom").data("person_type_id",$("#item_"+id_item).data("person_type_id"));
    $("#modal-picture #img-zoom").data("img_available",img_available);
    $("#modal-picture #modal_title").text(file_title);
    //Remove las selected area (if exist)
    $("#pic_area").removeAttr('style');
    //Load features:
    $("#modal-picture #feat_camera").text($("#item_"+id_item).data("camera"));
    $("#modal-picture #feat_match").text($("#item_"+id_item).data("match"));
    $("#modal-picture #feat_time").text($("#item_"+id_item).data("time"));
    $("#modal-picture #feat_id").text($("#item_"+id_item).data("person_id"));
    $("#modal-picture #feat_name").text($("#item_"+id_item).data("person_name"));
    $("#modal-picture #feat_info").text($("#item_"+id_item).data("person_info"));
    $("#modal-picture #feat_persontype").text($("#item_"+id_item).data("person_type"));
    //Archived image:
    var img_archived_src = $("#item_"+id_item).data("img_archived_url");
    var backX = 100;
    var backY = 100;
    $("#modal-picture #img_archived").attr('style',"background-image: url('"+img_archived_src+"'); background-size: cover; background-position: "+backX+"% "+backY+"%;");
    $("#modal-picture #zoom_content").attr('style',"background-color: "+$("#item_"+id_item).data("person_color"));
}

$("#modal-picture").on('shown.bs.modal', function(){
    //Get Item ID:
    var id_item = $("#modal-picture #img-zoom").data("item_id");
    var person_type = $("#modal-picture #img-zoom").data("person_type_id");
    var img_available = $("#modal-picture #img-zoom").data("img_available");
    //console.log("Item ID:"+ id_item);

    if (img_available>0) {

        var img = document.getElementById('img-zoom');
        var current_width = img.clientWidth;
        var natural_width = img.naturalWidth;
        var ratioW = current_width / natural_width;

        var current_height = img.clientHeight;
        var natural_height = img.naturalHeight;
        var ratioH = current_height / natural_height;

        //console.log("Cur wid:"+current_width+" Nat wid:"+natural_width+" ratioW:"+ratioW);
        //console.log("Cur hei:"+current_height+" Nat hei:"+natural_height+" ratioH:"+ratioH);
        
        //Get DB values for x,y,w,h:
        var topX = $("#item_"+id_item).data("topx");
        var topY = $("#item_"+id_item).data("topy");
        var areaw = $("#item_"+id_item).data("areaw");
        var areah = $("#item_"+id_item).data("areah");
        //console.log("DB topX:"+topX+" topY:"+topY+" areaw:"+areaw+" areah:"+areah);
        
        var topX = topX*ratioW;
        var topY = topY*ratioH;
        var areaw = areaw*ratioW;
        var areah = areah*ratioH;
        //console.log("topX:"+topX+" topY:"+topY+" areaw:"+areaw+" areah:"+areah);

        var area_color = "blue";
        if (person_type==0) area_color="green";
        if (person_type==1) area_color="red";
        if (person_type==2) area_color="yellow";
        $("#pic_area").attr("style","position:absolute;top:"+topY+"px;left:"+topX+"px;width:"+areaw+"px;height:"+areah+"px;border:3px solid "+area_color+";background-color:transparent");
    }
});
</script>
@endsection