@extends('um::layouts.um')

@section('content')
    Show User
    <?php
    echo "<pre>";
    print_r($user->toArray());
    echo "</pre>";
    ?>
@stop
