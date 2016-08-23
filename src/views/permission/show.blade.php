@extends('um::layouts.um')
@section('content')
    <h3>Show Permission</h3>
    <p>{{count($permission->roles)}} {{str_plural('role', count($permission->roles))}} with this permission.</p>
    <table class="table table-bordered table-hover table-striped">
        <tbody>
        <tr>
            <th>ID</th>
            <td>{{$permission->id}}</td>
        </tr>
        <tr>
            <th>Name</th>
            <td>{{$permission->name}}</td>
        </tr>
        <tr>
            <th>Display Name</th>
            <td>{{$permission->display_name}}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{$permission->description}}</td>
        </tr>
        <tr>
            <th>Parent Permission</th>
            <td>
                @if(!is_null($permission->parentPerm))
                    <a href="{{route("um.permission.show", $permission->parentPerm->id)}}">{{$permission->parentPerm->display_name}}</a>
                @endif
            </td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{$permission->created_at}}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{$permission->updated_at}}</td>
        </tr>
        <tr>
            <th>Roles</th>
            <td>
                <ul>
                    @foreach($permission->roles as $role)
                        <li>
                            <a href="{{route("um.role.show", $role->id)}}">{{$role->display_name}}</a>
                        </li>
                    @endforeach
                </ul>
            </td>
        </tr>
        </tbody>
    </table>
@stop