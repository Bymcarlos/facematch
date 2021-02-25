@extends('layouts.intranet')
@section('css_custom')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
<style type="text/css">
.bgimg {
    height: 150px;
    width: 100px;
    background-repeat: no-repeat;
}
</style>
@endsection

@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">Bodies</li>
</ol>
<!-- Filters -->
<form id="form_filters" data-toggle="validator" action="{{ route('bodies.filters') }}" method="POST">
{{ csrf_field() }}
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="small border">
    	<td>Start Date-Time:</td>
        <td>End Date-Time:</td>
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
<form id="form_reset" action="{{ route('bodies.filters.remove') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
</form>
<!-- List Bodies -->
<div class="row m-1">
@foreach ($items as $item)
    <!-- Check if we know person type: -->
    @php ($person_type = "")
    @php ($person_type_id = -1)

    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <div class="tile" @if ($item->vagidentified>0) style="background-color:#fff4e8" @endif>
            <div class="wrapper">
                <div class="header">{{date("m/d/Y H:i:s", strtotime($item->captureTime))}}</div>
                <div class="banner-img row pr-4 pl-4 pt-1 pb-1">
                    <div id="item_{{$item->id}}" class="col-7 bgimg" style="cursor: pointer; border-radius:5px; background-image: url('{{ asset('Media/Frames/')}}/{{$item->img_name}}');
                          background-size: {{$item->img_width}}px {{$item->img_height}}px; resize: both; background-position: {{$item->img_posX}}% {{$item->img_posY}}%" data-toggle="modal" data-target="#modal-picture" data-img_available="{{$item->img_available}}" data-imgurl="{{ asset('Media/Frames/')}}/{{$item->img_name}}" data-topx="{{$item->topLeftX}}" data-topy="{{$item->topLeftY}}" data-areaw="{{$item->width}}" data-areah="{{$item->height}}" data-qb="{{number_format($item->probability,2)}}" data-topcolor="{{$item->topColor}}" data-bottomcolor="{{$item->bottomColor}}" data-camera="{{$item->description}}" data-time="{{date('m/d/Y H:i:s', strtotime($item->captureTime))}}" onclick="showImage({{$item->id}},'FaceMatch.com')"></div>
                    <div class="col-5">
                        <div class="small">
                            W:&nbsp;{{$item->width}}
                        </div>
                        <div class="small">
                            QB:&nbsp;{{number_format($item->probability,0)}}%
                        </div>
                    </div>
                </div>
                <div class="stats">
                    <div>
                        <strong>BODY#</strong>{{$item->id}}
                    </div>
                    <div>
                        <strong>&nbsp;</strong>&nbsp;
                    </div>
                    <div>
                        <strong>CAMERA</strong>{{$item->description}}
                    </div>
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
        <div class="modal-content">
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
                            <div class="ml-3" id="pic_top_area"></div><div class="ml-3" id="pic_bottom_area"></div>
                            
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card small">
                          <ul id="picFeatures" class="list-group list-group-flush p-0">
                            <li class="list-group-item p-1">ID: <span id="feat_id"></span></li>
                            <li class="list-group-item p-1">QB (Accuracy): <span id="feat_qb"></span>%</li>
                            <li class="list-group-item p-1">Body size (WxH): <span id="feat_bodysize"></span></li>
                            <li class="list-group-item p-1">Top Color: <span id="feat_topcolor"></span></li>
                            <li class="list-group-item p-1">Bottom Color: <span id="feat_bottomcolor"></span></li>
                            <li class="list-group-item p-1">Camera: <span id="feat_camera"></span></li>
                            <li class="list-group-item p-1">Time: <span id="feat_time"></span></li>
                          </ul>
                        </div>
                        <div>
                            <form id="form_body_export" method="GET">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-sm crud-submit btn-primary m-1">Export Body Image</button>
                        </div>
                    </div>
                    <input type="hidden" id="feat_person_type"/>
                </div>
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

function showImage(id_item,file_title) {
    var img_src = $("#item_"+id_item).data("imgurl");
    var img_available = $("#item_"+id_item).data("img_available");
    console.log(img_available);
    if (img_available>0)
        $("#modal-picture #img-zoom").attr("src",img_src);
    else
        $("#modal-picture #img-zoom").attr("src","{{asset('images/not-available.jpg')}}");
    $("#modal-picture #img-zoom").data("item_id",id_item);
    $("#modal-picture #img-zoom").data("img_available",img_available);
    $("#modal-picture #img-zoom").data("top_color",$("#item_"+id_item).data("topcolor"));
    $("#modal-picture #img-zoom").data("bottom_color",$("#item_"+id_item).data("bottomcolor"));
    $("#modal-picture #modal_title").text(file_title);
    //Remove las selected area (if exist)
    $("#pic_top_area").removeAttr('style');
    $("#pic_bottom_area").removeAttr('style');
    //Load features:
    $("#modal-picture #feat_id").text(id_item);
    $("#modal-picture #feat_qb").text($("#item_"+id_item).data("qb"));
    $("#modal-picture #feat_bodysize").text($("#item_"+id_item).data("areaw")+" x "+$("#item_"+id_item).data("areah"));
    $("#modal-picture #feat_topcolor").text($("#item_"+id_item).data("topcolor"));
    $("#modal-picture #feat_bottomcolor").text($("#item_"+id_item).data("bottomcolor"));
    $("#modal-picture #feat_camera").text($("#item_"+id_item).data("camera"));
    $("#modal-picture #feat_time").text($("#item_"+id_item).data("time"));
    //Export body image button - form action:
    $("#modal-picture #form_body_export").attr("action","{{url('body/export/save')}}/"+id_item);
}

$("#modal-picture").on('shown.bs.modal', function(){
    //Get Item ID:
    var id_item = $("#modal-picture #img-zoom").data("item_id");
    var top_color = $("#modal-picture #img-zoom").data("top_color");
    var bottom_color = $("#modal-picture #img-zoom").data("bottom_color");
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
        var posX = $("#item_"+id_item).data("topx");
        var posY = $("#item_"+id_item).data("topy");
        var areaw = $("#item_"+id_item).data("areaw");
        var areah = $("#item_"+id_item).data("areah");
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


        $("#pic_top_area").attr("style","position:absolute;top:"+posTopY+"px;left:"+posTopX+"px;width:"+areawTop+"px;height:"+areahTop+"px;border:2px solid "+top_color+";background-color:transparent");
        $("#pic_bottom_area").attr("style","position:absolute;top:"+posBottomY+"px;left:"+posBottomX+"px;width:"+areawBottom+"px;height:"+areahBottom+"px;border:2px solid "+bottom_color+";background-color:transparent");
        
    }
});

</script>
@endsection