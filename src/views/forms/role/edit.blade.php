@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($role, array('url' => array('um/role', $role->id), 'method'=>'put')) !!}
@include('um::partial_forms.role')
{!! Form::submit('Save', ["class"=>"btn btn-default btn-sm"]) !!}
<a href="{{url('um/role')}}" class="btn btn-default btn-sm">Cancel</a>
{!! Form::close() !!}