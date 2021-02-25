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
	<li class="breadcrumb-item active">Faces</li>
</ol>
<!-- Filters -->
<form id="form_filters" data-toggle="validator" action="{{ route('faces.filters') }}" method="POST">
{{ csrf_field() }}
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="small border">
    	<td>Start Date-Time:</td>
        <td>End Date-Time:</td>
        <td>Minimum age:</td>
        <td>Maximum age:</td>
        <td>Gender:</td>
        <td>Camera:</td>
        <td>Sort by:</td>
        <td></td>
    	<td></td>
    </tr>
    <tr id="filters" class="small border">
        <td><input type="text" class="form-control form-control-sm" style="width: 150px" id="startTime" name="startTime"></td>
        <td><input type="text" class="form-control form-control-sm" style="width: 150px" id="endTime" name="endTime"/></td>
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
<form id="form_reset" action="{{ route('faces.filters.remove') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
</form>
<!-- List Faces -->
<div class="row m-1">
@foreach ($items as $item)
    <!-- Check if we know person type: -->
    @php ($person_type = "")
    @php ($person_type_id = -1)
    @isset ($identifieds[$item->id])
        @isset ($persons[$identifieds[$item->id]->person_id])
            @php ($person_type_id = $persons[$identifieds[$item->id]->person_id]->person_type)
            @php ($person_type = $personTypes[$person_type_id])
        @endisset
    @endisset
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <div class="tile" @if ($person_type_id>=0) style="background-color:{{$personTypeColors[$person_type_id]}}" @endif>
            <div class="wrapper">
                <div class="header">{{date("m/d/Y H:i:s", strtotime($item->captureTime))}}</div>
                <div class="banner-img row pr-4 pl-4 pt-1 pb-1">
                    <div id="item_{{$item->id}}" class="col-7 bgimg" style="cursor: pointer; border-radius:5px; background-image: url('{{ asset('Media/Frames/')}}/{{$item->img_name}}');
                          background-size: {{$item->img_width}}px {{$item->img_height}}px; resize: both; background-position: {{$item->img_posX}}% {{$item->img_posY}}%" data-toggle="modal" data-target="#modal-picture" data-img_available="{{$item->img_available}}" data-imgurl="{{ asset('Media/Frames/')}}/{{$item->img_name}}" data-topx="{{$item->topLeftX}}" data-topy="{{$item->topLeftY}}" data-areaw="{{$item->width}}" data-areah="{{$item->height}}" data-conf="{{number_format($item->confidence,2)}}" data-gender_id="{{$item->gender}}" data-gender="{{$genders[$item->gender]}}" data-age="{{$age_ranges[$item->age]}}" data-angry="{{number_format($item->angry,2)}}" data-happy="{{number_format($item->happy,2)}}" data-neutral="{{number_format($item->neutral,2)}}" data-surp="{{number_format($item->surprise,2)}}" data-camera="{{$item->description}}" data-facewidth="{{$item->width}}" data-trackq="{{number_format($item->accuracy,0)}}" data-time="{{date('m/d/Y H:i:s', strtotime($item->captureTime))}}" data-person_type="{{$person_type_id}}" onclick="showImage({{$item->id}},'FaceMatch.com')"></div>
                    <div class="col-5">
                        <div class="small">
                            %:&nbsp;{{number_format($item->confidence,1)}}
                        </div>
                        <div class="small">
                            W:&nbsp;{{$item->width}}
                        </div>
                        <div class="small">
                            Q:&nbsp;{{number_format($item->accuracy,0)}}
                        </div>
                    </div>
                </div>
                <div class="stats">
                    <div>
                        <strong>FACE#</strong>{{$item->id}}
                    </div>
                    <div>
                        <strong>TYPE</strong>{{$person_type}}
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
                            <div class="ml-3" id="pic_area"></div>
                            <div class="mt-2">Add this face to people Tab:</div>
                            <div class="mt-2"><button type="button" class="btn btn-sm crud-submit btn-success m-1" onclick="addKnownPerson()">Known Person</button><button type="button" class="btn btn-sm crud-submit btn-primary m-1" onclick="addNewPerson()">New Person</button></div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card small">
                          <ul id="picFeatures" class="list-group list-group-flush p-0">
                            <li class="list-group-item p-1">ID: <span id="feat_id"></span></li>
                            <li class="list-group-item p-1">Confidence: <span id="feat_conf"></span>%</li>
                            <li class="list-group-item p-1">Name: <span id="feat_name"></span></li>
                            <li class="list-group-item p-1">Gender: <span id="feat_gender"></span></li>
                            <li class="list-group-item p-1">Age: <span id="feat_age"></span></li>
                            <li class="list-group-item p-1">Angry: <span id="feat_angry"></span>%</li>
                            <li class="list-group-item p-1">Happy: <span id="feat_happy"></span>%</li>
                            <li class="list-group-item p-1">Neutral: <span id="feat_neutral"></span>%</li>
                            <li class="list-group-item p-1">Surprised: <span id="feat_surp"></span>%</li>
                            <li class="list-group-item p-1">Dissatisfied: <span id="feat_diss"></span>%</li>
                            <li class="list-group-item p-1">Camera: <span id="feat_camera"></span></li>
                            <li class="list-group-item p-1">Face width: <span id="feat_face_w"></span></li>
                            <li class="list-group-item p-1">Tracker Quality: <span id="feat_trackq"></span></li>
                            <li class="list-group-item p-1">Time: <span id="feat_time"></span></li>
                          </ul>
                        </div>
                    </div>
                    <input type="hidden" id="feat_person_type"/>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add new person -->
