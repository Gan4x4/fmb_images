@section('page-css')

    <link rel="stylesheet" href="/tagsinput/css/tagsinput.css" type="text/css" />
<!-- 
    <link rel="stylesheet" href="/tagsinput/css/bootstrap-tagsinput-typeahead.css" type="text/css" />
    -->   
@endsection


{!! Form::bsText('name','Property name') !!}
{!! Form::bsText('description','Property description') !!}


<h3>Tags</h3>

{!! Form::bsText('new_tag','Find or add new tag') !!}


{!! Form::checklist('tags',$tags,$selected_tags) !!}


@section('page-js-script')

    <script src="/typeahead/bootstrap3-typeahead.min.js"></script>
    <script language="Javascript">
        jQuery(function($) {
            on_submit = false;
            $("input[name=new_tag]").typeahead({ 
                source:{!!  $tags->toJson() !!},
                updater:function (item) {
                    //var userInput = $("input[name=new_tag]").val();
                    // Check only full - matching items
                    //if (userInput == item.name){
                    
                        $("input[value="+item.id+"]").prop('checked',true);
                    
                    //}
                    //dont forget to return the item to reflect them into input
                    //return item;
                }
            });

        });
    </script>
    
@endsection





