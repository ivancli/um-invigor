<link rel="stylesheet" href="{{asset('css/main.css')}}">
@yield('styles')
<style>
    .required label:after {
        content: " *";
        color: #ff0000;
    }
</style>
<div class="container">
    @yield('content')
</div>


<script type="text/javascript" src="{{asset('js/main.js')}}"></script>
@yield('scripts')