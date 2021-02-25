<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>{{ config ('app.name', 'FACEMATCH') }}</title>

  <!-- Bootstrap core CSS -->
  <link href="{{ asset('css/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="{{ asset('css/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

  <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="{{ asset('css/grayscale.min.css') }}" rel="stylesheet">

</head>

<body id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
      <div class="navbar-brand js-scroll-trigger" href="#page-top"><img src="{{asset('images/logoo.png')}}"/></div>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        Menu
        <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#signup">Login</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Header -->
  <header class="masthead">
    <div class="container d-flex h-100 align-items-center">
      <div class="mx-auto text-center">
        <h1 class="mx-auto my-0 text-uppercase">FaceMatch</h1>
        <h2 class="text-white-50 mx-auto mt-2 mb-5">FaceMatch.com connects to your existing camera systems to provide you with real time stats. </h2>
        <a href="#" target="_blank" class="btn btn-primary js-scroll-trigger">More info</a>
      </div>
    </div>
  </header>

  <!-- Signup Section -->
  <section id="signup" class="signup-section">
    <div class="container">
      <div class="row">
        <div class="col-md-10 col-lg-8 mx-auto text-center">

          <i class="far fa-paper-plane fa-2x mb-2 text-white"></i>
          <h2 class="text-white mb-5">Login to get access!</h2>

          <form class="form-inline d-flex" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            <input type="text" class="form-control flex-fill mr-0 mr-sm-2 mb-3 mb-sm-0" name="email" value="{{ old('email') }}" required placeholder="Enter username ...">
            <input type="password" class="form-control flex-fill mr-0 mr-sm-2 mb-3 mb-sm-0" name="password" required placeholder="Enter password ...">
            <button type="submit" class="btn btn-primary mx-auto">Validate</button>
          </form>

        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-black small text-center text-white-50">
    <div class="container">
      Copyright &copy; FaceMatch {{date("Y")}}
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- Plugin JavaScript -->
  <script src="{{ asset('js/jquery-easing/jquery.easing.min.js') }}"></script>

  <!-- Custom scripts for this template -->
  <script src="{{ asset('js/grayscale.min.js') }}"></script>

</body>

</html>
