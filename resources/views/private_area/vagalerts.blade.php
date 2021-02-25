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
<div class="row">
    <div class="col-10">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
            <li class="breadcrumb-item active">Vagrancy Alerts</li>
        </ol>
    </div>
    <div class="col-2 text-right"><a href="{{ asset('Media/Manual/VagrancyManual.pdf')}}" class="btn btn-sm btn-info"><i class="fas fa-fw fa-info"></i></a></div>
</div>
<!-- Filters -->
<form id="form_filters" data-toggle="validator" action="{{ route('vagalerts.filters') }}" method="POST">
{{ csrf_field() }}
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="small border">
        <td class="text-left">Start Date-Time:</td>
        <td class="text-left">End Date-Time:</td>
        <td class="text-left">Vagrancy Type:</td>
        <td>Top Color:</td>
        <td>Bottom Color:</td>
        <td>Camera:</td>
        <td>Sort by:</td>
        <td></td>
        <td></td>
    </tr>
    <tr id="filters" class="small border">
        <td><input type="text" class="form-control form-control-sm" style="width: 150px" id="startTime" name="startTime"></td>
        <td><input type="text" class="form-control form-control-sm" style="width: 150px" id="endTime" name="endTime"/></td>
        <td><select class="form-control form-control-sm" id="vagType" name="vagType">
                @foreach ($vagTypes as $id => $value)
                    @php ($sel="")
                    @if (isset($params["vagType"]) and $params["vagType"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td><select class="form-control form-control-sm" id="topColor" name="topColor">
                @foreach ($bodyColors as $id => $value)
                    @php ($sel="")
                    @if (isset($params["topColor"]) and $params["topColor"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td><select class="form-control form-control-sm" id="bottomColor" name="bottomColor">
                @foreach ($bodyColors as $id => $value)
                    @php ($sel="")
                    @if (isset($params["bottomColor"]) and $params["bottomColor"] == $id) @php ($sel="selected") @endif
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
        <td><button type="button" class="btn crud-submit btn-success" onclick="checkForm()">Apply</button></td>
        <td><button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button></td>
    </tr>
</table>
</form>
<form id="form_reset" action="{{ route('vagalerts.filters.remove') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
</form>
<!-- List Alerts -->
<div class="row">

@foreach ($items as $item)
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <div class="tile" style="background-color:#fff4e8">
            <div class="wrapper">
                <div class="header"><img id="icon_alert_{{$item->id}}" src="{{ asset('images/')}}/{{$alertIcons[$item->confirmed]}}" width="20"/>
                    <span class="small mr-2">{{date('m/d/Y H:i:s', strtotime($item->captureTime))}}</span></div>
                <div class="dates">
                    <div class="start">
                        W: {{$item->width}} - QB: {{number_format($item->probability,0)}}%<strong>LIVE:</strong>
                    </div>
                    <div class="ends">
                        W: {{$bodies[$item->firstBody_id]->width}} - QB: {{number_format($bodies[$item->firstBody_id]->probability,0)}}%<strong>FIRST BODY:</strong>
                    </div>
                </div>
                <div>
                    <div id="item_{{$item->id}}" class="bgimg" style="cursor: pointer; background-image: url('{{ asset('Media/Frames/')}}/{{$item->img_name}}');
                          background-size: cover; resize: both; background-position: {{$item->img_live_posX}}% {{$item->img_live_posY}}%" data-toggle="modal" data-target="#modal-picture" data-img_live_available="{{$item->img_live_available}}" data-img_live_url="{{ asset('Media/Frames/')}}/{{$item->img_name}}" data-live_topx="{{$item->topLeftX}}" data-live_topy="{{$item->topLeftY}}" data-live_topcolor="{{$item->topColor}}" data-live_bottomcolor="{{$item->bottomColor}}" data-live_qb="{{$item->probability}}" data-live_channel="{{$item->CH_DESC}}" data-live_time="{{date('m/d/Y H:i:s', strtotime($item->captureTime))}}" data-live_width="{{$item->width}}" data-live_height="{{$item->height}}" data-img_capt_available="{{$item->img_capt_available}}" data-img_capt_url="{{ asset('Media/Frames/')}}/{{$frames[$bodies[$item->firstBody_id]->frame_id]->img_name}}" data-capt_topx="{{$bodies[$item->firstBody_id]->topLeftX}}" data-capt_topy="{{$bodies[$item->firstBody_id]->topLeftY}}" data-capt_topcolor="{{$bodies[$item->firstBody_id]->topColor}}" data-capt_bottomcolor="{{$bodies[$item->firstBody_id]->bottomColor}}" data-capt_qb="{{$bodies[$item->firstBody_id]->probability}}" data-capt_channel="{{$item->CH_CAPT}}" data-capt_time="{{date('m/d/Y H:i:s', strtotime($item->captureTime))}}" data-capt_width="{{$bodies[$item->firstBody_id]->width}}" data-capt_height="{{$bodies[$item->firstBody_id]->height}}" data-capt_posX="{{$item->img_capt_posX}}" data-capt_posY="{{$item->img_capt_posY}}" onclick="showImage({{$item->id}},'FaceMatch')"></div>
                      <div class="bgimg" style="cursor: pointer; background-image: url('{{ asset('Media/Frames/')}}/{{$frames[$bodies[$item->firstBody_id]->frame_id]->img_name}}');
                          background-size: cover; resize: both; background-position: {{$item->img_capt_posX}}% {{$item->img_capt_posY}}%" data-toggle="modal" data-target="#modal-picture" onclick="showImage({{$item->id}},'FaceMatch')"></div>
                </div>
                <div class="stats">
                    <div>
                        <strong>VAG ALERT#</strong>{{$item->id}}
                    </div>
                    <div>
                        <strong>BODY#</strong>{{$item->firstBody_id}}
                    </div>
                    <div>
                        <strong>CHANNEL</strong>{{$item->CH_DESC}}
                    </div>
                </div>
                <div class="footer">
                    <a href="#" class="btn btn-success" data-toggle="modal" data-target="#modal_alert_state" onclick="alertStateDialog({{$item->id}},1)">Confirm</a>
                    <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#modal_alert_state" onclick="alertStateDialog({{$item->id}},-1)">Reject</a>
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
                    <div class="col-6">
                        <div class="form-group wrapper">
                            <div class="list-group-item p-1 text-light bg-secondary small">CAPTURED VAGRANCE BODY FROM LIVE VIDEO</div>
                            <img class="modal-content" id="img-live-zoom">
                            <!-- Next div is for drawing the select area over the picture. Necessary ml-3 to adjust the 0,0 coordinate with the picture -->
                            <div class="ml-3" id="pic_live_top_area"></div><div class="ml-3" id="pic_live_bottom_area"></div>
                            <div class="row">
                                <div class="col-6 p-1">
                                    <div class="card small mt-2">
                                      <ul id="picFeatures" class="list-group list-group-flush p-0">
                                        <li id="zoom_live_topcolor" class="list-group-item p-1">Top Color: <span id="feat_live_topcolor"></span></li>
                                        <li class="list-group-item p-1">QB (Accuracy): <span id="feat_live_qb"></span>%</li>
                                        <li class="list-group-item p-1">Camera: <span id="feat_live_channel"></span></li>
                                      </ul>
                                    </div>
                                </div>
                                <div class="col-6 p-1">
                                    <div class="card small mt-2">
                                      <ul id="picFeatures" class="list-group list-group-flush p-0">
                                        <li id="zoom_live_bottomcolor" class="list-group-item p-1">Bottom Color: <span id="feat_live_bottomcolor"></span></li>
                                        <li class="list-group-item p-1">Body Size (W x H): <span id="feat_live_size"></span></li>
                                        <li class="list-group-item p-1">Date: <span id="feat_live_date"></span></li>
                                      </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group wrapper">
                            <div class="list-group-item p-1 text-light bg-secondary small">CAPTURED FIRST BODY</div>
                            <img class="modal-content" id="img-capt-zoom">
                            <!-- Next div is for drawing the select area over the picture. Necessary ml-3 to adjust the 0,0 coordinate with the picture -->
                            <div class="ml-3" id="pic_capt_top_area"></div><div class="ml-3" id="pic_capt_bottom_area"></div>
                            <div class="row">
                                <div class="col-6 p-1">
                                    <div class="card small mt-2">
                                      <ul id="picFeatures" class="list-group list-group-flush p-0">
                                        <li id="zoom_capt_topcolor" class="list-group-item p-1">Top Color: <span id="feat_capt_topcolor"></span></li>
                                        <li class="list-group-item p-1">QB (Accuracy): <span id="feat_capt_qb"></span>%</li>
                                        <li class="list-group-item p-1">Camera: <span id="feat_capt_channel"></span></li>
                                      </ul>
                                    </div>
                                </div>
                                <div class="col-6 p-1">
                                    <div class="card small mt-2">
                                      <ul id="picFeatures" class="list-group list-group-flush p-0">
                                        <li id="zoom_capt_bottomcolor" class="list-group-item p-1">Bottom Color: <span id="feat_capt_bottomcolor"></span></li>
                                        <li class="list-group-item p-1">Body Size (W x H): <span id="feat_capt_size"></span></li>
                                        <li class="list-group-item p-1">Date: <span id="feat_capt_date"></span></li>
                                      </ul>
                                    </div>
                                </div>
                            </div>
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

function checkForm() {
    $("#form_filters").submit();
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
        url: "{{route('vagalerts.state.update')}}",
        data: { _token: "{{ csrf_token() }}", alert_id: alertID, alert_action: $('#modal_alert_state #alert_action').val() },
        success: function(data){
            //console.log("OK: "+data);
            switch (parseInt(data)) {
                case 10:
                    //console.log("10");
                    $('#icon_alert_'+alertID).attr('src',"{{ asset('images/cross.png')}}");
                    break;
                case 20:
                    //console.log("20");
                    $('#icon_alert_'+alertID).attr('src',"{{ asset('images/checkmark.png')}}");
                    break;
                default:
                    $('#icon_alert_'+alertID).attr('src',"{{ asset('images/question.png')}}");
                    break;
            }
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            //console.log("error:"+error.Message);
        }
    });
}

function showImage(id_item,file_title) {
    $("#modal-picture #modal_title").text(file_title);

    var img_live_available = $("#item_"+id_item).data("img_live_available");
    $("#modal-picture #img-live-zoom").data("img_live_available",img_live_available);
    if (img_live_available>0) {
        var img_live_src = $("#item_"+id_item).data("img_live_url");
        $("#modal-picture #img-live-zoom").attr("src",img_live_src);
    } else {
        $("#modal-picture #img-live-zoom").attr("src","{{asset('images/not-available.jpg')}}");
    }

    var liveTopColor = $("#item_"+id_item).data("live_topcolor");
    var liveBottomColor = $("#item_"+id_item).data("live_bottomcolor");
    var captTopColor = $("#item_"+id_item).data("capt_topcolor");
    var captBottomColor = $("#item_"+id_item).data("capt_bottomcolor");

    $("#modal-picture #img-live-zoom").data("item_id",id_item);
    $("#modal-picture #img-live-zoom").data("live_topcolor",liveTopColor);
    $("#modal-picture #img-live-zoom").data("live_bottomcolor",liveBottomColor);
    $("#modal-picture #img-capt-zoom").data("capt_topcolor",captTopColor);
    $("#modal-picture #img-capt-zoom").data("capt_bottomcolor",captBottomColor);
    

    //Remove last selected area (if exist)
    $("#pic_live_top_area").removeAttr('style');
    $("#pic_live_bottom_area").removeAttr('style');
    $("#pic_capt_top_area").removeAttr('style');
    $("#pic_capt_bottom_area").removeAttr('style');
    //Load features:
    $("#modal-picture #feat_live_topcolor").text(liveTopColor);
    $("#modal-picture #feat_live_qb").text($("#item_"+id_item).data("live_qb"));
    $("#modal-picture #feat_live_channel").text($("#item_"+id_item).data("live_channel"));
    $("#modal-picture #feat_live_bottomcolor").text(liveBottomColor);
    $("#modal-picture #feat_live_size").text($("#item_"+id_item).data("live_width")+" x "+$("#item_"+id_item).data("live_height"));
    $("#modal-picture #feat_live_date").text($("#item_"+id_item).data("live_time"));

    $("#modal-picture #feat_capt_topcolor").text(captTopColor);
    $("#modal-picture #feat_capt_qb").text($("#item_"+id_item).data("capt_qb"));
    $("#modal-picture #feat_capt_channel").text($("#item_"+id_item).data("capt_channel"));
    $("#modal-picture #feat_capt_bottomcolor").text(captBottomColor);
    $("#modal-picture #feat_capt_size").text($("#item_"+id_item).data("capt_width")+" x "+$("#item_"+id_item).data("capt_height"));
    $("#modal-picture #feat_capt_date").text($("#item_"+id_item).data("capt_time"));

    //Captured image:
    var img_capt_available = $("#item_"+id_item).data("img_capt_available");
    $("#modal-picture #img-capt-zoom").data("img_capt_available",img_capt_available);
    if (img_capt_available>0) {
        var img_capt_src = $("#item_"+id_item).data("img_capt_url");
        $("#modal-picture #img-capt-zoom").attr("src",img_capt_src);
    } else {
        $("#modal-picture #img-capt-zoom").attr("src","{{asset('images/not-available.jpg')}}");
    }
}

$("#modal-picture").on('shown.bs.modal', function(){
    
    //Get Item ID:
    var id_item = $("#modal-picture #img-live-zoom").data("item_id");
    var live_top_color = $("#modal-picture #img-live-zoom").data("live_topcolor");
    $("#zoom_live_topcolor").attr("style","background-color: Silver; color:"+live_top_color);
    var live_bottom_color = $("#modal-picture #img-live-zoom").data("live_bottomcolor");
    $("#zoom_live_bottomcolor").attr("style","background-color: Silver; color:"+live_bottom_color);
    var live_img_available = $("#modal-picture #img-live-zoom").data("img_live_available");
    //console.log("Item ID:"+ id_item);

    if (live_img_available>0) {
        var img = document.getElementById('img-live-zoom');
        var current_width = img.clientWidth;
        var natural_width = img.naturalWidth;
        var ratioW = current_width / natural_width;

        var current_height = img.clientHeight;
        var natural_height = img.naturalHeight;
        var ratioH = current_height / natural_height;

        console.log("Cur wid:"+current_width+" Nat wid:"+natural_width+" ratioW:"+ratioW);
        console.log("Cur hei:"+current_height+" Nat hei:"+natural_height+" ratioH:"+ratioH);
        
        //Get DB values for x,y,w,h:
        var posX = $("#item_"+id_item).data("live_topx");
        var posY = $("#item_"+id_item).data("live_topy");
        var areaw = $("#item_"+id_item).data("live_width");
        var areah = $("#item_"+id_item).data("live_height");
        //console.log("DB posX:"+posX+" posY:"+posY+" areaw:"+areaw+" areah:"+areah);
        
        var posX = posX*ratioW;
        var posY = posY*ratioH;
        var areaw = areaw*ratioW;
        var areah = areah*ratioH;
        //Translate of Sitti c++ code to draw top and bottom area:
        var height_unit = ((areah+posY)-posY)/14;
        //Draw top:
        var posTopX = posX;
        var posTopY = posY +(height_unit*2);
        var areawTop = areaw;
        var areahTop = height_unit*6;
        //Bottom area:
        var posBottomX = posX;
        var areawBottom = areaw;
        var posBottomY = posY +(height_unit*8);
        var areahBottom = posY + (height_unit*2);
        //Check to avoid the bottom area exceding the picture area:
        if ((posBottomY + areahBottom)>current_height)
            areahBottom = current_height - posBottomY;
        
        //Check to avoid the right areas exceding the picture area:
        if ((posX+areaw)>current_width) {
            areawTop = current_width - posX;
            areawBottom = current_width - posX;
        }

        //Check top and left:
        if (posTopX<0) posTopX=0;
        if (posTopY<0) posTopY=0;        
        if (posBottomX<0) posBottomX=0;
        if (posBottomY<0) posBottomY=0;


        $("#pic_live_top_area").attr("style","position:absolute;top:"+posTopY+"px;left:"+posTopX+"px;width:"+areawTop+"px;height:"+areahTop+"px;border:2px solid "+live_top_color+";background-color:transparent");
        $("#pic_live_bottom_area").attr("style","position:absolute;top:"+posBottomY+"px;left:"+posBottomX+"px;width:"+areawBottom+"px;height:"+areahBottom+"px;border:2px solid "+live_bottom_color+";background-color:transparent");
        
    }
    var capt_top_color = $("#modal-picture #img-capt-zoom").data("capt_topcolor");
    $("#zoom_capt_topcolor").attr("style","background-color: Silver; color:"+capt_top_color);
    var capt_bottom_color = $("#modal-picture #img-capt-zoom").data("capt_bottomcolor");
    $("#zoom_capt_bottomcolor").attr("style","background-color: Silver; color:"+capt_bottom_color);
    var capt_img_available = $("#modal-picture #img-capt-zoom").data("img_capt_available");
    console.log("capt_img_avail: "+capt_img_available);
    if (capt_img_available>0) {
        var img = document.getElementById('img-capt-zoom');
        var current_width = img.clientWidth;
        var natural_width = img.naturalWidth;
        var ratioW = current_width / natural_width;

        var current_height = img.clientHeight;
        var natural_height = img.naturalHeight;
        var ratioH = current_height / natural_height;

        console.log("Cur wid:"+current_width+" Nat wid:"+natural_width+" ratioW:"+ratioW);
        console.log("Cur hei:"+current_height+" Nat hei:"+natural_height+" ratioH:"+ratioH);
        
        //Get DB values for x,y,w,h:
        var posX = $("#item_"+id_item).data("capt_topx");
        var posY = $("#item_"+id_item).data("capt_topy");
        var areaw = $("#item_"+id_item).data("capt_width");
        var areah = $("#item_"+id_item).data("capt_height");
        //console.log("DB posX:"+posX+" posY:"+posY+" areaw:"+areaw+" areah:"+areah);
        
        var posX = posX*ratioW;
        var posY = posY*ratioH;
        var areaw = areaw*ratioW;
        var areah = areah*ratioH;
        //Translate of Sitti c++ code to draw top and bottom area:
        var height_unit = ((areah+posY)-posY)/14;
        //Draw top:
        var posTopX = posX;
        var posTopY = posY +(height_unit*2);
        var areawTop = areaw;
        var areahTop = height_unit*6;
        //Bottom area:
        var posBottomX = posX;
        var areawBottom = areaw;
        var posBottomY = posY +(height_unit*8);
        var areahBottom = posY + (height_unit*2);
        //Check to avoid the bottom area exceding the picture area:
        if ((posBottomY + areahBottom)>current_height)
            areahBottom = current_height - posBottomY;
        
        //Check to avoid the right areas exceding the picture area:
        if ((posX+areaw)>current_width) {
            areawTop = current_width - posX;
            areawBottom = current_width - posX;
        }

        //Check top and left:
        if (posTopX<0) posTopX=0;
        if (posTopY<0) posTopY=0;        
        if (posBottomX<0) posBottomX=0;
        if (posBottomY<0) posBottomY=0;

        $("#pic_capt_top_area").attr("style","position:absolute;top:"+posTopY+"px;left:"+posTopX+"px;width:"+areawTop+"px;height:"+areahTop+"px;border:2px solid "+capt_top_color+";background-color:transparent");
        $("#pic_capt_bottom_area").attr("style","position:absolute;top:"+posBottomY+"px;left:"+posBottomX+"px;width:"+areawBottom+"px;height:"+areahBottom+"px;border:2px solid "+capt_bottom_color+";background-color:transparent");
        
    }
});
</script>
@endsection