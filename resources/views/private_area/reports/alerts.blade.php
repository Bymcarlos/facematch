@extends('layouts.intranet')
@section('css_custom')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
@endsection
@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">Reports</li>
</ol>
<!--Sections: Charts | Alert Logs -->
<div class="row mb-1 float-left">
    <div class="col-12">
        <ul class="nav nav-tabs d-inline-flex">
          <li class="nav-item">
              <a class="nav-link" href="#" onclick="resetChartsFilters()">Charts</a>
          </li>
          <li class="nav-item">
              <a class="nav-link active" href="{{ route('reports',['section'=>$section_id]) }}">Alert Logs</a>
          </li>
        </ul>
    </div>
</div>
<!-- Filters -->
<form id="form_filters" data-toggle="validator" action="{{ route('reports.alerts.filters') }}" method="POST">
{{ csrf_field() }}
<input type="hidden" id="export" name="export"/>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="small border">
        <td width="12%">Time Period:</td>
        <td width="12%">Camera:</td>
        <td width="12%">Alert type:</td>
        <td width="12%">Vagrancy type:</td>
        <td width="12%">Person type:</td>
        <td width="12%">Person:</td>
        <td></td>
        <td width="1%"></td>
        <td width="1%"></td>
        @if (isset($items) and count($items)>0)
        <td width="1%"></td>
        @endif
    </tr>
    <tr id="filters" class="small border">
        <td><select class="form-control form-control-sm" id="timePeriod" name="timePeriod">
                @foreach ($timePeriods as $id => $value)
                    @php ($sel="")
                    @if (isset($params["timePeriod"]) and $params["timePeriod"] == $id) @php ($sel="selected") @endif
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
        <td>
          <select class="form-control form-control-sm" id="alertType" name="alertType" onclick="checkAlertType()">
                @foreach ($alertTypes as $id => $value)
                    @php ($sel="")
                    @if (isset($params["alertType"]) and $params["alertType"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td>@php ($disabled="disabled='true'")
          @if (isset($params["alertType"]) and $params["alertType"] == 0)
            @php ($disabled="")
          @endif
          <select class="form-control form-control-sm" id="vagType" name="vagType" {{$disabled}}>
            @foreach ($vagTypes as $id => $value)
                @php ($sel="")
                @if (isset($params["vagType"]) and $params["vagType"] == $id) @php ($sel="selected") @endif
                <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
            @endforeach
          </select></td>
        <td>@php ($disabled="")
          @if (isset($params["alertType"]) and $params["alertType"] >= 0)
            @php ($disabled="disabled='true'")
          @endif
          <select class="form-control form-control-sm" id="personType" name="personType" {{$disabled}}>
                @foreach ($personTypes as $id => $value)
                    @php ($sel="")
                    @if (isset($params["personType"]) and $params["personType"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td><select class="form-control form-control-sm" id="person" name="person" {{$disabled}}>
            @if (isset($params["person"]))
                <option value="-1">All</option>
                @foreach ($persons as $person)
                    @php ($sel="")
                    @if (isset($params["person"]) and $params["person"] == $person->id) @php ($sel="selected") @endif
                    <option value="{{ $person->id }}" {{$sel}}>{{ $person->id }}-{{ $person->description }}</option>
                @endforeach
            @else
                <option selected value="-1">All</option>
                @foreach ($persons as $person)
                    <option value="{{ $person->id }}" {{$sel}}>{{ $person->id }}-{{ $person->description }}</option>
                @endforeach
            @endif
            </select></td>
        <td></td>
        <td><button type="button" class="btn crud-submit btn-success" onclick="showAlerts()">View</button></td>
        <td><button type="button" class="btn btn-secondary" onclick="resetAlertsFilters()">Reset</button></td>
        @if (isset($items) and count($items)>0)
        <td><button type="button" class="btn btn-primary" onclick="exportAlerts()">Export</button></td>
        @endif
    </tr>
</table>
</form>
<form id="form_charts_reset" action="{{ route('reports.charts.filters.remove') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
</form>
<form id="form_alerts_reset" action="{{ route('reports.alerts.filters.remove') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="DELETE">
</form>
<!-- List Alerts Logs -->
@if ($alertType<0)
<table class="table table-bordered table-sm table-hover" id="dt_list_people" width="100%" cellspacing="0">
    <thead class="thead-dark small">
        <tr>
            <th>DATE</th>
            <th>ALERT ID</th>
            <th>FACE ID</th>
            <th>PERSON NAME</th>
            <th>PERSON TYPE</th>
            <th>CONFIDENCE</th>
            <th>CAMERA</th>
            <th>CONFIRMATION</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr class="small">
          <th>{{date("d-m-Y H:i:s", strtotime($item->captureTime))}}</th>
          <td>{{$item->ALERT_ID}}</td>
          <td>{{$item->FACE_ID}}</td>
          <td>{{$item->PERSON_NAME}}</td>
          <td>{{$personTypes[$item->person_type]}}</td>
          <td>{{number_format($item->confidence,1)}}%</td>
          <td>{{$channels[$item->CHANNEL_ID]->description}}</td>
          <td>{{$confirmValues[$item->confirmed]}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@if ($alertType==0)
<table class="table table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="thead-dark small">
        <tr>
            <th>DATE</th>
            <th>ALERT ID</th>
            <th>BODY ID</th>
            <th>PROBABILITY</th>
            <th>CAMERA</th>
            <th>CONFIRMATION</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr class="small">
          <th>{{date("d-m-Y H:i:s", strtotime($item->captureTime))}}</th>
          <td>{{$item->ALERT_ID}}</td>
          <td>{{$item->body_id}}</td>
          <td>{{number_format($item->probability,0)}}%</td>
          <td>{{$channels[$item->channel_id]->description}}</td>
          <td>{{$confirmValues[$item->confirmed]}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@if ($alertType>0)
<table class="table table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="thead-dark small">
        <tr>
            <th>DATE</th>
            <th>ALERT ID</th>
            <th>FRAME ID</th>
            <th>MATCH PERCENTAGE</th>
            <th>CAMERA</th>
            <th>CONFIRMATION</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr class="small">
          <th>{{date("d-m-Y H:i:s", strtotime($item->captureTime))}}</th>
          <td>{{$item->ALERT_ID}}</td>
          <td>{{$item->frame_id}}</td>
          <td>{{number_format($item->matchPercentage,0)}}%</td>
          <td>{{$channels[$item->channel_id]->description}}</td>
          <td>{{$confirmValues[$item->confirmed]}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection

@section('js_custom')
<script>
function showAlerts() {
  $('#form_filters #export').val(0);
  $('#form_filters').submit();
}
function resetChartsFilters() {
  $('#form_charts_reset').submit();
}
function resetAlertsFilters() {
  $('#form_alerts_reset').submit();
}
function exportAlerts() {
  $('#form_filters #export').val(1);
  $('#form_filters').submit();
}
$('#dt_list_people').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,2,3,4,5,6,7]
    },
    {
        "width": "15%", 
        "targets": 0
    },
    {
        "width": "10%", 
        "targets": 1
    },
    {
        "width": "10%", 
        "targets": 2
    },
    {
        "width": "15%", 
        "targets": 3
    },
    {
        "width": "15%", 
        "targets": 4
    },
    {
        "width": "10%", 
        "targets": 5
    },
    {
        "width": "10%", 
        "targets": 6
    },
    {
        "width": "15%", 
        "targets": 7
    }],
    "dom": 'rt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]]
});
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,2,3,4,5,6,7]
    },
    {
        "width": "25%", 
        "targets": 0
    },
    {
        "width": "20%", 
        "targets": 1
    },
    {
        "width": "20%", 
        "targets": 2
    },
    {
        "width": "10%", 
        "targets": 5
    },
    {
        "width": "10%", 
        "targets": 6
    },
    {
        "width": "15%", 
        "targets": 7
    }],
    "dom": 'rt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]]
});

function checkAlertType() {
  var alertType = parseInt($('#filters #alertType').val());
  switch (alertType) {
    case -1:
      $('#filters #personType').removeAttr('disabled');
      $('#filters #person').removeAttr('disabled');
      $('#filters #vagType').val(-1);
      $('#filters #vagType').attr('disabled','true');
      break;
    case 0:
    //Enable vagrancy type filter
      $('#filters #vagType').removeAttr('disabled');
      //Disable Person Type and Person:
      $('#filters #personType').val(-1);
      $('#filters #personType').attr('disabled','true');
      $('#filters #person').val(-1);
      $('#filters #person').attr('disabled','true');
      break;
    case 1:
      //Disable Vagrancy Type, Person Type and Person:
      $('#filters #vagType').val(-1);
      $('#filters #vagType').attr('disabled','true');
      $('#filters #personType').val(-1);
      $('#filters #personType').attr('disabled','true');
      $('#filters #person').val(-1);
      $('#filters #person').attr('disabled','true');
      break;
  }
}
</script>
@endsection