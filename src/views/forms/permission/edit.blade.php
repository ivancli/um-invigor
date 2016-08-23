<div class="alert alert-info" role="alert">
    <strong>{{count($permission->roles)}}</strong> {{str_plural('role', count($permission->roles))}} with this
    permission.
</div>
@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($permission, array('route' => array('um.permission.update', $permission->id), 'method'=>'put')) !!}
@include('um::partial_forms.permission')
<div class="text-right">
    {!! Form::submit('Save', ["class"=>"btn btn-primary btn-sm"]) !!}
    <a href="{{route('um.permission.index')}}" class="btn btn-default btn-sm">Cancel</a>
</div>
{!! Form::close() !!}