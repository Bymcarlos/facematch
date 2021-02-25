<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>{{ config ('app.name', 'FACEMATCH') }}</title>

  <!-- Custom fonts for this template-->
  <link href="{{ asset('css/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
  <!-- Page level plugin CSS-->
  <link href="{{ asset('css/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">

  <link href="{{ asset('css/cards.css') }}" rel="stylesheet" type="text/css">
  @yield('css_custom')

</head>

<body id="page-top">

  <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

    <a class="navbar-brand mr-1" href="index.html">FaceMatch</a>

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
      <i class="fas fa-bars"></i>
    </button>
    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="global_help" data-toggle="modal" data-target="#modal-help">
      <i class="fas fa-question-circle" style="color: gold;"></i>
    </button>

    <!-- Navbar Options -->
    <div class="d-none d-md-inline-block text-center form-inline ml-auto mr-auto">
      @if ($engine_status<0) <h2 class="text-danger">FaceMatch Server is not running!</h2>@endif
    </div>
    <!-- Navbar Engine state and user logout -->
      <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown no-arrow mx-1">
          <a class="nav-link" href="#" data-toggle="modal" data-target="#modal-restart">
          <i class="fas fa-play-circle fa-fw"></i></a>
        </li>
        <li class="nav-item dropdown no-arrow mx-1">
          <!-- <a class="nav-link" href="#" onclick="stopEngine()"> -->
          <a class="nav-link" href="#" data-toggle="modal" data-target="#modal-stop">
          <i class="fas fa-pause-circle fa-fw"></i></a>
        </li>
        <li class="nav-item dropdown no-arrow mx-1">
          <a class="nav-link" href="#" id="engine_status" >
          @if ($engine_status>0)
            <img src="{{ asset('images/greenlight.png') }}" width="20" />
          @else
            <img src="{{ asset('images/redlight.png') }}" width="20" />
          @endif
          </a>
        </li>
        <li class="nav-item dropdown no-arrow">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-circle fa-fw"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <div class="dropdown-item">{{ Auth::user()->username }}</div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
          </div>
        </li>
      </ul>

    <!-- Navbar -->
    
    

  </nav>

  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="{{url('home')}}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('faces')">
          <i class="fas fa-fw fa-table"></i>
          <span>Faces</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('bodies')">
          <i class="fas fa-fw fa-table"></i>
          <span>Bodies</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('alerts')">
          <i class="fas fa-fw fa-table"></i>
          <span>Alerts</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('vagalerts')">
          <i class="fas fa-fw fa-table"></i>
          <span>Vagrancy</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('nonfaces')">
          <i class="fas fa-fw fa-table"></i>
          <span>Nonfaces</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('people')">
          <i class="fas fa-fw fa-table"></i>
          <span>People</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" onclick="showSection('reports/charts')">
          <i class="fas fa-fw fa-table"></i>
          <span>Reports</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('channels.index')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>Channels</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('alert.setting')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>Alert setting</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('users.index')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>Users</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('settings.index')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>Settings</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('api.index')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>Api</span></a>
      </li>
      <li class="nav-item" id="mnu_brightsign" @if (!$brightsign_enabled) style="display:none" @endif>
        <a class="nav-link" href="{{route('brightsign.index')}}">
          <i class="fas fa-fw fa-table"></i>
          <span>BrightSign</span></a>
      </li>
    </ul>
    <form id="form_main_sections" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="DELETE">
    </form>

    <div id="content-wrapper">

      <div class="container-fluid">
        @yield('content')
      </div>
      <!-- /.container-fluid -->

      <!-- Sticky Footer -->
      <footer class="sticky-footer">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright © FaceMatch {{date("Y")}}</span>
          </div>
        </div>
      </footer>

    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
        </div>
      </div>
    </div>
  </div>

