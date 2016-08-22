@extends('um::layouts.um')
@section('content')
    Show Permission
    <?php
    echo "<pre>";
    print_r($permission->toArray());
    echo "</pre>";
    ?>
@stop