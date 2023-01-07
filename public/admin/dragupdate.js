$(function() {

    //$("tbody").sortable();
    
    $('tbody').sortable({
        axis: 'y',
        stop: function (event, ui) {
            var data = $(this).sortable('serialize');
            //$('.dataTables_processing', $('#task_list_datatable').closest('.dataTables_wrapper')).show();
            $.ajax({
               data: data,
               type: 'GET',
               url: "/priority_update",
                            
               success:function(data) { //item,index,value
                $.TaskList();
                console.log('priority-updated');
               }, 
            }); //ajax  
	      } //stop
    }); //sortable
});