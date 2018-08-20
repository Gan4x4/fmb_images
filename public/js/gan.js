function deleteEntity(path,title){
    if (confirm(title)){
        $.ajax({
            url: path,
            type: 'DELETE',  
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(result) {
                if (result.error === 0){
                    location.reload();
                    //alert(result.message);
                }else{
                    alert(result.message);
                }
            }
        });
    }
}







// https://stackoverflow.com/questions/11338774/serialize-form-data-to-json
function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}