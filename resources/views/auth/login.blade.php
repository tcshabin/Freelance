<!DOCTYPE html>
<html>
 <head>
  <title>Task Management System</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
  <style type="text/css">
   .box{
    width:600px;
    margin:0 auto;
    border:1px solid #ccc;
   }
  </style>
 </head>
 <body>
  <br />
  <div class="container box">
   <h3 align="center"><span style="color:blue;">Hire a Fame</h3>
   <h4 align="center"><span style="color:black;">Welcome back, good to see you again!,</span></h4>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div>{{$error}}</div>
        @endforeach
    @endif
    <form method="post" enctype="multipart/form-data" action="{{URL('/login')}}" >
    <input type="hidden" name="_token"  id="_token" value="{{csrf_token()}}">
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control" required=""/>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control" required=""/>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Remember Me</label>

            <a href="#" class="pull-right">Forgot Password ?</a>
        </div>
        <div class="form-group">
           <button type="submit" class="btn btn-success btn-lg btn-block">Login</button>
        </div>
    </form>
        <div class="form-group">
            <h4 style="text-align:center;">Don't have an account?<a href="/register">Sign up now</a></h4>
        </div>
  </div>
 </body>
 </html>

