@extends('layouts.intranet')
@section('css_custom')

@endsection

@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">Channels</li>
</ol>


<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light small">
        <tr>
            <th>#</th>
            <th>TYPE</th>
            <th>IP</th>
            <th>DESCRIPTION</th>
            <th>MIN SIZE</th>
            <th>MAX SIZE</th>
            <th>MATCH %</th>
            <th>TRACKER QUALITY</th>
            <th>ENABLE</th>
            <th width="1%" colspan="3"><a href="{{route('channels.create')}}" class="btn btn-primary btn-sm">Add Channel</a></th>
        </tr>
    </thead>
    <tbody class="small">
    @php ($count=0)
    @php ($enabled_channels=0)
    @foreach ($channels as $channel)
        @php ($count++)
        @php ($ch_ip=0)
        @php ($ch_type=1)
        @if (filter_var($channel->ip, FILTER_VALIDATE_IP)) 
            @php ($ch_ip=$channel->ip)
            @php ($ch_type=0)
        @endif

        @if ($channel->filter_flag==1)
            @php ($ch_filter = "checked")
        @else
            @php ($ch_filter = "")
        @endif
        
        @if ($channel->enabled==1)  
            @php ($ch_enable = "checked")
            @php ($enabled_channels++)
        @else 
            @php ($ch_enable = "") 
        @endif
        @if ($channel->filter_flag==1)  @php ($ch_filter = "checked") @else @php ($ch_filter = "") @endif
        <tr>
            <td>{{$count}}</td>
            <td>{{$channelTypes[$ch_type]}}</td>
            <td>{{$ch_ip}}</td>
            <td>{{$channel->description}}</td>
            <td>{{$channel->minFaceSize}}</td>
            <td>{{$channel->maxFaceSize}}</td>
            <td>{{$channel->matchPercentage}}%</td>
            <td>{{$channel->trackerQuality}}</td>
            <td><input type="checkbox" class="form-control" id="enabled_{{$channel->id}}" {{$ch_enable}}  onclick="channelEnable({{$channel->id}},{{$channel->enabled}})"></td>
            <td><a href="{{route('channels.edit',['id'=>$channel->id])}}" class="btn btn-primary btn-sm">Edit</a></td>
            <td><a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal_channel_remove" onclick="channelRemove({{ $channel->id}},{{$count}},'{{$channel->description}}')">Delete</a></td>
            <td><a href="#" class="btn btn-secondary btn-sm">Test</a></td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Add / Edit channel -->
