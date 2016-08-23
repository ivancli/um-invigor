@extends('um::layouts.um')
@section('content')
    <h3>View Role:</h3>
    <p>{{count($role->users)}} {{str_plural('user', count($role->users))}} with this role.</p>
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
                <ul>
                    @foreach($role->perms as $permission)
                        <li>
                            <a href="{{route("um.permission.show", [$permission->id])}}">{{$permission->display_name}}</a>
                        </li>
                    @endforeach
                </ul>
            </td>
        </tr>
        </tbody>
    </table>
@stop