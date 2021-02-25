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
<!--Sections: Email | Telegram -->
<div class="row mb-1 float-left">
    <div class="col-12">
        <ul class="nav nav-tabs d-inline-flex">
          <li class="nav-item">
              <a class="nav-link active" href="{{ route('alert.setting',['section'=>$section_id]) }}">Email</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="{{ route('alert.setting',['section'=>$section_tel]) }}">Telegram</a>
          </li>
        </ul>
    </div>
</div>

<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light small">
        <tr>
            <th width="25%">EMAIL</th>
            <th width="70%">DESCRIPTION</th>
            <th width="5%" colspan="2"><a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_email_features" onclick="emailAdmin(0)">Add Email</a></th>
        </tr>
    </thead>
    <tbody class="small">
        @foreach ($emails as $email)
        <tr>
            <td id="email_{{$email->id}}">{{$email->email_address}}</td>
            <td id="desc_{{$email->id}}">{{$email->description}}</td>
            <td><a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_email_features" onclick="emailAdmin({{$email->id}})">Edit</a></td>
            <td><a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal_email_remove" onclick="emailRemove({{$email->id}})">Delete</a></td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Add / Edit email -->
<div class="modal fade" id="modal_email_features" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">FaceMatch.com</h4>
            </div>
            <div class="modal-body">
                <form id="form_email" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="_method" name="_method"/>
                    <input type="hidden" id="email_id"/>
                    <div class="form-group mb-1">
                        <label class="control-label" for="em_email">Email:</label>
                    </div>
                    <div class="form-group mt-1">
                        <input type="text" id="em_email" name="em_email" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required />
                    </div>
                    <div class="form-group mb-1">
                        <label class="control-label" for="em_description">Description:</label>
                    </div>
                    <div class="form-group mt-1">
                        <input type="text" id="em_description" name="em_description" class="form-control small" required/>
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

<!-- Remove email -->
<div class="modal fade" id="modal_email_remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                    <input type="hidden" id="email_id" name="email_id" />
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
function emailAdmin(id_item) {
    $('#modal_email_features #form_email #email_id').val(id_item);
    if (id_item>0) {
        //edit
        $('#modal_email_features #form_email').attr('action',"{{url('alert/setting/email')}}/"+id_item);
        $('#modal_email_features #form_email #_method').val('PUT');
        //Current values:
        var em_email = $("#email_"+id_item).text();
        var em_desc = $("#desc_"+id_item).text();
        $('#modal_email_features #form_email #em_email').val(em_email);
        $('#modal_email_features #form_email #em_description').val(em_desc);
    } else {
        //add new email
        $('#modal_email_features #form_email').attr('action',"{{url('alert/setting/email')}}");
        $('#modal_email_features #form_email #_method').val('POST');
        //Reset fields:
        $('#modal_email_features #form_email #em_email').val("");
        $('#modal_email_features #form_email #em_description').val("");
    }
}
function emailRemove(id_item) {
  var em_email = $("#email_"+id_item).text();
  var em_desc = $("#desc_"+id_item).text();
  $('#modal_email_remove #email_id').val(id_item);
  $('#modal_email_remove #form_delete').attr('action',"{{url('alert/setting/email')}}/"+id_item);
  $('#modal_email_remove #title').text("Remove '"+em_email+"' ("+em_desc+") from database. Are you sure?");
}
</script>
@endsection