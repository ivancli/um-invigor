<div class="alert alert-info" role="alert">
    <strong>{{count($role->users)}}</strong> {{str_plural('user', count($role->users))}} in this role.
</div>
@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($role, array('route' => array('um.role.update', $role->id), 'method'=>'put')) !!}
@include('um::partial_forms.role')
<div class="text-right">
    {!! Form::submit('Save', ["class"=>"btn btn-primary btn-sm"]) !!}
    <a href="{{route('um.role.index')}}" class="btn btn-default btn-sm">Cancel</a>
</div>
{!! Form::close() !!}