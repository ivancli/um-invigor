<h3>Edit Permission: {{$permission->name}}</h3>
<div class="um-form-container">
    @include('um::forms.permission.edit')
</div>
<script type="text/javascript">
    $(function () {
        $(".um-form-container select").select2();
    })
</script>
