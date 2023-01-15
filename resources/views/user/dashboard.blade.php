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
   <h3 align="center"><b><span>Authenticate Your Social Profiles&nbsp;&nbsp;<a href="\logout" class="btn btn-link">Logout</a> </h3></b><br />

   <div align="center"><img src="https://app.hireafame.com/static/media/fb.30d6e87cb7099efc8d56.jpg" ><a href="\user\facebook">Facebook</a></div>
   <br>
   <div align="center"><img src="https://app.hireafame.com/static/media/yt.4504d71f9d363b16eeec.png" width="65px" height="60px"><a href="\user\google">Youtube</a></div>
   <br>
   <div align="center"><img src="data:image/png;base64,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" ><a href="\user\instagram">Instagram</a></div>
   <!-- &nbsp;&nbsp;<a href="\logout" class="btn btn-success">Logout</a>  -->
   <button onclick="authenticate().then(loadClient)">authorize and load</button>
   <button onclick="execute()">execute</button>
  </div>
 </body>
 </html>

 <script src="https://apis.google.com/js/api.js"></script>
<script>

    
  /**
   * Sample JavaScript code for youtube.channels.list
   * See instructions for running APIs Explorer code samples locally:
   * https://developers.google.com/explorer-help/code-samples#javascript
   */

  function authenticate() {
    return gapi.auth2.getAuthInstance()
        .signIn({scope: "https://www.googleapis.com/auth/youtube.readonly"})
        .then(function() { console.log("Sign-in successful"); },
              function(err) { console.error("Error signing in", err); });
  }
  function loadClient() {
    gapi.client.setApiKey("AIzaSyA2E5FFRuCCtrpEhDnd4UajpqGhW6-RAi0");
    return gapi.client.load("https://www.googleapis.com/discovery/v1/apis/youtube/v3/rest")
        .then(function() { console.log("GAPI client loaded for API"); },
              function(err) { console.error("Error loading GAPI client for API", err); });
  }
  // Make sure the client is loaded and sign-in is complete before calling this method.
  function execute() {
    return gapi.client.youtube.channels.list({
      "part": [
        "snippet"
      ],
      "mine": true
    })
        .then(function(response) {
                // Handle the results here (response.result has the parsed body).
                console.log("Response", response);
              },
              function(err) { console.error("Execute error", err); });
  }
  gapi.load("client:auth2", function() {
    gapi.auth2.init({client_id: "164651186675-o15q3kskt47o24jjc96o14somnnklq08.apps.googleusercontent.com"});
  });
</script>

