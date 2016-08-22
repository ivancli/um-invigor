@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::open(array('url' => 'um/role', 'method'=>'post')) !!}
@include('um::partial_forms.role')
{!! Form::submit('Create', ["class"=>"btn btn-default btn-sm"]) !!}
<a href="{{url('um/role')}}" class="btn btn-default btn-sm">Cancel</a>
{!! Form::close() !!}