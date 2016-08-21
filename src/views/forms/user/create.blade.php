{!! Form::open(array('url' => 'um/user', 'method'=>'post')) !!}
{!! Form::token() !!}
@include('um::partial_forms.user')
{!! Form::submit('Create', ["class"=>"btn btn-default btn-sm"]) !!}
{!! Form::close() !!}