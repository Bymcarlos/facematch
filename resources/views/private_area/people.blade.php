@extends('layouts.intranet')
@section('css_custom')
<style type="text/css">
.bgimg {
    height: 120px;
    width: 100px;
    background-repeat: no-repeat;
    border-radius: 5px;
}
</style>
@endsection

@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">People</li>
</ol>
<!-- Filters -->
<form id="filters" data-toggle="validator" action="{{ route('people.filters') }}" method="POST">
<div class="row">
	<div class="col-12 col-md-4 col-lg-3">
		<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
		    <tr class="small border">
		        <td width="25%">Total People:</td>
		        <td width="25%">VIP:</td>
		        <td width="25%">Blacklist:</td>
		        <td width="25%">Staff:</td>
		    </tr>
		    <tr class="small border">
		    	<td><input type="text" id="counter_total" class="form-control form-control-sm text-center" disabled="true" value="{{$personCount['total']}}"/></td>
		    	<td><input type="text" id="counter_0" class="form-control form-control-sm text-center" disabled="true" value="@if (isset($personCount['types'][0])) {{$personCount['types'][0]}} @else 0 @endif"/></td>
		    	<td><input type="text" id="counter_1" class="form-control form-control-sm text-center" disabled="true" value="@if (isset($personCount['types'][1])) {{$personCount['types'][1]}} @else 0 @endif"/></td>
		    	<td><input type="text" id="counter_2" class="form-control form-control-sm text-center" disabled="true" value="@if (isset($personCount['types'][2])) {{$personCount['types'][2]}} @else 0 @endif"/></td>
		    </tr>
		</table>
	</div>
	<div class="col-12 col-md-8 col-lg-9">
		{{ csrf_field() }}
		<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
		    <tr class="small border">
		        <td>Name:</td>
		        <td>Person Type:</td>
		        <td>Sort by:</td>
                <td width="1%"></td>
                <td width="1%"></td>
		        <td width="130px"></td>
		    </tr>
		    <tr class="small border">
		        <td><input type="text" class="form-control form-control-sm" id="personName" name="personName" @isset ($params["personName"]) value="{{$params['personName']}}" @endisset/></td>
		        <td><select class="form-control form-control-sm" id="personType" name="personType">
		                @foreach ($personTypes as $id => $value)
		                    @php ($sel="")
		                    @if (isset($params["personType"]) and $params["personType"] == $id) @php ($sel="selected") @endif
		                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
		                @endforeach
		            </select></td>
		        <td><select class="form-control form-control-sm" id="listOrder" name="listOrder">
		                @foreach ($listOrders as $id => $value)
		                    @php ($sel="")
		                    @if (isset($params["listOrder"]) and $params["listOrder"] == $id) @php ($sel="selected") @endif
		                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
		                @endforeach
		            </select></td>
		        <td><button type="submit" class="btn crud-submit btn-success">Apply</button></td>
                <td><button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button></td>
		        <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_person_add" onclick="personAdmin(0)">Add Person</button></td>
		    </tr>
		</table>
	</div>
</div>
</form>
<form id="form_reset" action="{{ route('people.filters.remove') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
</form>
<!-- List People -->
<div class="row">
@foreach ($persons as $person)
	@php ($pic_file = "no_image.jpg")
	@php ($pic_quality = "-")
    @php ($pic_width = "-")
	@php ($pic_height = "-")
	@if (isset($knownfaces[$person->id]))
		@php ($pic_file = $knownfaces[$person->id]->img_name )
		@php ($pic_quality = $knownfaces[$person->id]->quality)
        @php ($pic_width = $knownfaces[$person->id]->width)
		@php ($pic_height = $knownfaces[$person->id]->height)
	@endif
    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
        <div class="tile" id="person_card_{{$person->id}}" style="background-color:{{$personTypeColors[$person->person_type]}}">
            <div class="wrapper">
                <div class="header" id="person_id_{{$person->id}}" data-person_type="{{$person->person_type}}" data-address="{{$person->address}}" data-info="{{$person->info}}" data-imgurl="{{ asset('Media/KnownFaces/')}}/{{$pic_file}}" data-quality="{{$pic_quality}}" data-face_w="{{$pic_width}}" data-face_h="{{$pic_height}}" data-address="{{$person->address}}" data-added="{{date('m/d/Y H:i:s', strtotime($person->date_created))}}">{{$person->description}}</div>
                <div class="banner-img row pr-4 pl-4 pt-1 pb-1">
                    <div class="col-7 bgimg" style="cursor: pointer; background-image: url('{{ asset('Media/KnownFaces/')}}/{{$pic_file}}');
                          background-size: cover; background-position: 0% 0%;" data-toggle="modal" data-target="#modal-info-person" onclick="personShow({{$person->id}})"></div>
                    <div class="col-5 pl-1">
                    	<div>
	                        <small>W: {{$pic_width}}</small>
	                    </div>
	                    <div>
	                        <small>Q: {{$pic_quality}}</small>
	                    </div>
                    </div>
                </div>
                <div class="stats">
                    <div style="width: 50%">
                        <strong>PERSON#</strong>{{$person->id}}
                    </div>
                    <div style="width: 50%">
                        <strong>TYPE</strong><span id="person_type_id_{{$person->id}}">{{$personTypes[$person->person_type]}}</span>
                    </div>
                </div>
                <div class="footer">
                    <a href="#" class="btn btn-success" data-toggle="modal" data-target="#modal_person_edit" onclick="personAdmin({{$person->id}})">Edit</a>
                    <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#modal_person_remove" onclick="personRemove({{$person->id}})">Remove</a>
                </div>
            </div>
        </div> 
    </div>
