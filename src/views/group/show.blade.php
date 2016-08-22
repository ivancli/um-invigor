@extends('um::layouts.um')
@section('content')
    Show Group
    <?php
    echo "<pre>";
    print_r($group->toArray());
    echo "</pre>";
    ?>
@stop