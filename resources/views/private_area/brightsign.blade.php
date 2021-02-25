@extends('layouts.intranet')
@section('css_custom')

@endsection
@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">Brightsign Device</li>
</ol>

<div class="container-fluid">
    <div class="row">
        <div class="col-6 p-1 rounded-sm text-left">
        	BrightSign Device
        	<table class="table-sm mb-1 bg-light" width="100%" cellspacing="0">
			    <tr class="small border">
			    	<td class="text-center" width="30%">From Camera:</td>
			        <td class="text-center" width="30%">BrightSign Device IP Address:</td>
			        <td class="text-center" width="30%">BrightSign Device UDP Port:</td>
                    <td class="text-center" width="10%"></td>
			    </tr>
			    <tr class="small border">
			        <td class="text-center" width="30%"><select class="form-control" readonly>
			        	<option value="1">{{$channel->description}}</option>
			        </select></td>
			        <td class="text-center" width="30%"><input type="text" class="form-control-inline text-center" value="{{$brightsign->ip}}" readonly/></td>
			        <td class="text-center" width="30%"><input type="text" class="form-control-inline text-center" value="{{$brightsign->udpPort}}" readonly/></td>
                    <td><a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_device_edit" onclick="loadDialog()">Edit</a></td>
			    </tr>
			</table>
			<table class="table-sm mb-1 bg-light" width="100%" cellspacing="0">
			    <tr class="small border">
			    	<td class="text-center" width="30%">Person UDP Command:</td>
			        <td class="text-center" width="35%" id="vipUdpCommand">VIP:</td>
			        <td class="text-center" width="35%" id="staffUdpCommand">Staff:</td>
			    </tr>
			    <tr class="small border">
			        <td class="text-center" width="30%"></td>
			        <td class="text-center" width="35%"><input type="text" class="form-control-inline text-center" value="{{$brightsign->vipUdpCommand}}" readonly/><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_edit_value" onclick="editFieldValue(false,'vipUdpCommand','{{$brightsign->vipUdpCommand}}')"></i></td>
			        <td class="text-center" width="35%"><input type="text" class="form-control-inline text-center" value="{{$brightsign->staffUdpCommand}}" readonly/><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_edit_value" onclick="editFieldValue(false,'staffUdpCommand','{{$brightsign->staffUdpCommand}}')"></i></td>
			    </tr>
			</table>
        </div>
        <div class="col-6 p-1 rounded-sm text-left">
        	Group Number
        	<table class="table-sm mb-1 bg-light" width="100%" cellspacing="0">
			    <tr class="small border">
			    	<td class="text-center" id="maxFemaleGroupNumber">Total Female Age Group:</td>
			        <td class="text-center" id="maxMaleGroupNumber">Total Male Age Group:</td>
			    </tr>
			    <tr class="small border">
			        <td class="text-center"><input type="text" class="form-control-inline text-center" value="{{$brightsign->maxFemaleGroupNumber}}" readonly/><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_edit_value" onclick="editFieldValue(true,'maxFemaleGroupNumber',{{$brightsign->maxFemaleGroupNumber}},{{count($brightsignfag)}})" @if ($auth_user_level>1) hidden @endif></i></td>
			        <td class="text-center"><input type="text" class="form-control-inline text-center" value="{{$brightsign->maxMaleGroupNumber}}" readonly/><i class="fas fa-fw fa-edit fa-lg" style="color:DarkCyan;cursor: pointer;" data-toggle="modal" data-target="#modal_edit_value" onclick="editFieldValue(true,'maxMaleGroupNumber',{{$brightsign->maxMaleGroupNumber}},{{count($brightsignmag)}})" @if ($auth_user_level>1) hidden @endif></i></td>
			    </tr>
			</table>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-6 bg-light p-2 rounded-sm text-left">
        	<div class="row">
        		<div class="col-3 text-left">Female Age Group</div>
        		<div class="col-9 text-right">
        			@if (count($brightsignfag)<$brightsign->maxFemaleGroupNumber)
        				<button class="btn btn-primary mr-2" data-toggle="modal" data-target="#modal_group_admin" onclick="adminGroup(0)">Add Female Group</button>
        			@else
        				<button class="btn btn-primary mr-2" data-toggle="modal" data-target="#modal_max_reached" onclick="maxGroupsReached(0)">Add Female Group</button>
        			@endif
        		</div>
        	</div>
        	@foreach ($brightsignfag as $i => $item)
	        <div class="row m-2 p-2 border border-secondary rounded-sm text-left">
	        	<div class="col-3">Group Age {{$i+1}}</div>
	        	<div id="item_0_{{$item->id}}" data-min="{{$item->minFemaleAge}}" data-max="{{$item->maxFemaleAge}}" data-udp="{{$item->udpCommand}}" class="col-2">{{$item->minFemaleAge}}-{{$item->maxFemaleAge}}</div>
	        	<div class="col-4">UDP Command: {{$item->udpCommand}}</div>
	        	<div class="col-3 text-right"><button class="btn btn-success mr-1" data-toggle="modal" data-target="#modal_group_admin" onclick="adminGroup(0,{{$item->id}})">Edit</button><button class="btn btn-danger" data-toggle="modal" data-target="#modal_group_remove" onclick="removeGroup(0,{{$item->id}})">Remove</button></div>
	        </div>
	        @endforeach
        </div>
        <div class="col-6 bg-light p-2 rounded-sm text-left">
        	<div class="row">
        		<div class="col-3 text-left">Total Male Age Group</div>
        		<div class="col-9 text-right">
        			@if (count($brightsignmag)<$brightsign->maxMaleGroupNumber)
        				<button class="btn btn-primary mr-2" data-toggle="modal" data-target="#modal_group_admin" onclick="adminGroup(1)">Add Male Group</button>
        			@else
        				<button class="btn btn-primary mr-2" data-toggle="modal" data-target="#modal_max_reached" onclick="maxGroupsReached(1)">Add Male Group</button>
        			@endif
        		</div>
        	</div>
        	@foreach ($brightsignmag as $i => $item)
        	<div class="row m-2 p-2 border border-secondary rounded-sm text-left">
	        	<div class="col-3">Group Age {{$i+1}}</div>
	        	<div id="item_1_{{$item->id}}" data-min="{{$item->minMaleAge}}" data-max="{{$item->maxMaleAge}}" data-udp="{{$item->udpCommand}}" class="col-2">{{$item->minMaleAge}}-{{$item->maxMaleAge}}</div>
	        	<div class="col-4">UDP Command: {{$item->udpCommand}}</div>
	        	<div class="col-3 text-right"><button class="btn btn-success mr-1" data-toggle="modal" data-target="#modal_group_admin" onclick="adminGroup(1,{{$item->id}})">Edit</button><button class="btn btn-danger" data-toggle="modal" data-target="#modal_group_remove" onclick="removeGroup(1,{{$item->id}})">Remove</button></div>
	        </div>
	        @endforeach
        </div>
    </div>
