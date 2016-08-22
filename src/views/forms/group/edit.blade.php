{!! Form::model($group, array('url' => array('um/group', $group->id), 'method'=>'put')) !!}
{!! Form::token() !!}
@include('um::partial_forms.group')
{!! Form::submit('Save', ["class"=>"btn btn-default btn-sm"]) !!}
{!! Form::close() !!}