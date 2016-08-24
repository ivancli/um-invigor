@extends('um::layouts.um')
@section('content')
    <h3>Edit Role: {{$role->name}}</h3>
    <div class="um-form-container">
        @include('um::forms.role.edit')
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(function () {
            $(".um-form-container select").select2();
        })
    </script>
@stop()