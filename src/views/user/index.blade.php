@extends('um::layouts.um')

@section('styles')
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <form action="{{route("um.user.index")}}" method="get" class="form-inline">
                        <label for="txt-search">Name/email</label>&nbsp;
                        <input type="text" class="form-control" id="txt-search" name="search" value="">
                        <button class="btn btn-default btn-sm">Search</button>
                    </form>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route("um.user.create")}}" class="btn btn-default btn-sm">Create new user</a>
                </div>
            </div>
            <table class="table table-bordered table-hover table-striped" id="tbl-users">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th width="15%"></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop


@section('scripts')
    <script type="text/javascript">
        var tblUsers = null;
        $(function () {
            jQuery.fn.dataTable.Api.register( 'processing()', function ( show ) {
                return this.iterator( 'table', function ( ctx ) {
                    ctx.oApi._fnProcessingDisplay( ctx, show );
                } );
            } );
            tblUsers = $("#tbl-users").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'><'col-sm-7'p>>",
                "ajax": "{{route('um.user.index')}}",
                "columns": [
                    {
                        "name": "id",
                        "data": "id"
                    },
                    {
                        "name": "name",
                        "data": "name"
                    },
                    {
                        "name": "email",
                        "data": "email"
                    },
                    {
                        "name": "created_at",
                        "data": "created_at"
                    },
                    {
                        "name": "updated_at",
                        "data": "updated_at"
                    },
                    {
                        "class": "text-center",
                        "sortable": false,
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.show_url
                                    }).addClass("text-muted").append(
                                            $("<i>").addClass("glyphicon glyphicon-search")
                                    ),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": data.edit_url
                                    }).addClass("text-muted").append(
                                            $("<i>").addClass("glyphicon glyphicon-pencil")
                                    ),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#",
                                        "onclick": "deleteUser('" + data.delete_url + "')"
                                    }).addClass('text-danger').append(
                                            $("<i>").addClass("glyphicon glyphicon-trash")
                                    )
                            ).html();
//                            return
                        }
                    }
                ]
            })
        });

        function deleteUser(url, callback) {
            if (confirm("Do you want to delete this user?")) {
                tblUsers.processing(true);
                $.ajax({
                    "url": url,
                    "type": "delete",
                    "data": {
                        "_token": "{!! csrf_token() !!}"
                    },
                    'cache': false,
                    'dataType': "json",
                    "success": function (response) {
                        tblUsers.processing(false);
                        if ($.isFunction(callback)) {
                            callback(response);
                        }
                        tblUsers.ajax.reload(null, false);
                    },
                    "error": function () {

                    }
                })
            }
        }
    </script>
@stop