<!-- Help Modal -->
<div class="modal fade" id="modal-help" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Restart Server Confirm -->
<div class="modal fade" id="modal-restart" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">FaceMatch.com</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        The server will be down for a moment while restarting the FaceMatch.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="startEngine()">Restart</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Restarting Server -->
<div class="modal fade" id="modal-restarting" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">FaceMatch.com</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="title">
        Restarting server, please wait.
      </div>
      <div class="progress m-3" id="progress_bar">
        <div class="progress-bar" id="progress_img" role="progressbar" aria-valuenow="1"
        aria-valuemin="0" aria-valuemax="100" style="width:1%">
          <span class="sr-only" id="progress_text"></span>
        </div>
      </div> 
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn_restarting" disabled="true" data-dismiss="modal">Restarting...</button>
      </div>
    </div>
  </div>
</div>

<!-- Stop FaceMatch Server Confirm -->
<div class="modal fade" id="modal-stop" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">FaceMatch.com</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure to stop engine?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="stopEngine()">Stop</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


  <!-- Bootstrap core JavaScript-->
  <script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{ asset('js/jquery-easing/jquery.easing.min.js') }}"></script>

  <!-- Page level plugin JavaScript-->
  <script src="{{ asset('js/chart.js/Chart.min.js') }}"></script>
  <script src="{{ asset('js/datatables/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('js/datatables/dataTables.bootstrap4.js') }}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{ asset('js/sb-admin.min.js') }}"></script>

  <!-- Demo scripts for this page-->
  <script src="{{ asset('js/demo/datatables-demo.js') }}"></script>
  <script src="{{ asset('js/demo/chart-area-demo.js') }}"></script>

  <script type="text/javascript">
    function showSection(section) {
      $("#form_main_sections").attr('action',"{{url('/')}}/"+section);
      $("#form_main_sections").submit();
    }

    function startEngine() {
      console.log("Calling startEngine");
      $.ajax({
          type: 'PUT',
          url: "{{route('engine.start')}}",
          data: { _token: "{{ csrf_token() }}" },
          success: function(data){
              console.log("RESULT: "+data);
              checkServerRunning();
          },
          error: function (xhr, status, error) {
              //var err = eval("(" + xhr.responseText + ")");
              console.log("error:"+error.Message);
          }
      });
    }

    function stopEngine() {
      console.log("Calling stopEngine");
      $.ajax({
          type: 'PUT',
          url: "{{route('engine.stop')}}",
          data: { _token: "{{ csrf_token() }}" },
          success: function(data){
              console.log("RESULT: "+data);
              //Check if has stopped and update the light:

          },
          error: function (xhr, status, error) {
              //var err = eval("(" + xhr.responseText + ")");
              console.log("error:"+error.Message);
          }
      });
    }

    function checkServerRunning() {
      var pingUrl = "{{route('engine.check')}}";
      var targetUrl = "{{ route('home') }}";
      var count=0;
      var myVar =setInterval(ping, 5000);

      $('#modal-restarting').modal('show'); 

      function ping() {
          count++;
          $("#modal-restarting #progress_bar #progress_img").attr('aria-valuenow',count*2);
          $("#modal-restarting #progress_bar #progress_img").attr('style',"width:"+(count*2)+"%");
          console.log(count);
          console.log("Check server");
          $.ajax({
              type: 'GET',
              url: pingUrl,
              success: function(result) {
                  //console.log(result);
                  //window.location.href = targetUrl;

                  //Server is running:     
                  $("#modal-restarting #title").text('The server is running again!');
                  $("#modal-restarting #progress_bar").hide();
                  $("#modal-restarting #btn_restarting").removeAttr('disabled');
                  $("#modal-restarting #btn_restarting").text('OK');
                  clearInterval(myVar);
              }
          });
          //We wait for more than 4 minutes, 
          if (count>50) {
              $("#modal-restarting #title").text('The server is not able to restart, please check the physical server');
              $("#modal-restarting #progress_bar").hide();
              $("#modal-restarting #btn_restarting").removeAttr('disabled');
              $("#modal-restarting #btn_restarting").text('Close');
              clearInterval(myVar);
          }
      }
    }
  </script>
  @yield('js_custom')
</body>

</html>
