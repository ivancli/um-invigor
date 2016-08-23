{{--{{dump(Auth::user())}}--}}
{{--{{dump(Auth::user()->can('manage_user'))}}--}}
{{--{{dump(Auth::user()->can('manage_group'))}}--}}
{{--{{dump(Auth::user()->can('manage_role'))}}--}}
{{--{{dump(Auth::user()->can('manage_permission'))}}--}}


{{--<h3>Child user permission testing</h3>--}}
{{--create_user - {{dump(Auth::user()->can('create_user'))}}--}}
{{--read_user - {{dump(Auth::user()->can('read_user'))}}--}}
{{--update_user - {{dump(Auth::user()->can('update_user'))}}--}}
{{--delete_user - {{dump(Auth::user()->can('delete_user'))}}--}}

{{--<h3>Child permission permission testing</h3>--}}
{{--create_permission - {{dump(Auth::user()->can('create_permission'))}}--}}
{{--read_permission - {{dump(Auth::user()->can('read_permission'))}}--}}
{{--update_permission - {{dump(Auth::user()->can('update_permission'))}}--}}
{{--delete_permission - {{dump(Auth::user()->can('delete_permission'))}}--}}



<link rel="stylesheet" href="{{Config::get('um.bootstrap_css_path')}}">
<div class="container">
    @yield('content')
</div>