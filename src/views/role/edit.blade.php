@extends('um::layouts.um')
@section('content')
    <h3>Edit Role: {{$role->name}}</h3>
    @include('um::forms.role.edit')
@stop