@endforeach
</div>
{{ $persons->links('private_area.pagination')}}
<!-- Add person -->
<div class="modal fade" id="modal_person_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_add" method="POST" action="{{route('person.store')}}">
                    {{ csrf_field() }}
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
<!-- Edit person -->
<div class="modal fade" id="modal_person_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="person_id"/>
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
                            <input type="text" id="person_name" class="form-control"/>
                        </div>
                        <div class="col-5">
                            <select class="form-control" id="person_type" required>
                                @foreach ($personTypes as $key => $value)
                                <option value="{{$key}}" @if ($key<0) disabled @endif>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="address">Address:</label>
                        <input type="text" id="address" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="info">Information:</label>
                        <textarea class="form-control" id="info"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn crud-submit btn-success" data-dismiss="modal" onclick="personUpdate()">Submit</button>
                        <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Remove person -->
<div class="modal fade" id="modal_person_remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_update" method="POST" action="{{route('person.delete')}}">
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="person_id" name="person_id" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Yes</button>
                        <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal info person -->
<div class="modal fade" id="modal-info-person" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" on>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">FaceMatch.com</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <div class="row" style="text-align: center;" id="person_pictures"></div>
                <div class="row m-1">
                    <div class="col-12" style="text-align: center;">
                        <form id="form_picture_add" method="POST" enctype="multipart/form-data" action="{{route('person.picture.add')}}">
                        {{ csrf_field() }}
                        <input type="hidden" id="person_id" name="person_id"/>
                        <input type="file" id="selectedFile" name="selectedFile" style="display: none;"/>
                        <button type="button" id="btnSelectPicture" class="btn btn-sm crud-submit btn-primary" onclick="document.getElementById('selectedFile').click();" >Add picture</button>
                        </form></div>  
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card small">
                          <ul id="picFeatures" class="list-group list-group-flush p-0">
                            <li class="list-group-item p-1">Name: <span id="feat_name"></span></li>
                            <li class="list-group-item p-1">Quality: <span id="feat_quality"></span></li>
                            <li class="list-group-item p-1">Face size: <span id="feat_face_size"></span></li>
                            <li class="list-group-item p-1">Address: <span id="feat_address"></span></li>
                            <li class="list-group-item p-1">Added on: <span id="feat_added"></span></li>
                          </ul>
                        </div>
                        <div class="card small mt-2">
                          <ul id="picFeatures" class="list-group list-group-flush p-0">
                            <li class="list-group-item p-1 text-light bg-secondary small">INFORMATION:</li>
                            <li class="list-group-item p-1"><span id="feat_info"></span></li>
                          </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_custom')
<script>
function resetFilters() {
  $('#form_reset').submit();
}

function personAdmin(person_id) {
    if (person_id>0) { //Edit
        $('#modal_person_edit #person_id').val(person_id);
        $('#modal_person_edit #person_name').val($('#person_id_'+person_id).text());
        $('#modal_person_edit #person_type').val($('#person_id_'+person_id).data('person_type'));
        $('#modal_person_edit #address').val($('#person_id_'+person_id).data('address'));
    	$('#modal_person_edit #info').text($('#person_id_'+person_id).data('info'));
    } else { //Add person:
        $('#modal_person_add #person_id').val(0);
        $('#modal_person_add #person_name').val('');
        $('#modal_person_add #person_type').val(-1);
        $('#modal_person_add #address').val('');
        $('#modal_person_add #info').text('');
    }
}

