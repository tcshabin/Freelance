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
   <p><b>Latest Videos</b> &nbsp;Total Subscribers : {{$channels->subscribers_count ?? 0}} &nbsp;<span>Total Videos : {{$channels->video_count ?? 0}}</span></p>
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
          @forelse($new_videos as $key=> $video)
          <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{$video->video_id}}</td>
              <td>{{$video->channel_id}}</td>
              <td>{{$video->view_count}}</td>
              <td>{{$video->like_count}}</td>
          </tr>
          @empty
          <tr>No data found</tr>
          @endforelse
        </tbody>

      </table>
   
   </div>
 </body>
 </html>



