@extends('um::layouts.um')
@section('content')
    <h3>Edit User: {{$user->name}}</h3>
    @include('um::forms.user.edit')
@stop