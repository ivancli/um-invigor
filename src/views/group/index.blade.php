@extends('um::layouts.um')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <form action="{{route("um.group.index")}}" method="get" class="form-inline">
                        <label for="txt-search">Name/Display name</label>&nbsp;
                        <input type="text" class="form-control" id="txt-search" name="search" value="">
                        <button class="btn btn-default btn-sm">Search</button>
                    </form>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route("um.group.create")}}" class="btn btn-default btn-sm">Create new group</a>
                </div>
            </div>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Active</th>
                    <th>Website</th>
                    <th>Description</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th width="10%"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>{{$group->id}}</td>
                        <td>{{$group->name}}</td>
                        <td class="text-center">{{$group->active == 1 ? 'yes' : 'no'}}</td>
                        <td>{{$group->website}}</td>
                        <td>{{$group->description}}</td>
                        <td>{{$group->created_at}}</td>
                        <td>{{$group->updated_at}}</td>
                        <td class="text-center">
                            <a href="{{route("um.group.show", $group->id)}}" class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-search"></i>
                            </a>
                            <a href="{{route("um.group.edit", $group->id)}}" class="btn btn-default btn-sm">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>
                            {!! Form::model($group, array('route' => array('um.group.destroy', $group->id), 'method'=>'delete', 'style'=>'display:inline-block', "onsubmit"=>"return confirm('Do you want to delete this group?')")) !!}
                            {!! Form::hidden('id', null, array('class' => 'form-control')) !!}
                            <button class="btn btn-default btn-sm"><i class="glyphicon glyphicon-remove"></i></button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-right">
                {{$groups->appends(Request::only('search'))->links()}}
            </div>
        </div>
    </div>
@stop