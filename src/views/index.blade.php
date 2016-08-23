@extends('um::layouts.um')

@section('content')

    @if(Auth::check())
        <ul>
            <li>
                <a href="{{url('um/user')}}">View users</a>
            </li>
            <li>
                <a href="{{url('um/group')}}">View group</a>
            </li>
            <li>
                <a href="{{url('um/role')}}">View role</a>
            </li>
            <li>
                <a href="{{url('um/permission')}}">View permission</a>
            </li>
        </ul>
    @else
        @include('um::auths.login')
    @endif
@stop