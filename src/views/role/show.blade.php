@extends('um::layouts.um')
@section('content')
    Show Role
    <?php
    echo "<pre>";
    print_r($role->toArray());
    echo "</pre>";
    ?>
@stop