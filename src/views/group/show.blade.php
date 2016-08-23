@extends('um::layouts.um')
@section('content')
    <h3>Show Group</h3>
    <p>{{count($group->users)}} {{str_plural('user', count($group->users))}} in this group.</p>
    <table class="table table-bordered table-hover table-striped">
        <tbody>
        <tr>
            <th>ID</th>
            <td>{{$group->id}}</td>
        </tr>
        <tr>
            <th>Name</th>
            <td>{{$group->name}}</td>
        </tr>
        <tr>
            <th>Is active</th>
            <td>{{$group->active == 1 ? "Yes" : "No"}}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{$group->description}}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{$group->created_at}}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{$group->updated_at}}</td>
        </tr>
        <tr>
            <th>Users</th>
            <td>
                @foreach($group->users as $index=>$user)
                    <a href="{{route("um.user.show", $user->id)}}">{{$user->name}}</a>
                    @if($index != count($group->users)-1)
                        ,
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>
@stop