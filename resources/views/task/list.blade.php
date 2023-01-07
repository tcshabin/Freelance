<!DOCTYPE html>
<html>
 <head>
<title>Task Management System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js" defer></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>  
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js" defer></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

  <!-- drag & drop sortable -->

  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

  <style type="text/css">
   .box{
    width:1000px;
    margin:0 auto;
    border:1px solid #ccc;
   }
   
  </style>
 </head>
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <body>
  <br />
  <div class="container box">
   <h3 align="center"><span style="color:red;">Task Management System
    &nbsp;&nbsp;<a href="\create" class="btn btn-success">Add</a>
   </h3><br />

   <div class="card-body">
    
    <div class="table-responsive">

    <div class="container">
        <div class="row">
            <div class="col-6 p-3">
              <select id="project_list" class="form-control" name="project_list">
                <option value="">Show All Projects</option>
                @foreach($projects as $pro)
                    <option value="{{$pro->id}}">{{$pro->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-6 p-3">
              <p>Note : You can use drag and drop the rows to Update the priority</p>
            </div>
        </div>
    </div>
      <table class="table card-table table-vcenter text-nowrap align-items-center" id="task_list_datatable">
      <thead class="tab-head">
          <tr>
          <th class="head-title">No</th>
            <th class="head-title">Task</th>
            <th class="head-title">Project</th>
            <th class="head-title">Date</th>
            <th class="head-title">Action</th>
          </tr>
      </thead>
      </table>
    
    </div>
  </div>
  </div>
 </body>
 <script src="../../admin/taskdatatable.js"></script>
 <script src="../../admin/dragupdate.js"></script>
</html>

