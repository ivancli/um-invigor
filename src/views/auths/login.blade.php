{!! Form::open(array('url' => 'um/login', 'method'=>'post')) !!}
{!! Form::token() !!}
<div class="form-group">
    {!! Form::label('email', 'Email', array('class' => 'control-label')) !!}
    {!! Form::email('email', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('password', 'Password', array('class' => 'control-label')) !!}
    {!! Form::password('password', array('class' => 'form-control')) !!}
</div>
{!! Form::submit('Login', ["class"=>"btn btn-default btn-sm"]) !!}
{!! Form::close() !!}