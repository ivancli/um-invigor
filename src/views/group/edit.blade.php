@extends('um::layouts.um')
@section('content')
    <h3>Edit Group: {{$group->name}}</h3>
    @include('um::forms.group.edit')
@stop