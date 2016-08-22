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
<div class="form-group">
    {!! Form::label('group_id[]', 'Groups', array('class' => 'control-label')) !!}
    {!! Form::select('group_id[]', $groups, isset($user) ? $user->groups->pluck('id')->toArray() : null, ['class'=>'form-control', 'multiple' => 'multiple', 'size'=>10]) !!}
</div>

<div class="form-group">
    {!! Form::label('role_id[]', 'Roles', array('class' => 'control-label')) !!}
    {!! Form::select('role_id[]', $roles, isset($user) ? $user->roles->pluck('id')->toArray() : null, ['class'=>'form-control', 'multiple' => 'multiple', 'size'=>10]) !!}
</div>
