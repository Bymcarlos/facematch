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
              <a class="nav-link active" href="{{ route('reports',['section'=>$section_id]) }}">Charts</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="#" onclick="resetAlertsFilters()">Alert Logs</a>
          </li>
        </ul>
    </div>
</div>
<!-- Filters -->
<form id="form_filters" data-toggle="validator" action="{{ route('reports.charts.filters') }}" method="POST">
  {{ csrf_field() }}
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="small border">
        <td width="8%">Start Date:</td>
        <td width="8%">End Date:</td>
        <td width="12%">Type of chart:</td>
        <td width="12%">Statistic type:</td>
        <td width="12%">Camera:</td>
        <td width="12%">Alert type:</td>
        <td width="12%">Vagrancy type:</td>
        <td width="12%">Person type:</td>
        <td width="12%">Person:</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr id="filters" class="small border">
        <td><input type="text" class="form-control form-control-sm" style="width: 150px" id="startTime" name="startTime"></td>
        <td><input type="text" class="form-control form-control-sm" style="width: 150px" id="endTime" name="endTime"/></td>
        <td><select class="form-control form-control-sm" id="chartType" name="chartType">
          @if (isset($params["chartType"]))
            <option value="-1" disabled>Select chart type</option>
            @foreach ($chartTypes as $id => $value)
                @php ($sel="")
                @if (isset($params["chartType"]) and $params["chartType"] == $id) @php ($sel="selected") @endif
                <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
            @endforeach
          @else
            <option value="-1" selected disabled>Select chart type</option>
            @foreach ($chartTypes as $id => $value)
                <option value="{{ $id }}">{{ $value }}</option>
            @endforeach
          @endif
            </select></td>
        <td><select class="form-control form-control-sm" id="statType" name="statType" onclick="checkStatType()">
          @if (isset($params["statType"]))
            <option value="-1" disabled>Select statistic type</option>
            @foreach ($statTypes as $id => $value)
                @php ($sel="")
                @if (isset($params["statType"]) and $params["statType"] == $id) 
                  @php ($sel="selected") 
                @endif
                <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
            @endforeach
          @else
            <option value="-1" selected disabled>Select statistic type</option>
            @foreach ($statTypes as $id => $value)
                <option value="{{ $id }}">{{ $value }}</option>
            @endforeach
          @endif
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
          @php ($disabled="disabled='true'")
          @if (isset($params["statType"]) and $params["statType"] == 3)
            @php ($disabled="")
          @endif
          <select class="form-control form-control-sm" id="alertType" name="alertType" {{$disabled}} onclick="checkAlertType()">
                @foreach ($alertTypes as $id => $value)
                    @php ($sel="")
                    @if (isset($params["alertType"]) and $params["alertType"] == $id) @php ($sel="selected") @endif
                    <option value="{{ $id }}" {{$sel}}>{{ $value }}</option>
                @endforeach
            </select></td>
        <td>
          @php ($disabled="disabled='true'")
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
        <td>
          @php ($disabled="disabled='true'")
          @if (isset($params["statType"]) and $params["statType"] == 3)
            @php ($disabled="")
          @endif
          <select class="form-control form-control-sm" id="person" name="person" {{$disabled}}>
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
        <td><button type="button" class="btn crud-submit btn-success" onclick="checkDates()">View</button></td>
        <td><button type="button" class="btn btn-secondary" onclick="resetChartsFilters()">Reset</button></td>
        <td>@if ($showChart)
          <button type="button" class="btn btn-primary" id="save-pdf">PDF</button>
          @endif
        </td>
        
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
<!-- Chart Area -->
<div class="container">
   <div class="panel panel-default">
    <div class="panel-body" align="center">
     <div id="chart_area" style="width:900px; height:300px;">

     </div>
    </div>
   </div>
   
  </div>
@endsection

@section('js_custom')
<script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script>
$(function () {
  $('#startTime').datetimepicker({format: 'MM/DD/YYYY'});
    @isset($params['startTime'])
        $('#startTime').data("DateTimePicker").defaultDate("{{$params['startTime']}}");
    @endisset
  $('#endTime').datetimepicker({format: 'MM/DD/YYYY'});
    @isset($params['endTime'])
        $('#endTime').data("DateTimePicker").defaultDate("{{$params['endTime']}}");
    @endisset
});

function checkDates() {
  var startTime = $('#startTime').val();
  var endTime = $('#endTime').val();
  var start = new Date(startTime);
  var end = new Date(endTime);
  if (isNaN(start) || isNaN(end)) {
    alert ("Please select a Start and End Date.");
  } else {
    if (start.getTime()>end.getTime()) {
      alert ("Start Date must be before End Date");
    } else {
      var chartType = $('#chartType').val();
      if (!chartType) {
          alert("Chart Type is required");
          exit;
      }
      var statType = $('#statType').val();
      if (!statType) {
          alert("Statistic Type is required");
          exit;
      }
      $('#form_filters').submit();
    }
  }
}

function resetChartsFilters() {
  $('#form_charts_reset').submit();
}
function resetAlertsFilters() {
  $('#form_alerts_reset').submit();
}

function checkStatType() {
  if ($('#filters #statType').val() == 3) {
    //Enable Person Filter
    $('#filters #person').removeAttr('disabled');
    //Enable Alert Type Filter
    $('#filters #alertType').removeAttr('disabled');
  } else {
    //Disable Person Filter
    $('#filters #person').val(-1);
    $('#filters #person').attr('disabled','true');
    //Disable Alert Type and Vagrancy Type Filters
    $('#filters #alertType').val(-1);
    $('#filters #alertType').attr('disabled','true');

    $('#filters #vagType').val(-1);
    $('#filters #vagType').attr('disabled','true');
  }
}
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
@if ($showChart)
<script type="text/javascript" src="{{ asset('js/google-charts/loader.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
<script type="text/javascript">
     var analytics = {!! $chart_data !!};
     console.log(analytics);

     google.charts.load('current', {'packages':['corechart']});

     google.charts.setOnLoadCallback(drawChart);

     function drawChart() {
      var options = {
         title : '{{$chartTitle}}',
         legend : {
            position: 'labeled'
         },
         colors: [{!! $chart_colors !!}]
      };

      var data = google.visualization.arrayToDataTable(analytics);
      
      @if ($showChart && $params["chartType"]==0)
        
        var chart = new google.visualization.PieChart(document.getElementById('chart_area'));
      @endif
      @if ($showChart && $params["chartType"]==1)

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_area'));
      @endif
      @if ($showChart && $params["chartType"]==2)
      var chart = new google.visualization.LineChart(document.getElementById('chart_area'));
      @endif

      @if ($showChart)
        var btnSave = document.getElementById('save-pdf');
        btnSave.addEventListener('click', function () {
          var doc = new jsPDF();
          doc.addImage(chart.getImageURI(), 0, 0);
          doc.save('chart.pdf');
        }, false);
      
        chart.draw(data, options);

      @endif
     }
</script>
@endif
@endsection