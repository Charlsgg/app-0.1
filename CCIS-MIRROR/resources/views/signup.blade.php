<div 
    id="app"
    data-page="signup"
    data-user='@json(Auth::user())'>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/app.css', 'resources/js/app.ts'])