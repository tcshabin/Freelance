<!DOCTYPE html>
<html>
 <head>
  <title>Hire</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
   <h3 align="center"><b><span>Instagram Summary&nbsp;&nbsp;<a href="\logout" class="btn btn-link">Logout</a> </h3></b><br />
   <p><b>Profile Details</b> &nbsp;Instagram User-Id : {{$instagram_id}}</p>
   
   <table class="table datatable-basic" id="example">
        <thead>
          <tr class="thead-light">
            <th>No</th>
            <th>Post</th>
            <th>Followers</th>
            <th>Following</th>
          </tr>
        </thead>
        <tbody>
         
        </tbody>
    </table>
   <p align="center;">Instagram Business Verification Not Done !</p>
   </div>
 </body>
 </html>



