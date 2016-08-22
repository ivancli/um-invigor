<div class="form-group">
    {!! Form::label('name', 'Name', array('class' => 'control-label')) !!}
    {!! Form::text('name', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('active', 'Is active', array('class' => 'control-label')) !!}
    {!! Form::checkbox('active', '1', true, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('website', 'Website', array('class' => 'control-label')) !!}
    {!! Form::text('website', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('description', 'Description', array('class' => 'control-label')) !!}
    {!! Form::text('description', null, array('class' => 'form-control')) !!}
</div>
