<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KampusLMS')</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 860px; margin: 24px auto; padding: 0 16px; color: #0F172A; }
        nav { display: flex; justify-content: space-between; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border-bottom: 1px solid #E2E8F0; padding: 8px; text-align: left; }
        label { display: block; margin-top: 12px; font-weight: 600; }
        input, textarea, select { width: 100%; padding: 8px; box-sizing: border-box; }
        input[type=checkbox] { width: auto; }
        button, .btn { padding: 8px 14px; cursor: pointer; }
        .ok { background: #EBF9F1; color: #1B8A5A; padding: 8px 12px; border-radius: 8px; }
        .err { background: #FDECEC; color: #B42318; padding: 8px 12px; border-radius: 8px; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route(auth()->user()->dashboardRoute()) }}">&larr; Dashboard</a>
        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Logout</button></form>
    </nav>
    @if(session('success'))<p class="ok">{{ session('success') }}</p>@endif
    @if(session('error'))<p class="err">{{ session('error') }}</p>@endif
    @if($errors->any())<div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
    @yield('content')
</body>
</html>
