<!DOCTYPE html>
<html>
 <head>
  <title>Task Management System</title>
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
   <h3 align="center"><b><span>Youtube Summary&nbsp;&nbsp;<a href="\logout" class="btn btn-link">Logout</a> </h3></b><br />
   <center><p>&nbsp;<b>Total Subscribers</b> : {{$channels->subscribers_count ?? 0}} &nbsp;<span><b>Total Videos</b> : {{$channels->video_count ?? 0}}</span>
   &nbsp;<span><b>Average Engagement</b> : {{$channels->average_engagement ?? 0}}</span>
  </p></center>
  <center><p>&nbsp;<span><b>Description</b> : {{$channels->description ?? ''}}</span></p></center>
  <p><b>Latest Videos</b></p>
   <table class="table datatable-basic" id="example">
        <thead>
          <tr class="thead-light">
            <th>No</th>
            <th>Video id</th>
            <th>Channel Id</th>
            <th>Views</th>
            <th>Likes</th>
          </tr>
        </thead>
        <tbody>
          @foreach($new_videos as $key=> $video)
          <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{$video->video_id}}</td>
              <td>{{$video->channel_id}}</td>
              <td>{{$video->view_count}}</td>
              <td>{{$video->like_count}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @if(count($new_videos) == 0) <p align="center;">No data found</p> @endif
   </div>
 </body>
 </html>



