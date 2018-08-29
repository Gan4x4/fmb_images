<div class='row'>
    {!! Form::label($name, $title); !!}
    
    @php
        if ($tag){
            $group = $tag->group;
            $tag_id = $tag->id;
        }
        else{
            // Default group
            $group = App\Group::getDefault();
            $tag_id = 0;
        }
        
        $parents = $group->getParents();
        $parents->add($group);
    @endphp
    
    @foreach($parents as $group)
        {{ $group->name }}
        {!! Form::select('group[]',APP\Controller::collection2select($group->getSiblings()),$group->id,['class'=>'form-control']); !!}
    @endforeach
    tag 
    {!! Form::select('tag',APP\Controller::collection2select($group->tags()),$tag_id,['class'=>'form-control']); !!}
    
</div>