<div class="modal fade" id="modal_channel_features" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_channel" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="_method" name="_method"/>
                    <input type="hidden" id="person_id"/>
                    <div class="row form-group mb-1">
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
                    <div class="row form-group mt-1">
                        <div class="col-4">
                            <select class="form-control" id="ch_type" name="ch_type" required onchange="checkChannelType()">
                                <option value="-1" disabled selected>Select</option>
                                @foreach ($channelTypes as $key => $value)
                                <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <input type="text" id="ch_ip" name="ch_ip" class="form-control" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$"/>
                        </div>
                        <div class="col-3">
                            <input type="number" id="ch_port" name="ch_port" class="form-control"/>
                        </div>
                    </div>
                    <div class="row form-group mb-1">
                        <div class="col-6">
                            <label class="control-label" for="ch_username">Username:</label>
                        </div>
                        <div class="col-6">
                            <label class="control-label" for="ch_password">Password:</label>
                        </div>
                    </div>
                    <div class="row form-group mt-1">
                        <div class="col-6">
                            <input type="text" id="ch_username" name="ch_username" class="form-control"/>
                        </div>
                        <div class="col-6">
                            <input type="text" id="ch_password" name="ch_password" class="form-control"/>
                        </div>
                    </div>
                    <div class="row form-group mb-1">
                        <div class="col-3">
                            <label class="control-label small" for="ch_number">Number:</label>
                        </div>
                        <div class="col-4">
                            <label class="control-label small" for="ch_model">Model:</label>
                        </div>
                        <div class="col-5">
                            <label class="control-label small" for="ch_description">Description:</label>
                        </div>
                    </div>
                    <div class="row form-group mt-1">
                        <div class="col-3">
                            <input type="number" id="ch_number" name="ch_number" class="form-control small"/>
                        </div>
                        <div class="col-4">
                            <select class="form-control small" id="ch_model" name="ch_model">
                                <option value="-1" disabled selected>Select</option>
                                @foreach ($cameras as $camera)
                                <option value="{{$camera->id}}">{{$camera->camera_model}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <input type="text" id="ch_description" name="ch_description" class="form-control small" required/>
                        </div>
                    </div>
                    <div class="row form-group mb-1">
                        <div class="col-3">
                            <label class="control-label small" for="ch_min_face">Minimun Face Size (30-80):</label>
                        </div>
                        <div class="col-3">
                            <label class="control-label small" for="ch_max_face">Maximun Face Size (100-500):</label>
                        </div>
                        <div class="col-3">
                            <label class="control-label small" for="ch_match">Match Threshold (60-100)%:</label>
                        </div>
                        <div class="col-3">
                            <label class="control-label small" for="ch_tracker">Tracker Quality (0-60):</label>
                        </div>
                    </div>
                    <div class="row form-group mb-1">
                        <div class="col-3">
                            <input type="number" id="ch_min_face" name="ch_min_face" class="form-control small" min="30" max="80" required/>
                        </div>
                        <div class="col-3">
                            <input type="number" id="ch_max_face" name="ch_max_face" class="form-control small" min="100" max="500" required/>
                        </div>
                        <div class="col-3">
                            <input type="number" id="ch_match" name="ch_match" class="form-control small" min="0" max="100" required/>
                        </div>
                        <div class="col-3">
                            <input type="number" id="ch_tracker" name="ch_tracker" class="form-control small" min="0" max="60" required/>
                        </div>
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

<!-- Remove channel -->
<div class="modal fade" id="modal_channel_remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_delete" method="POST">
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="channel_id" name="channel_id" />
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

<!-- Maximum channels enabled alert -->
<div class="modal fade" id="modal_channel_max_enabled" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">You have reached the maximum number cameras that can be enabled at a time ({{$settings->camera_limit}}).<p>Go to Settings tab to change the camera limit.</p></h5>
            </div>
            <div class="modal-body">
                <button type="submit" class="btn crud-submit btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- FaceMatch is running, can't make changes on channel enable/disable status -->
<div class="modal fade" id="modal_channel_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">FaceMatch is running, can not enable/disable channels.</h5>
            </div>
            <div class="modal-body">
                <button type="submit" class="btn crud-submit btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_custom')
<script type="text/javascript">
function channelAdmin(channel_id,type,ip,port,user,pass,number,model,description,minface,maxface,match,tracker) {
    if (channel_id>0) {
        //edit
        $('#modal_channel_features #form_channel #channel_id').val(channel_id);
        $('#modal_channel_features #form_channel').attr('action',"{{url('channels')}}/"+channel_id);
        $('#modal_channel_features #_method').val('PUT');
        //Current values:
        $('#modal_channel_features #form_channel #ch_type').val(type);
        $('#modal_channel_features #form_channel #ch_ip').val(ip);
        $('#modal_channel_features #form_channel #ch_port').val(port);
        $('#modal_channel_features #form_channel #ch_username').val(user);
        $('#modal_channel_features #form_channel #ch_password').val(pass);
        $('#modal_channel_features #form_channel #ch_number').val(number);
        $('#modal_channel_features #form_channel #ch_model').val(model);
        $('#modal_channel_features #form_channel #ch_description').val(description);
        $('#modal_channel_features #form_channel #ch_min_face').val(minface);
        $('#modal_channel_features #form_channel #ch_max_face').val(maxface);
        $('#modal_channel_features #form_channel #ch_match').val(match);
        $('#modal_channel_features #form_channel #ch_tracker').val(tracker);
    } else {
        //add new channel
        $('#modal_channel_features #form_channel').attr('action',"{{url('channels')}}");
        $('#modal_channel_features #_method').val('POST');
        //Reset fields:
        $('#modal_channel_features #form_channel #ch_type').val(-1);
        $('#modal_channel_features #form_channel #ch_ip').val('');
        $('#modal_channel_features #form_channel #ch_port').val('');
        $('#modal_channel_features #form_channel #ch_username').val('');
        $('#modal_channel_features #form_channel #ch_password').val('');
        $('#modal_channel_features #form_channel #ch_number').val('');
        $('#modal_channel_features #form_channel #ch_model').val(-1);
        $('#modal_channel_features #form_channel #ch_description').val('');
        $('#modal_channel_features #form_channel #ch_min_face').val('');
        $('#modal_channel_features #form_channel #ch_max_face').val('');
        $('#modal_channel_features #form_channel #ch_match').val('');
        $('#modal_channel_features #form_channel #ch_tracker').val('');
    }
}

function channelRemove(channel_id,number,description) {
    $('#modal_channel_remove #channel_id').val(channel_id);
    $('#modal_channel_remove #form_delete').attr('action',"{{url('channels')}}/"+channel_id);
    $('#modal_channel_remove #title').text("Remove channel '"+number+"-"+description+"' from database. Are you sure?");
}

function checkChannelType(){
    var type = $('#modal_channel_features #form_channel #ch_type').val();
    if (type==0) { //IP Camera
        $('#modal_channel_features #form_channel #ch_ip').removeAttr('disabled');
        $('#modal_channel_features #form_channel #ch_ip').attr('required','true');

        $('#modal_channel_features #form_channel #ch_port').removeAttr('disabled');
        $('#modal_channel_features #form_channel #ch_port').attr('required','true');

        $('#modal_channel_features #form_channel #ch_username').removeAttr('disabled');
        $('#modal_channel_features #form_channel #ch_username').attr('required','true');

        $('#modal_channel_features #form_channel #ch_password').removeAttr('disabled');
        $('#modal_channel_features #form_channel #ch_password').attr('required','true');

        $('#modal_channel_features #form_channel #ch_number').removeAttr('disabled');
        $('#modal_channel_features #form_channel #ch_number').attr('required','true');

        $('#modal_channel_features #form_channel #ch_model').removeAttr('disabled');
        $('#modal_channel_features #form_channel #ch_model').attr('required','true');
    } else {
        //USB Camera
        $('#modal_channel_features #form_channel #ch_ip').attr('disabled','true');
        $('#modal_channel_features #form_channel #ch_ip').removeAttr('required');

        $('#modal_channel_features #form_channel #ch_port').attr('disabled','true');
        $('#modal_channel_features #form_channel #ch_port').removeAttr('required');

        $('#modal_channel_features #form_channel #ch_username').attr('disabled','true');
        $('#modal_channel_features #form_channel #ch_username').removeAttr('required');

        $('#modal_channel_features #form_channel #ch_password').attr('disabled','true');
        $('#modal_channel_features #form_channel #ch_password').removeAttr('required');

        $('#modal_channel_features #form_channel #ch_number').attr('disabled','true');
        $('#modal_channel_features #form_channel #ch_number').removeAttr('required');

        $('#modal_channel_features #form_channel #ch_model').val(-1);
        $('#modal_channel_features #form_channel #ch_model').attr('disabled','true');
        $('#modal_channel_features #form_channel #ch_model').removeAttr('required');
    }
}

function channelEnable(channel_id,channel_enable) {
    //Show current state (checked or unchecked) until check if we can make changes:
    if (channel_enable)
        $('#enabled_'+channel_id).prop('checked', true);
    else
        $('#enabled_'+channel_id).prop('checked', false);

    //Check if FaceMatch is running:
    if ({{$engine_status}}<0) { //Stopped, can make changes:
        if (channel_enable==1) {    //Change to disabled
            window.location.href = "{{url('channel/enable/')}}/"+channel_id;
        } else { //Change to enabled
            if ({{$enabled_channels}}<{{$settings->camera_limit}}) {
                window.location.href = "{{url('channel/enable/')}}/"+channel_id;
            } else {
                $('#modal_channel_max_enabled').modal('show'); 
            }
        }
    } else {
        $('#modal_channel_alert').modal('show'); 
    }
}
</script>
@endsection