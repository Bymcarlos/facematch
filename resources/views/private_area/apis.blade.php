@extends('layouts.intranet')
@section('css_custom')
<style type="text/css">
    #print_to_pdf{
      visibility:hidden;
      height:0px;
    }
</style>
@endsection
@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">Api</li>
</ol>

<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light small">
        <tr>
            <th width="2%">#</th>
            <th width="16%">SYSTEM</th>
            <th width="16%">ALERT TYPE</th>
            <th width="16%">INPUT</th>
            <th width="16%">IP:PORT</th>
            <th width="14%">ENABLE</th>
            <th width="10%"><a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_api_admin" onclick="loadDialog()">Add Api</a></th>
            <th width="10%"><a href="#" class="btn btn-secondary btn-sm" onclick="demoFromHTML()">Print as PDF</a></th>
        </tr>
    </thead>
    <tbody class="small">
        @foreach ($apis as $api)
            @if ($api->enabled==1)  
                @php ($api_enable = "checked")
            @else 
                @php ($api_enable = "") 
            @endif
            @php ($virtual_input = "")
            @php ($ip_port = $api->ip_address.":".$api->port)
            @if (strtolower($api->system_name)=="axis")
                @php ($virtual_input = "Virtual Input: ".$api->virtual_input)
                @php ($ip_port = "")
            @endif
            <tr>
                <td id="list_api_{{$api->id}}">{{$api->id}}</td>
                <td id="list_system_{{$api->id}}" data-virtual_port="{{$api->virtual_input}}">{{$api->system_name}}</td>
                <td id="list_alert_type_{{$api->id}}">{{$api->alert_type}}</td>
                <td>{{$virtual_input}}</td>
                <td>{{$ip_port}}</td>
                <td><input type="checkbox" class="form-control" id="enabled_{{$api->id}}" {{$api_enable}}  onclick="apiEnable({{$api->id}},{{$api->enabled}})"></td>
                <td><a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_api_admin" onclick="loadDialog({{$api->id}})">Edit</a></td>
                <td><a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal_api_remove" onclick="apiRemove({{$api->id}})">Delete</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
<div id="print_to_pdf">
    <table id="tab_customers" width="1200" style="font-size:8px;">
        <thead>         
            <tr class="small">
                <th width="3%">No.</th>
                <th width="7%">Integration</th>
                <th width="18%">Virtual Input</th>
                <th width="18%">Alarm Message Title</th>
                <th width="18%">Alarm Message Description</th>
                <th width="18%">Email Subject</th>
                <th width="18%">Email Message</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($apis as $api)
                @if (strtolower($api->system_name)=="axis")
                <tr class="small">
                    <td>{{$api->id}}</td>
                    <td>{{$api->system_name}}</td>
                    <td>{{$api->virtual_input}}</td>
                    <td>FaceMatch</td>
                    <td>{{$api->alert_type}} Alert!</td>
                    <td>FaceMatch Alert</td>
                    <td>{{$api->alert_type}} Alert!</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table> 
</div>

<!-- Add/Edit Api -->
<div class="modal fade" id="modal_api_admin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                    <input type="hidden" id="api_id" name="api_id">
                    <div class="form-group mb-1 row">
                        <div class="col-4 text-left">
                            <label class="control-label">Integrate with:</label>
                        </div>
                        <div class="col-6">
                            <select class="form-control" id="integrate" name="integrate" onchange="integrateSelected(this.value)">
                                <option val="0" selected disabled>Select item</option>
                                @foreach ($integrate_list as $item)
                                    <option value="{{$item}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="fc_input_port" class="form-group mb-1 row" style="display: none">
                        <div class="col-4 text-left">
                            <label class="control-label">Virtual Input Port:</label>
                        </div>
                        <div class="col-6">
                            <select class="form-control" id="input_port" name="input_port">
                                <option val="0" selected disabled>Select number</option>
                                @for ($p=1;$p<=32;$p++)
                                    <option value="{{$p}}">{{$p}}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div id="fc_alert_type" class="form-group mb-1 row" style="display: none">
                        <div class="col-4 text-left">
                            <label class="control-label">Alert Type:</label>
                        </div>
                        <div class="col-6">
                            <select class="form-control" id="alert_type" name="alert_type">
                                <option val="0" selected disabled>Select item</option>
                                @foreach ($alert_types as $item)
                                    <option value="{{$item}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="fc_ip" class="row form-group mb-1 row" style="display: none">
                        <div class="col-4 text-left">
                            <label class="control-label">IP:</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="ip" name="ip" class="form-control" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$"/>
                        </div>
                    </div>
                    <div id="fc_port" class="row form-group mb-1 row" style="display: none">
                        <div class="col-4 text-left">
                            <label class="control-label">Port Number:</label>
                        </div>
                        <div class="col-6">
                            <input type="number" id="port" name="port" class="form-control"/>
                        </div>
                    </div>
                    <div id="fc_username" class="row form-group mb-1 row" style="display: none">
                        <div class="col-4 text-left">
                            <label class="control-label" for="username">Username:</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="username" name="username" class="form-control"/>
                        </div>
                    </div>
                    <div id="fc_password" class="row form-group mb-1 row" style="display: none">
                        <div class="col-4 text-left">
                            <label class="control-label" for="username">Password:</label>
                        </div>
                        <div class="col-6">
                            <input type="text" id="password" name="password" class="form-control"/>
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

