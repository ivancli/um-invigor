@extends('um::layouts.um')
@section('content')
    <h3>Edit Permission: {{$permission->name}}</h3>
    @include('um::forms.permission.edit')
@stop