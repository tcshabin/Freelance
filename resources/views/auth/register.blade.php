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
   <h4 align="center"><span style="color:black;">Thank You For Choosing Us!</span></h4>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div> <span align="center" style="color:red;">{{$error}}</span></div>
        @endforeach
    @endif
    <form method="post" enctype="multipart/form-data" action="{{URL('/register')}}" >
    <input type="hidden" name="_token"  id="_token" value="{{csrf_token()}}">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="username" id="username" class="form-control" required=""/>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control" required=""/>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" required=""/>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control" required=""/>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required=""/>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
        </div>
    </form>
        <div class="form-group">
            <h4 style="text-align:center;">Do you have already an account?<a href="/login">Login now</a></h4>
        </div>
  </div>
 </body>
 </html>