<!-- Remove api -->
<div class="modal fade" id="modal_api_remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">Remove API from database. Are you sure?</h5>
            </div>
            <div class="modal-body">
                <table class="table-bordered table-sm table-hover mb-2" id="dt_list" width="100%" cellspacing="0">
                    <thead class="bg-light small">
                        <tr>
                            <th width="4%">#</th>
                            <th width="48%">SYSTEM</th>
                            <th width="48%">ALERT TYPE</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                            <tr>
                                <td id="api_id"></td>
                                <td id="system"></td>
                                <td id="alert_type"></td>
                            </tr>
                    </tbody>
                </table>
                <form data-toggle="validator" id="form_delete" method="POST">
                    <input type="hidden" name="_method" value="DELETE" />
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

<!-- FaceMatch is running, can't make changes on api enable/disable status -->
<div class="modal fade" id="modal_api_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">FaceMatch is running, can not enable/disable APIs.</h5>
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
function apiEnable(api_id,api_enable) {
    //Show current state (checked or unchecked) until check if we can make changes:
    if (api_enable)
        $('#enabled_'+api_id).prop('checked', true);
    else
        $('#enabled_'+api_id).prop('checked', false);

    //Check if FaceMatch is running:
    if ({{$engine_status}}<0) { //Stopped, can make changes:
        window.location.href = "{{url('api/enable/')}}/"+api_id;
    } else {
        $('#modal_api_alert').modal('show'); 
    }
}

function apiRemove(id_item) {
  $('#modal_api_remove #api_id').text($("#list_api_"+id_item).text());
  $('#modal_api_remove #system').text($("#list_system_"+id_item).text());
  $('#modal_api_remove #alert_type').text($("#list_alert_type_"+id_item).text());
  $('#modal_api_remove #form_delete').attr('action',"{{url('api')}}/"+id_item);
  $('#modal_api_remove #title').text("Remove '"+info+"' from database. Are you sure?");
}

function loadDialog(api_id=0) {
    $('#modal_api_admin #api_id').val(api_id);
    hiddeControls();
    if (api_id>0) { //Edit
        $('#modal_api_admin #title').text("Edit API");
        $('#modal_api_admin #form_admin').attr('action',"{{url('api')}}/"+api_id);
        $('#modal_api_admin #_method').val('PUT');
        var system = $('#list_system_'+api_id).text();
        $('#modal_api_admin #integrate').val(system);
        showControls(system,api_id);
    } else {    //Add new api:
        $('#modal_api_admin #title').text("Add new API");
        $('#modal_api_admin #form_admin').attr('action',"{{route('api.store')}}");
        $('#modal_api_admin #_method').val('POST');   
        $('#modal_api_admin #integrate').prop('selectedIndex',0);
    }
}

function hiddeControls() {
    $('#modal_api_admin #fc_input_port').css('display','none');
    $('#modal_api_admin #input_port').prop('selectedIndex',0);

    $('#modal_api_admin #fc_alert_type').css('display','none');
    $('#modal_api_admin #alert_type').prop('selectedIndex',0);

    $('#modal_api_admin #fc_ip').css('display','none');
    $('#modal_api_admin #ip').val('');

    $('#modal_api_admin #fc_port').css('display','none');
    $('#modal_api_admin #port').val('');

    $('#modal_api_admin #fc_username').css('display','none');
    $('#modal_api_admin #username').val('');

    $('#modal_api_admin #fc_password').css('display','none');
    $('#modal_api_admin #password').val('');
}

