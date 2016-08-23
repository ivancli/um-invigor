@extends('um::layouts.um')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <form action="{{url("um/user")}}" method="get" class="form-inline">
                        <label for="txt-search">Name/email</label>&nbsp;
                        <input type="text" class="form-control" id="txt-search" name="search" value="">
                        <button class="btn btn-default btn-sm">Search</button>
                    </form>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{url("um/user/create")}}" class="btn btn-default btn-sm">Create new user</a>
                </div>
            </div>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th width="10%"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{$user->id}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->created_at}}</td>
                        <td>{{$user->updated_at}}</td>
                        <td class="text-center">
                            <a href="{{url("um/user/{$user->id}/edit")}}" class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>
                            {!! Form::model($user, array('url' => array('um/user', $user->id), 'method'=>'delete', 'style'=>'display:inline-block', "onsubmit"=>"return confirm('Do you want to delete this user?')")) !!}
                            {!! Form::hidden('id', null, array('class' => 'form-control')) !!}
                            <button class="btn btn-default btn-sm"><i class="glyphicon glyphicon-remove"></i></button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-right">
                {{$users->appends(Request::only('search'))->links()}}
            </div>
        </div>
    </div>
@stop