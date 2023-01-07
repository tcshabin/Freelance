$.TaskList = function() {

    $(function() {
    
    if ($('#task_list_datatable').length > 0) {
        var i = i++;
        var project_id = $('#project_list').val();
        var ajax_url = '/tasks_data?project='+project_id;

        $("#task_list_datatable").DataTable().clear().destroy();

        $('#task_list_datatable').DataTable({
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            processing: true,
            serverSide: true,
            rowReorder: true,
            responsive: true,
            destroy: true, 
            ajax: ajax_url,
            columns: [{
                    orderable: false,
                    data: "null",
                    width: '10px',
                    autoWidth: false,
                    'text-align': 'center',
                    render: function(data, type, full, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },

                {
                    orderable: false,
                    data: "null",
                    width: '50px',
                    autoWidth: false,
                    'text-align': 'center',
                    render: function(data, type, full) {
                        if (full.name != null) {
                            return full.name;
                        } else {
                            return 'NIL';
                        }
                    }
                },

                {
                    orderable: false,
                    data: "null",
                    width: '50px',
                    autoWidth: false,
                    'text-align': 'center',
                    render: function(data, type, full) {
                        if (full.project != null) {
                            return full.project;
                        } else {
                            return 'NIL';
                        }
                    }
                },
                {
                    orderable: false,
                    data: "null",
                    width: '50px',
                    autoWidth: false,
                    'text-align': 'center',
                    render: function(data, type, full) {
                        if (full.created_at != null) {
                            var first_date = moment(full.created_at).format('DD-MM-YYYY');  
                            return first_date;
                        } else {
                            return 'NIL';
                        }
                    }
                },
                {
                    orderable: false,
                    data: "null",
                    width: '50px',
                    autoWidth: false,
                    'text-align': 'center',
                    render: function(data, type, full) {
                        return '<div class="actions"><a href="/tasks/' + full.id + '"><i class="fa fa-pencil" aria-hidden="true"></i> View / Edit</a>&nbsp;&nbsp;<a style="color:red;" class="deletetask" data-id="'+full.id+'" href="#"><i class="fa fa-trash"></i> Delete</a></div>';
                    }
                    
                },
                


            ],
            // fnCreatedRow: function( nRow, aData, iDataIndex ) {
            //     alert(111);
            //     $(nRow).attr('id', aData[0]);
            // },
          

            createdRow: function(row, data, dataIndex) {

                $(row).attr('id', 'item-'+data.id);
                
                setTimeout(function() {
                    $('#task_list_datatable tbody').addClass("m-datatable__body");
                    $('#task_list_datatable tbody').attr('id','prioritysort');
                    // $('#task_list_datatable tbody tr:odd').attr('id',data.name);
                    // $('#task_list_datatable tbody tr:even').attr('id',data.id);
                    $('#task_list_datatable tbody tr:odd').addClass("m-datatable__row m-datatable__row--odd");
                    $('#task_list_datatable tbody tr:even').addClass("m-datatable__row m-datatable__row--even");
                    $('#task_list_datatable td').addClass("m-datatable__cell");
                    $('#task_list_datatable input').addClass("form-control m-input");

                    $('#task_list_datatable tr').css('table-layout', 'fixed');
                });
            }

        });

        //$("#task_list_datatable_filter.dataTables_filter").append($("#project_list"));
    }

    $('body').on('click', '.deletetask', function(e) {
        
        var id = $(this).data("id");
        var token = $("meta[name='csrf-token']").attr("content");
        
        if (confirm("Are you sure?")) {
            
            var responseUrl  = "/task/"+$(this).data("id");
            $.ajax({
                url: responseUrl,
                type: 'DELETE',
                data: {
                    "id": id,
                    "_token": token,
                },
                success: function(data) {
                    swal({title: "Success!", text: data['message'], type: 
                        "success"}).then(function(){ 
                            $.TaskList();
                        //location.reload();
                        }
                    );
                },error: function(data) {
                    swal("Error!", 'Something went wrong', "error");
                }
            });
        }
    });

    
    });
 };

 $.TaskList();
 

 $( "#project_list" ).change(function() {
     $.TaskList();
 });
