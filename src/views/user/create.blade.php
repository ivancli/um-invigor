<h3>Create User</h3>
<div class="um-form-container">
    @include('um::forms.user.create')
</div>
<script type="text/javascript">
    $(function () {
        $(".um-form-container select").select2();
    })
</script>
