@extends('um::layouts.um')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <form action="{{route("um.permission.index")}}" method="get" class="form-inline">
                        <label for="txt-search">Name/Display name</label>&nbsp;
                        <input type="text" class="form-control" id="txt-search" name="search" value="">
                        <button class="btn btn-default btn-sm">Search</button>
                    </form>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route("um.permission.create")}}" class="btn btn-default btn-sm">Create new permission</a>
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
                @foreach($permissions as $permission)
                    <tr>
                        <td>{{$permission->id}}</td>
                        <td>{{$permission->name}}</td>
                        <td>{{$permission->display_name}}</td>
                        <td>{{$permission->description}}</td>
                        <td>{{$permission->created_at}}</td>
                        <td>{{$permission->updated_at}}</td>
                        <td class="text-center">
                            <a href="{{route("um.permission.show", $permission->id)}}" class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-search"></i>
                            </a>
                            <a href="{{route("um.permission.edit", $permission->id)}}" class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>
                            {!! Form::model($permission, array('route' => array('um.permission.destroy', $permission->id), 'method'=>'delete', 'style'=>'display:inline-block', "onsubmit"=>"return confirm('Do you want to delete this permission?')")) !!}
                            {!! Form::hidden('id', null, array('class' => 'form-control')) !!}
                            <button class="btn btn-default btn-sm"><i class="glyphicon glyphicon-remove"></i></button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-right">
                {{$permissions->appends(Request::only('search'))->links()}}
            </div>
        </div>
    </div>
@stop