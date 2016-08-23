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
    {!! Form::label('role_id[]', 'Roles', array('class' => 'control-label')) !!}
    {!! Form::select('role_id[]', $roles, isset($permission) && !is_null($permission->roles) ? $permission->roles->pluck('id')->toArray() : null, ['class'=>'form-control', 'multiple' => 'multiple', 'size'=>10]) !!}
</div>

<div class="form-group">
    {!! Form::label('parent_id', 'Parent permission', array('class' => 'control-label')) !!}
    {!! Form::select('parent_id', $permissions, isset($permission) ? $permission->parent_id : null, ['class'=>'form-control']) !!}
</div>