function showControls(integrate_value,api_id=0) {
    $('#modal_api_admin #fc_alert_type').removeAttr('style');
    if (integrate_value.localeCompare("Axis")==0) {
        $('#modal_api_admin #fc_input_port').removeAttr('style');
        if (api_id>0) {
            //Load current data:
            $('#modal_api_admin #input_port').val($('#list_system_'+api_id).data('virtual_port'));
            $('#modal_api_admin #alert_type').val($('#list_alert_type_'+api_id).text());
        }
    } else {
        $('#modal_api_admin #fc_ip').removeAttr('style');
        $('#modal_api_admin #fc_port').removeAttr('style');
        $('#modal_api_admin #fc_username').removeAttr('style');
        $('#modal_api_admin #fc_password').removeAttr('style');
        if (api_id>0) {
            //Load current data:
            $('#modal_api_admin #alert_type').val($('#list_alert_type_'+api_id).text());
            $.ajax({
                type: 'GET',
                url: "{{url('api')}}/"+api_id,
                dataType: 'json',
                success: function(data){
                    //console.log(data);
                    $('#modal_api_admin #ip').val(data.ip_address);
                    $('#modal_api_admin #port').val(data.port);
                    $('#modal_api_admin #username').val(data.username);
                    $('#modal_api_admin #password').val(data.password);
                },
                error: function (xhr, status, error) {
                    //var err = eval("(" + xhr.responseText + ")");
                    //console.log("error:"+error.Message);
                }
            });
        }
    }
    
}

function integrateSelected(value) {
    hiddeControls();
    showControls(value);
}

function sendForm() {
    var send_form = false;
    var opt_integrate = $('#modal_api_admin #integrate').prop('selectedIndex');
    console.log(opt_integrate);

    switch (opt_integrate) {
        case 0:
            alert("Select integrate with item");
            break;
        case 1: //Axis:
            if ($('#modal_api_admin #input_port').prop('selectedIndex')==0) {
                alert("Select virtual input port");
                return;
            }
            if ($('#modal_api_admin #alert_type').prop('selectedIndex')==0) {
                alert("Select alert type");
                return;
            }
            send_form = true;
            break;
        default: //Other than Axis:
            if ($('#modal_api_admin #alert_type').prop('selectedIndex')==0) {
                alert("Select alert type");
                return;
            }
            if (!/((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$/.test($('#modal_api_admin #ip').val())) {
                $('#modal_api_admin #ip').focus();
                alert("Please set a valid IP address.");
                return;
            }
            if ($('#modal_api_admin #port').prop('selectedIndex')==0) {
                alert("Set port number");
                return;
            }
            if (!$('#modal_api_admin #username').val()) {
                $('#modal_api_admin #username').focus();
                alert("Please set username value.");
                return;
            }
            if (!$('#modal_api_admin #password').val()) {
                $('#modal_api_admin #password').focus();
                alert("Please set password value.");
                return;
            }
            send_form = true;
            break;
    }
    if (send_form) $('#modal_api_admin #form_admin').submit();   
}

/*
function ajaxPutExample(db_field,curr_state,db_field_related=null,attr=null) {
    var curr_state = $('#'+db_field).data("state");
    $.ajax({
        type: 'PUT',
        url: "{{url('api')}}/"+,
        data: { _token: "{{ csrf_token() }}", field: db_field, state: curr_state },
        success: function(data){
            console.log("OK: "+data);
            
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
        }
    });
}
*/
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
<script>
function demoFromHTML() {
    var pdf = new jsPDF('p', 'pt', 'letter');
    // source can be HTML-formatted string, or a reference
    // to an actual DOM element from which the text will be scraped.
    //source = $('#contenido')[0];
    source = $('#print_to_pdf')[0];

    // we support special element handlers. Register them with jQuery-style 
    // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
    // There is no support for any other type of selectors 
    // (class, of compound) at this time.
    specialElementHandlers = {
        // element with id of "bypass" - jQuery style selector
        '#bypassme': function (element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    };
    
    margins = {
        top: 20,
        bottom: 30,
        left: 20,
    };
    

    //pdf.setFont("arial");
    //pdf.setFontSize(7);

    // all coords and widths are in jsPDF instance's declared units
    // 'inches' in this case
    pdf.fromHTML(
        source, // HTML string or DOM elem ref.
        margins.left, // x coord
        margins.top, { // y coord
            'width': margins.width, // max width of content on PDF
            'elementHandlers': specialElementHandlers
        },

        function (dispose) {
            // dispose: object with X, Y of the last line add to the PDF 
            //          this allow the insertion of new lines after html
            pdf.save('Test.pdf');
        }, margins
    );
}
</script>
@endsection