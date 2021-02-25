@extends('layouts.intranet')
@section('css_custom')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
@endsection
@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">Alert Settings</li>
</ol>
<!--Sections: Email | Telegram  -->
<div class="row mb-1 float-left">
    <div class="col-12">
        <ul class="nav nav-tabs d-inline-flex">
          <li class="nav-item">
              <a class="nav-link" href="{{ route('alert.setting',['section'=>$section_em]) }}">Email</a>
          </li>
          <li class="nav-item">
              <a class="nav-link active" href="{{ route('alert.setting',['section'=>$section_id]) }}">Telegram</a>
          </li>
        </ul>
    </div>
</div>
<table class="table-bordered table-sm" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-info text-light">
        <tr>
            <th>BOT INFO</th>
        </tr>
    </thead>
</table>
<form id="form_email" method="POST">
  <table class="table-bordered table-sm mt-1" id="dt_list" width="100%" cellspacing="0">
      <thead class="bg-light small">
          <tr>
              <th class="col-5">BOT TOKEN</th>
              <th class="col-5">BOT NAME</th>
              <th class="col-1"></th>
              <th class="col-1"></th>
          </tr>
      </thead>
      <tbody class="small">
          <tr>
              <td>{{$bot->token}}</td>
              <td></td>
              <td><a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_tel_features">Enter Bot Token</a></td>
              <td><a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_telegram_bot_test" onclick="telTestBot({{$bot->id}})">Test Bot</a></td>
          </tr>
      </tbody>
  </table>
</form>
<table class="table-bordered table-sm mt-5" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-info text-light">
        <tr>
            <th>USER INFO</th>
        </tr>
    </thead>
</table>
<form id="form_email" method="POST">
  <table class="table-bordered table-sm mt-1" id="dt_list" width="100%" cellspacing="0">
      <thead class="bg-light small">
          <tr>
              <th class="col-4">FIRST NAME</th>
              <th class="col-4">LAST NAME</th>
              <th class="col-3">TRUSTED</th>
              <th class="col-1"></th>
          </tr>
      </thead>
      <tbody class="small">
        @foreach ($telUsers as $telUser)
          @php ($trusted = "")
          @if ($telUser->trusted == 1)
            @php ($trusted = "checked")
          @endif
          <tr>
              <td id="firstname_{{$telUser->id}}">{{$telUser->firstName}}</td>
              <td id="lastname_{{$telUser->id}}">{{$telUser->lastName}}</td>
              <td><input type="checkbox" id="trusted_{{$telUser->id}}" data-trusted="{{$telUser->trusted}}" onclick="telUserTrustedState({{$telUser->id}})" {{$trusted}} /></td>
              <td><a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal_telegram_remove" onclick="telUserRemove({{$telUser->id}})">Remove</a></td>
          </tr>
        @endforeach
      </tbody>
  </table>
</form>

<!-- Edit bot token -->
<div class="modal fade" id="modal_tel_features" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_email" method="POST" action="{{route('alert.setting.telegram.bot.update',['id'=>$bot->id])}}">
                    {{ csrf_field() }}
                    <input type="hidden" id="_method" name="_method" value="PUT"/>
                    <div class="form-group mb-1">
                        <label class="control-label" for="tel_token">Update Bot Token:</label>
                    </div>
                    <div class="form-group mt-1">
                        <input type="text" id="tel_token" name="tel_token" class="form-control" value="{{$bot->token}}" required />
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
<!-- Remove telegram user -->
<div class="modal fade" id="modal_telegram_remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                    <input type="hidden" id="tel_id" name="tel_id" />
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

<!-- Bot Test -->
<div class="modal fade" id="modal_telegram_bot_test" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">Runing bot test, please wait ...</h5>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_custom')
<script type="text/javascript">
function telUserRemove(id_item) {
  var tel_firstName = $("#firstname_"+id_item).text();
  var tel_lastName = $("#lastname_"+id_item).text();
  $('#modal_telegram_remove #tel_id').val(id_item);
  $('#modal_telegram_remove #form_delete').attr('action',"{{url('alert/setting/telegram/user')}}/"+id_item);
  $('#modal_telegram_remove #title').text("Remove user '"+tel_firstName+" "+tel_lastName+"' from database. Are you sure?");
}

function telUserTrustedState(telUser_id) {
    var telUser_trusted = $('#trusted_'+telUser_id).data("trusted");
    var new_state = 0;
    if (telUser_trusted==0) new_state = 1;

    $.ajax({
        type: 'PUT',
        url: "{{url('alert/setting/telegram/user/trusted')}}/"+telUser_id,
        data: { _token: "{{ csrf_token() }}", user_id: telUser_id, trusted: telUser_trusted },
        success: function(data){
            console.log("OK: "+data);
            if (data!=new_state) {
                alert("Can not change trusted value, please retry.");
                if (telUser_trusted==1)
                    $('#trusted_'+telUser_id).prop('checked', true);
                else
                    $('#trusted_'+telUser_id).prop('checked', false);
            } 
            $('#trusted_'+telUser_id).data("trusted",data);
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            //console.log("error:"+error.Message);
        }
    });
}

function telTestBot(botID) {
    $('#modal_telegram_bot_test #title').text("Runing bot test, please wait ...");
    $.ajax({
        type: 'PUT',
        url: "{{route('alert.setting.telegram.test.bot')}}",
        data: { _token: "{{ csrf_token() }}", bot_id: botID },
        success: function(data){
            console.log(data);
            if (data.length>0) {
              $('#modal_telegram_bot_test #title').text("Bot Test succesfully, Username: "+data);
            } else {
              $('#modal_telegram_bot_test #title').text("Bot Test faillure, please review bot token and try again.");
            }
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            //console.log("status:"+status);
            if(status === 'error') {     
                $('#modal_telegram_bot_test #title').text("Bot test error, please review token and try again.");
            }
        },
        timeout:7000
    });
}
</script>
@endsection