function personUpdate() {
    var personID = $('#modal_person_edit #person_id').val();
    var form_data = {};
    form_data['_token'] = "{{ csrf_token() }}";
    form_data['person_id'] = personID;
    form_data['person_type'] = $('#modal_person_edit #person_type').val();
    form_data['person_name'] = $('#modal_person_edit #person_name').val();
    form_data['address'] = $('#modal_person_edit #address').val();
    form_data['info'] = $('#modal_person_edit #info').val();
    $.ajax({
        type: 'PUT',
        url: "{{route('person.update')}}",
        data: form_data,
        success: function(data){
            objJSON = JSON.parse(data);
            if (objJSON.res>0) {
                //console.log(objJSON);
                //Update view:
                $('#person_id_'+personID).text(objJSON.data.description);
                $('#person_id_'+personID).data("person_type",objJSON.data.person_type);
                $('#person_id_'+personID).data("address",objJSON.data.address);
                $('#person_id_'+personID).data("info",objJSON.data.info);
                $('#person_type_id_'+personID).text(objJSON.person_type_value);
                $('#person_card_'+personID).attr("style","background-color:"+objJSON.person_type_color);
                //Update person type counters:
                var counters = objJSON.person_type_counts;
                console.log(counters);
                $('#counter_total').val(counters.total);
                $('#counter_0').val(counters.types[0]);
                $('#counter_1').val(counters.types[1]);
                $('#counter_2').val(counters.types[2]);
            } else {
                alert("Error: could not update person. Please review and try again.")
            }
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
        }
    });
}
    
$(document).ready(function () {

    $("#selectedFile").change(function (event) {
        //console.log('Submit');
        //stop submit the form, we will post it manually.
        event.preventDefault();

        // Get form
        var form = $('#form_picture_add')[0];

        // Create an FormData object 
        var data = new FormData(form);

        // If you want to add an extra field for the FormData
        data.append("CustomField", "This is some extra data, testing");

        // disabled the submit button
        $("#btnSelectPicture").prop("disabled", true);
        //console.log('Posting');
        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "{{route('person.picture.add')}}",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 5000,
            success: function (data) {
                console.log(data);
                $("#btnSelectPicture").prop("disabled", false);
            },
            error: function (e) {
                console.log("ERROR : ", e);
                $("#btnSelectPicture").prop("disabled", false);
            }
        });
    });

});


function personRemove(person_id) {
    $('#modal_person_remove #person_id').val(person_id);
	$('#modal_person_remove #title').text("Remove "+$('#person_id_'+person_id).text()+" from database. Are you sure?");
}

function personShow(person_id) {
    $("#modal-info-person #person_id").val(person_id);
    $("#modal-info-person #feat_name").text($("#person_id_"+person_id).text().toUpperCase());
    $("#modal-info-person #feat_quality").text($("#person_id_"+person_id).data("quality"));
    $("#modal-info-person #feat_face_size").text($("#person_id_"+person_id).data("face_w")+" x "+$("#person_id_"+person_id).data("face_h")+" pixeles");
    $("#modal-info-person #feat_address").text($("#person_id_"+person_id).data("address"));
    $("#modal-info-person #feat_added").text($("#person_id_"+person_id).data("added"));
    $("#modal-info-person #feat_info").text($("#person_id_"+person_id).data("info"));
    //Call to get person pictures:
    //var img_src = $("#person_id_"+person_id).data("imgurl");

    $.ajax({
        type: "GET",
        url: "{{url('person/pictures')}}/"+person_id,
        success: function (data) {
            //console.log(data);
            //$("#modal-info-person #person_pictures").empty();
            $("#modal-info-person #person_pictures").empty();
            var pictures = JSON.parse(data);
            //console.log (pictures);
            $.each(pictures, function(index, picture) {
                //console.log(picture.img_name);
                $("#modal-info-person #person_pictures").append("<div class='col-2'><img src='{{asset('Media/KnownFaces/')}}/"+picture.img_name+"' width='75'></div>");
            });
        },
        error: function (e) {
            console.log("ERROR : ", e);
            $("#btnSelectPicture").prop("disabled", false);
        }
    });
}
</script>
@endsection