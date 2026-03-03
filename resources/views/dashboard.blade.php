<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Budget Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0d0d1a;
            color: #f1f1f7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
        }
        .icon { font-size: 3rem; margin-bottom: 8px; }
        h1 { font-size: 1.8rem; font-weight: 700; }
        p  { color: #9ca3af; }
        .alert {
            background: rgba(52,211,153,.12);
            border: 1px solid rgba(52,211,153,.25);
            color: #34d399;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: .9rem;
        }
        .btn {
            margin-top: 16px;
            padding: 12px 32px;
            background: linear-gradient(135deg,#6C63FF,#4f46e5);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: .95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        form { display: inline; }
    </style>
</head>
<body>
    <div class="icon">💸</div>
    <h1>Dashboard</h1>
    <p>Halaman dashboard (dummy placeholder)</p>

    @if(session('success'))
        <div class="alert"><i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>{{ session('success') }}</div>
    @endif

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn">
            <i class="fa-solid fa-arrow-right-from-bracket" style="margin-right:6px;"></i>Logout
        </button>
    </form>
</body>
</html>
