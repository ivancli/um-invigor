@extends('um::layouts.um')
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <table id="tbl-groups" class="table table-bordered table-hover table-striped">
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
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var tblGroups;
        $(function () {
            jQuery.fn.dataTable.Api.register( 'processing()', function ( show ) {
                return this.iterator( 'table', function ( ctx ) {
                    ctx.oApi._fnProcessingDisplay( ctx, show );
                } );
            } );
            tblGroups = $("#tbl-groups").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'<'link-create'>><'col-sm-7'p>>",
                "ajax": "{{route('um.group.index')}}",
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
                        "name": "active",
                        "data": "active"
                    },
                    {
                        "name": "website",
                        "data": "website"
                    },
                    {
                        "name": "description",
                        "data": "description"
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
                                        "href": data.urls.show
                                    }).addClass("text-muted").append(
                                            $("<i>").addClass("glyphicon glyphicon-search")
                                    ),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": data.urls.edit
                                    }).addClass("text-muted").append(
                                            $("<i>").addClass("glyphicon glyphicon-pencil")
                                    ),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#",
                                        "onclick": "deleteGroup('" + data.urls.delete + "')"
                                    }).addClass('text-danger').append(
                                            $("<i>").addClass("glyphicon glyphicon-trash")
                                    )
                            ).html();
                        }
                    }
                ]
            });
            $("div.link-create").append(
                    $("<a>").attr({
                        "href": "{{route('um.group.create')}}"
                    }).addClass('btn btn-default').text("Create Group")
            )
        });

        function deleteGroup(url, callback) {
            if (confirm("Do you want to delete this group?")) {
                tblGroups.processing(true);
                $.ajax({
                    "url": url,
                    "type": "delete",
                    "data": {
                        "_token": "{!! csrf_token() !!}"
                    },
                    'cache': false,
                    'dataType': "json",
                    "success": function (response) {
                        tblGroups.processing(false);
                        if ($.isFunction(callback)) {
                            callback(response);
                        }
                        tblGroups.ajax.reload(null, false);
                    },
                    "error": function () {
                        alert("Unable to delete group, please try again later.");
                    }
                })
            }
        }
    </script>
@stop