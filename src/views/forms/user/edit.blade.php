@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($user, array('url' => array('um/user', $user->id), 'method'=>'put')) !!}
@include('um::partial_forms.user')
{!! Form::submit('Save', ["class"=>"btn btn-default btn-sm"]) !!}
<a href="{{url('um/user')}}" class="btn btn-default btn-sm">Cancel</a>
{!! Form::close() !!}