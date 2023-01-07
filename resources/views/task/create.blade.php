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
   <h3 align="center"><span style="color:red;">Task Management System
    &nbsp;&nbsp;<a href="\tasks"  target="_blank" class="btn btn-success">Task List</a>
   </h3><br />
   <div id="validation-errors"></div>
    <form method="post" enctype="multipart/form-data" id="handleAjax">
    <input type="hidden" name="_token"  id="_token" value="{{csrf_token()}}">
        <div class="form-group">
            <label>Project</label>
            <select class="form-control chosen-select" name="project" id="project" required="">
                <option value="">Select Project</option>
                @foreach($projects as $pro)
                    <option value="{{$pro->id}}">{{$pro->name}}</option>
                @endforeach
            </select>
        </div>   

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" id="name" class="form-control" required=""/>
        </div>
        <div class="form-group pull-right">
            <input type="submit" name="create" class="btn btn-primary" value="Add" />
        </div>
    </form>
  </div>
 </body>
 
 <script src="../../admin/taskformsubmit.js"></script>

</html>

