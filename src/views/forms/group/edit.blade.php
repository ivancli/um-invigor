<div class="alert alert-info" role="alert">
    <strong>{{count($group->users)}}</strong> {{str_plural('user', count($group->users))}} in this group.
</div>
@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($group, array('route' => array('um.group.update', $group->id), 'method'=>'put')) !!}
@include('um::partial_forms.group')
<div class="text-right">
    {!! Form::submit('Save', ["class"=>"btn btn-primary btn-sm"]) !!}
    <a href="{{route('um.group.index')}}" class="btn btn-default btn-sm">Cancel</a>
</div>
{!! Form::close() !!}