</div>

<!-- Edit Field Value -->
<div class="modal fade" id="modal_edit_value" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" method="POST" action="{{route('brightsign.value.field.update')}}">
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


<!-- Edit Device -->
<div class="modal fade" id="modal_device_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_channel" method="POST" action="{{route('brightsign.device.update')}}">
                    {{ csrf_field() }}
                    <input type="hidden" id="person_id"/>
                    <div class="row form-groups small">
                        <div class="col-4">
                            <label class="control-label" for="channel_id">Type:</label>
                        </div>
                        <div class="col-6">
                            <select class="form-control" id="channel_id" name="channel_id" required>
                                @foreach($channels as $item)
                                @php ($sel="")
                                @if ($item->id==$channel->id) @php ($sel="selected") @endif
                                <option value="{{$item->id}}" {{$sel}}>{{$item->description}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group mt-1 small">
                        <div class="col-4">
                            <label class="control-label" for="ip">IP (X.X.X.X):</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="ip" name="ip" class="form-control" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$" value="{{$brightsign->ip}}" required/>
                        </div>
                    </div>
                    <div class="row form-group mt-1 small">
                        <div class="col-4">
                            <label class="control-label" for="udpPort">PORT:</label>
                        </div>
                        <div class="col-6">
                            <input type="number" id="udpPort" name="udpPort" class="form-control" value="{{$brightsign->udpPort}}" required/>
                        </div>
                    </div>
                    <div class="form-group mt-1">
                        <button type="submit" class="btn crud-submit btn-success">Submit</button>
                        <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Admin group -->
<div class="modal fade" id="modal_group_admin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="form_admin" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="_method" name="_method">
                    <input type="hidden" id="type" name="type">
                    <div class="form-group mb-1 row">
                        <div class="col-4 text-left">
                            <label class="control-label">Minimun Age:</label>
                        </div>
                        <div class="col-4 text-left">
                            <label class="control-label">Maximun Age:</label>
                        </div>
                        <div class="col-4 text-left">
                            <label class="control-label">UDP Command:</label>
                        </div>
                        
                    </div>
                    <div class="form-group mb-1 row">
                        <div class="col-4">
                            <input type="number" class="form-control" id="min_age" name="min_age">
                        </div>
                        <div class="col-4">
                            <input type="number" class="form-control" id="max_age" name="max_age">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" id="udp_command" name="udp_command">
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="button" class="btn crud-submit btn-success" onclick="sendForm()">Submit</button>
                        <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Remove group -->
<div class="modal fade" id="modal_group_remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title"></h5>
            </div>
            <div class="modal-body">
                <table class="table-bordered table-sm table-hover mb-2" id="dt_list" width="100%" cellspacing="0">
                    <thead class="bg-light small">
                        <tr>
                            <th width="40%">Min Age</th>
                            <th width="40%">Max Age</th>
                            <th width="48%">UDP Command</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                            <tr>
                                <td id="min_age"></td>
                                <td id="max_age"></td>
                                <td id="udp_command"></td>
                            </tr>
                    </tbody>
                </table>
                <form data-toggle="validator" id="form_delete" method="POST">
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="type" name="type">
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

<!-- Max age groups reached -->
<div class="modal fade" id="modal_max_reached" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">You have reached the maximum number of female age group</h5>
            </div>
            <div class="modal-body">   
                <div class="form-group">
                    <button type="submit" class="btn crud-submit btn-success" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_custom')
<script type="text/javascript">
function editFieldValue(is_number,field,value,min,max) {
    console.log(field);
    $('#modal_edit_value #form_edit #title').text($('#'+field).text());
    $('#modal_edit_value #form_edit #field').val(field);    
    if (is_number) {
        $('#modal_edit_value #form_edit #value').attr('type','number');
        $('#modal_edit_value #form_edit #value').attr('min',min);
        $('#modal_edit_value #form_edit #value').attr('max',max);
    } else {
        $('#modal_edit_value #form_edit #value').attr('type','text');
    }
    $('#modal_edit_value #form_edit #value').val(value);
}
function adminGroup(type,group_id=0) {
	//type 0 -> Female, 1-> Male
	var type_title = "female";
	if (type==1) type_title = "male";

    $('#modal_group_admin #type').val(type);
    $('#modal_group_admin #form_admin #min_age').val('');
    $('#modal_group_admin #form_admin #max_age').val('');
    $('#modal_group_admin #form_admin #udp_command').val('');
    if (group_id>0) {
    	//Edit group
    	$('#modal_group_admin #title').text("Edit "+type_title+" group");
    	$('#modal_group_admin #form_admin').attr('action',"{{url('brightsign')}}/"+group_id);
    	$('#modal_group_admin #form_admin #_method').val('put');
    	$('#modal_group_admin #form_admin #min_age').val($("#item_"+type+"_"+group_id).data('min'));
    	$('#modal_group_admin #form_admin #max_age').val($("#item_"+type+"_"+group_id).data('max'));
    	$('#modal_group_admin #form_admin #udp_command').val($("#item_"+type+"_"+group_id).data('udp'));
    } else {
    	//Add group
    	$('#modal_group_admin #title').text("Add new "+type_title+" group");
    	$('#modal_group_admin #form_admin').attr('action',"{{route('brightsign.store')}}");
    	$('#modal_group_admin #form_admin #_method').val('post');
    }
}

function removeGroup(type, group_id) {
  $('#modal_group_remove #type').val(type);
  $('#modal_group_remove #min_age').text($("#item_"+type+"_"+group_id).data('min'));
  $('#modal_group_remove #max_age').text($("#item_"+type+"_"+group_id).data('max'));
  $('#modal_group_remove #udp_command').text($("#item_"+type+"_"+group_id).data('udp'));
  $('#modal_group_remove #form_delete').attr('action',"{{url('brightsign')}}/"+group_id);
  if (type==0)
  	$('#modal_group_remove #title').text("Remove female group, are you sure?");
  else
  	$('#modal_group_remove #title').text("Remove male group, are you sure?");
}

function sendForm() {
    $('#modal_group_admin #form_admin').submit();   
}

function maxGroupsReached(type) {
	if (type==0)
	  	$('#modal_max_reached #title').text("You have reached the maximum number of female age group");
	  else
	  	$('#modal_max_reached #title').text("You have reached the maximum number of male age group");
}
</script>
@endsection