{!! Form::model($user, array('url' => array('um/user', $user->id), 'method'=>'put')) !!}
{!! Form::token() !!}
@include('um::partial_forms.user')
{!! Form::submit('Save', ["class"=>"btn btn-default btn-sm"]) !!}
{!! Form::close() !!}