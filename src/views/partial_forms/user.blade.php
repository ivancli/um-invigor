<div class="form-group">
    {!! Form::label('name', 'Name', array('class' => 'control-label')) !!}
    {!! Form::text('name', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('email', 'Email', array('class' => 'control-label')) !!}
    {!! Form::email('email', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('password', 'Password', array('class' => 'control-label')) !!}
    {!! Form::password('password', array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('password_confirmation', 'Confirm password', array('class' => 'control-label')) !!}
    {!! Form::password('password_confirmation', array('class' => 'form-control')) !!}
</div>
