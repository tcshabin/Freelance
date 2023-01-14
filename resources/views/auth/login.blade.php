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
   <h3 align="center"><span style="color:red;">Multi-Login
    
   </h3><br />
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
        <div class="form-group pull-right">
           <button type="submit" class="btn btn-success">Login</button>
        </div>
    </form>
  </div>
 </body>
 </html>