<div class="modal fade" id="modal_person_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_add" method="POST" action="{{route('faces.person.add')}}">
                    {{ csrf_field() }}
                    <input type="hidden" id="person_add_item_id" name="person_add_item_id"/>
                    <input type="hidden" id="img_file_path" name="img_file_path"/>
                    <input type="hidden" name="person_known_id" value="0"/>
                    <div class="row form-group mb-1">
                        <div class="col-7">
                            <label class="control-label" for="person_name">Name:</label>
                        </div>
                        <div class="col-5">
                            <label class="control-label" for="person_type">Person Type:</label>
                        </div>
                    </div>
                    <div class="row form-group mt-1">
                        <div class="col-7">
                            <input type="text" id="person_name" name="person_name" class="form-control" required="true" />
                        </div>
                        <div class="col-5">
                            <select class="form-control" id="person_type" name="person_type" required>
                                @foreach ($personTypes as $key => $value)
                                <option value="{{$key}}" @if ($key<0) disabled @endif>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="address">Address:</label>
                        <input type="text" id="address" name="address" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="info">Information:</label>
                        <textarea class="form-control" id="info" name="info"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Submit</button>
                        <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add known person -->
<div class="modal fade" id="modal_person_known" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_add" method="POST" action="{{route('faces.person.add')}}">
                    {{ csrf_field() }}
                    <input type="hidden" id="person_known_item_id" name="person_known_item_id"/>
                    <input type="hidden" id="img_file_path" name="img_file_path"/>
                    <div class="form-group">
                        <label class="control-label" for="address">Select person:</label>
                        <select class="form-control form-control-sm" id="person_known_id" name="person_known_id">
                            @foreach ($persons as $person)
                                <option value="{{ $person->id }}">{{ $person->id }}-{{ $person->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Submit</button>
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
        $("#form_filters").submit();
    else
        alert("Minimum age must be younger than Maximum age.");
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
    $("#modal-picture #img-zoom").data("person_type",$("#item_"+id_item).data("person_type"));
    $("#modal-picture #img-zoom").data("gender_id",$("#item_"+id_item).data("gender_id"));
    $("#modal-picture #img-zoom").data("img_available",img_available);
    $("#modal-picture #modal_title").text(file_title);
    //Remove las selected area (if exist)
    $("#pic_area").removeAttr('style');
    //Load features:
    $("#modal-picture #feat_id").text(id_item);
    $("#modal-picture #feat_conf").text($("#item_"+id_item).data("conf"));
    $("#modal-picture #feat_gender").text($("#item_"+id_item).data("gender"));
    $("#modal-picture #feat_age").text($("#item_"+id_item).data("age"));
    var angry = $("#item_"+id_item).data("angry");
    if (angry>=60) {
        $("#modal-picture #feat_angry").text($("#item_"+id_item).data("angry"));
        $("#modal-picture #feat_diss").text("0.00");
    } else {
        $("#modal-picture #feat_angry").text("0.00");
        $("#modal-picture #feat_diss").text($("#item_"+id_item).data("angry"));
    }
    $("#modal-picture #feat_happy").text($("#item_"+id_item).data("happy"));
    $("#modal-picture #feat_neutral").text($("#item_"+id_item).data("neutral"));
    $("#modal-picture #feat_surp").text($("#item_"+id_item).data("surp"));
    $("#modal-picture #feat_camera").text($("#item_"+id_item).data("camera"));
    $("#modal-picture #feat_face_w").text($("#item_"+id_item).data("facewidth"));
    $("#modal-picture #feat_trackq").text($("#item_"+id_item).data("trackq"));
    $("#modal-picture #feat_time").text($("#item_"+id_item).data("time"));
}

$("#modal-picture").on('shown.bs.modal', function(){
    //Get Item ID:
    var id_item = $("#modal-picture #img-zoom").data("item_id");
    var person_type = $("#modal-picture #img-zoom").data("person_type");
    var gender_id = $("#modal-picture #img-zoom").data("gender_id");
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

        var area_color = "Blue";
        if (gender_id==0) area_color= "DeepPink";
        if (person_type==0) area_color="Green";
        if (person_type==1) area_color="Red";
        if (person_type==2) area_color="Yellow";
        $("#pic_area").attr("style","position:absolute;top:"+topY+"px;left:"+topX+"px;width:"+areaw+"px;height:"+areah+"px;border:3px solid "+area_color+";background-color:transparent");
    }
});

function addNewPerson() {
    var item_id = $("#modal-picture #img-zoom").data("item_id");
    $('#modal_person_new #person_add_item_id').val(item_id);
    $('#modal_person_new #img_file_path').val($("#item_"+item_id).data("imgurl"));
    $('#modal_person_new').modal('show');
}

function addKnownPerson() {
    var item_id = $("#modal-picture #img-zoom").data("item_id");
    $('#modal_person_known #person_known_item_id').val(item_id);
    $('#modal_person_known #img_file_path').val($("#item_"+item_id).data("imgurl"));
    $('#modal_person_known').modal('show');
}
</script>
@endsection