<h3>Edit User: {{$user->name}}</h3>
<div class="um-form-container">
    @include('um::forms.user.edit')
</div>
<script type="text/javascript">
    $(function () {
        $(".um-form-container select").select2();
    })
</script>
