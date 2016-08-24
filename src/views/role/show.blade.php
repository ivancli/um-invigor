@extends('um::layouts.um')
@section('content')
    <h3>View Role:</h3>
    <div class="alert alert-info" role="alert">
        <strong>{{count($role->users)}}</strong> {{str_plural('user', count($role->users))}} with this role.
    </div>
    <table class="table table-bordered table-hover table-striped">
        <tbody>
        <tr>
            <th>ID</th>
            <td>{{$role->id}}</td>
        </tr>
        <tr>
            <th>Name</th>
            <td>{{$role->name}}</td>
        </tr>
        <tr>
            <th>Display Name</th>
            <td>{{$role->display_name}}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{$role->description}}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{$role->created_at}}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{$role->updated_at}}</td>
        </tr>
        <tr>
            <th>Permissions</th>
            <td>
                @foreach($role->perms as $index=>$permission)
                    <a href="{{$permission->urls['show']}}">{{$permission->display_name}}</a>
                    @if($index != count($role->perms)-1)
                        ,
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>
@stop