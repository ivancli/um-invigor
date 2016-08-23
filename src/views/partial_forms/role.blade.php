<div class="form-group required">
    {!! Form::label('name', 'Name', array('class' => 'control-label')) !!}
    {!! Form::text('name', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('display_name', 'Display name', array('class' => 'control-label')) !!}
    {!! Form::text('display_name', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('description', 'Description', array('class' => 'control-label')) !!}
    {!! Form::text('description', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('permission_id[]', 'Permissions', array('class' => 'control-label')) !!}
    {!! Form::select('permission_id[]', $permissions, isset($role) && !is_null($role->perms) ? $role->perms->pluck('id')->toArray() : null, ['class'=>'form-control', 'multiple' => 'multiple', 'size'=>10]) !!}
</div>
