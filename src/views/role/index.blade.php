@extends('um::layouts.um')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <form action="{{route("um.role.index")}}" method="get" class="form-inline">
                        <label for="txt-search">Name/Display name</label>&nbsp;
                        <input type="text" class="form-control" id="txt-search" name="search" value="">
                        <button class="btn btn-default btn-sm">Search</button>
                    </form>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route("um.role.create")}}" class="btn btn-default btn-sm">Create new role</a>
                </div>
            </div>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Display name</th>
                    <th>Description</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th width="15%"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{$role->id}}</td>
                        <td>{{$role->name}}</td>
                        <td>{{$role->display_name}}</td>
                        <td>{{$role->description}}</td>
                        <td>{{$role->created_at}}</td>
                        <td>{{$role->updated_at}}</td>
                        <td class="text-center">
                            <a href="{{route("um.role.show", [$role->id])}}" class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-search"></i>
                            </a>
                            <a href="{{route("um.role.edit", $role->id)}}" class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>
                            {!! Form::model($role, array('route' => array('um.role.destroy', $role->id), 'method'=>'delete', 'style'=>'display:inline-block', "onsubmit"=>"return confirm('Do you want to delete this role?')")) !!}
                            {!! Form::hidden('id', null, array('class' => 'form-control')) !!}
                            <button class="btn btn-default btn-sm"><i class="glyphicon glyphicon-remove"></i></button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-right">
                {{$roles->appends(Request::only('search'))->links()}}
            </div>
        </div>
    </div>
@stop
