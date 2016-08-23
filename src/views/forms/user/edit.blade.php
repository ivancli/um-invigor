@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($user, array('route' => array('um.user.update', $user->id), 'method'=>'put')) !!}
@include('um::partial_forms.user')
<div class="text-right">
    {!! Form::submit('Save', ["class"=>"btn btn-primary btn-sm"]) !!}
    <a href="{{url('um/user')}}" class="btn btn-default btn-sm">Cancel</a>
</div>
{!! Form::close() !!}