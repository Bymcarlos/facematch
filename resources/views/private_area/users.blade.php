@extends('layouts.intranet')
@section('css_custom')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
@endsection
@section('content')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/home')}}"><span class="nav-link-text">Dashboard</span></a></li>
	<li class="breadcrumb-item active">Users</li>
</ol>

<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light small">
        <tr>
            <th width="25%">USER NAME</th>
            <th width="25%">CREATION</th>
            <th width="23%">LEVEL</th>
            <th width="10%"></th>
            <th width="10%"></th>
            <th width="7%">
                @if($auth_user->hasAnyRole(['admin','manager']))
                <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_user_new" onclick="userNew()">Add User</a>
                @endif
            </th>
        </tr>
    </thead>
    @php ($auth_user_level = $users_levels[$auth_user->level])
    <tbody class="small">
        @foreach ($users as $user)
            @php ($user_level = $users_levels[$user->level])
            @php ($show_user = false)
            @if ($auth_user_level<=$user_level || $user_level>=2) @php ($show_user = true) @endif
            @if ($show_user)
            <tr>
                <td id="username_{{$user->id}}">{{$user->username}}</td>
                <td id="creation_{{$user->id}}">{{date('m/d/Y H:i:s', strtotime($user->creation_datetime))}}</td>
                <td id="level_{{$user->id}}">{{$user->level}}</td>
                <td>
                    @if ($auth_user_level<=$user_level && $auth_user_level<3)
                    <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_user_password" onclick="userPassword({{$user->id}})">Change Password</a>
                    @endif
                </td>
                <td>
                    @if ($auth_user_level<=$user_level && $auth_user_level<3)
                    <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_user_level" onclick="userLevel({{$user->id}})">Change Level</a>
                    @endif
                </td>
                <td>@if ($auth_user_level<=$user_level && $auth_user_level<3 && $auth_user->id != $user->id)
                    <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal_user_remove" onclick="userRemove({{$user->id}})">Delete</a>
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>

<!-- New user -->
<div class="modal fade" id="modal_user_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content">Add New User</h5>
            </div>
            <div class="modal-body">
                <form id="form_user" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="_method" name="_method" action="{{route('users.store')}}"/>
                    <div class="form-group mb-1">
                        <label class="control-label" for="username">User Name (Single word without spaces: a-z A-Z 0-9 and _):</label>
                    </div>
                    <div class="form-group mt-1">
                        <input type="text" id="username" name="username" class="form-control" pattern="^[-a-zA-Z0-9_]+$" required />
                    </div>
                    <div class="form-group mb-1">
                        <label class="control-label" for="level" id="level">Level</label>
                    </div>
                    <div class="form-group mt-1">
                        <select class="form-control" id="level" name="level" required>
                            <option value="-1" selected disabled>Select level</option>
                            @foreach ($users_levels as $key => $level)
                                <option value="{{$key}}">{{$key}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-1">
                        <label class="control-label" for="password" id="password_title"></label>
                    </div>
                    <div class="form-group mt-1">
                        <input type="text" id="password" name="password" class="form-control small" required/>
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

<!-- Change password -->
<div class="modal fade" id="modal_user_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">Change Password</h5>
            </div>
            <div class="modal-body">
                <form id="form_user" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="_method" name="_method" value="PUT"/>
                    <input type="hidden" id="user_id"/>
                    <div class="form-group mb-1">
                        <label class="control-label" for="username" id="username_title">User Name:</label>
                    </div>
                    <div class="form-group mt-1">
                        <input type="text" id="username" name="username" class="form-control" disabled />
                    </div>
                    <div class="form-group mb-1">
                        <label class="control-label" for="level" id="level">Level</label>
                    </div>
                    <div class="form-group mt-1">
                        <select class="form-control" id="level" name="level" disabled>
                        @foreach ($users_levels as $key => $level)
                            <option value="{{$key}}">{{$key}}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-1">
                        <label class="control-label" for="password" id="password_title">New Password:</label>
                    </div>
                    <div class="form-group mt-1">
                        <input type="text" id="password" name="password" class="form-control small" required/>
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

<!-- Change level -->
<div class="modal fade" id="modal_user_level" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">Change User Level</h5>
            </div>
            <div class="modal-body">
                <form id="form_user" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="_method" name="_method" value="PUT"/>
                    <input type="hidden" id="user_id"/>
                    <div class="form-group mb-1">
                        <label class="control-label" for="username" id="username_title">Use Name:</label>
                    </div>
                    <div class="form-group mt-1">
                        <input type="text" id="username" name="username" class="form-control" disabled />
                    </div>
                    <div class="form-group mb-1">
                        <label class="control-label" for="level" id="level">Level</label>
                    </div>
                    <div class="form-group mt-1">
                        <select class="form-control" id="level" name="level">
                        @foreach ($users_levels as $key => $level)
                            <option value="{{$key}}">{{$key}}</option>
                        @endforeach
                        </select>
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

<!-- Remove user -->
<div class="modal fade" id="modal_user_remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                    <input type="hidden" id="user_id" name="user_id" />
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
@endsection

@section('js_custom')
<script type="text/javascript">
function userNew() {
    //add new user
    $('#modal_user_new #password_title').text("Password:");
    $('#modal_user_new #form_user').attr('action',"{{url('users')}}");
    $('#modal_user_new #form_user #_method').val('POST');
    //Reset fields:
    $('#modal_user_password #form_user #username').removeAttr('readonly');
    $('#modal_user_password #form_user #username').val("");
}

function userPassword(id_item) {
    $('#modal_user_password #form_user #user_id').val(id_item);
    $('#modal_user_password #form_user #password').val("");
    $('#modal_user_password #form_user').attr('action',"{{url('users')}}/"+id_item);
    //Current values:
    var username = $("#username_"+id_item).text();
    var level = $("#level_"+id_item).text();
    $('#modal_user_password #form_user #username').val(username);
    $('#modal_user_password #form_user #level').val(level);
}
function userLevel(id_item) {
    $('#modal_user_level #form_user').attr('action',"{{url('user/level/update')}}/"+id_item);
    var level = $("#level_"+id_item).text();
    var username = $("#username_"+id_item).text();
    $('#modal_user_level #form_user #username').val(username);
    $('#modal_user_level #form_user #user_id').val(id_item);
    $('#modal_user_level #form_user #level').val(level);

}
function userRemove(id_item) {
  var username = $("#username_"+id_item).text();
  $('#modal_user_remove #user_id').val(id_item);
  $('#modal_user_remove #form_delete').attr('action',"{{url('users')}}/"+id_item);
  $('#modal_user_remove #title').text("Remove '"+username+"' from database. Are you sure?");
}

function checkFieldUsername(us_id) {
    var username_value = $('#form_user #username').val();
    $.ajax({
        type: 'POST',
        url: "{{route('user.check.username')}}",
        data: { _token: "{{ csrf_token() }}", username: username_value, user_id: us_id},
        success: function(data){
            if (data>0) {
                $('#form_user #username').focus();
                alert("The User Name value already exists on database. Please change and try again.");
                return;
            } else
                $("#form_user").submit();
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+error.Message);
            return 0;
        }
    });
}
</script>
@endsection