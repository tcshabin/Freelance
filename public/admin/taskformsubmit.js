
$("#handleAjax").on("submit", function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    
    $(':input[type="submit"]').prop('disabled', true);
   
    var responseUrl  = "/store";

    if($('#table_id').length){
        if ($('#table_id').val().length > 0) {
            var responseUrl  = "/update-task/"+$('#table_id').val();
        }
    }
   
     $.ajax({
         type: "POST",
         url: responseUrl,
         data: formData,
               contentType: false,
               processData: false,
      
        success: function (data) {
            
            if(data['status'] == false){
                $(':input[type="submit"]').prop('disabled', false);
                $.each( data['error'], function( key, value ) {
                    alert( key + ": " + value );
                });
            }else{
                swal("Success!", data['message'], "success");
                $(':input[type="submit"]').prop('disabled', false);
            }
             
        },error: function(data) {
            var errors = data.responseJSON;
            console.log(errors);
            alert('error');
        }
       
     });
});