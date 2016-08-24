@extends('um::layouts.um')

@section('content')
    <h3>View User:</h3>
    <div class="alert alert-info" role="alert">
        <strong>{{count($user->roles)}}</strong> {{str_plural('role', count($user->roles))}} assigned to this user.
    </div>
    <table class="table table-bordered table-hover table-striped">
        <tbody>
        @foreach($user->toArray() as $field=>$value)
            @if(!is_array($value))
                <tr>
                    <th>
                        {{$field}}
                    </th>
                    <td>
                        {{$value}}
                    </td>
                </tr>
            @endif
        @endforeach
        <tr>
            <th>Roles</th>
            <td>
                @foreach($user->roles as $index=>$role)
                    <a href="{{$role->urls['show']}}">{{$role->display_name}}</a>
                    @if($index!=count($user->roles) - 1)
                        ,
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th>Groups</th>
            <td>
                @foreach($user->groups as $index=>$group)
                    <a href="{{$group->urls['show']}}">{{$group->name}}</a>
                    @if($index!=count($user->groups) - 1)
                        ,
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>
@stop
