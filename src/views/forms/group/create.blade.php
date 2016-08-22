@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::open(array('url' => 'um/group', 'method'=>'post')) !!}
@include('um::partial_forms.group')
{!! Form::submit('Create', ["class"=>"btn btn-default btn-sm"]) !!}
<a href="{{url('um/group')}}" class="btn btn-default btn-sm">Cancel</a>
{!! Form::close() !!}