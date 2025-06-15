<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Absensi Siswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ... (CSS yang sudah ada, atau hapus jika pakai Tailwind) */
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .nav {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links a {
            margin-right: 15px;
            text-decoration: none;
            color: #007bff;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .user-info {
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="nav">
            <div class="nav-links">
                <a href="{{ route('absensi.index') }}">Daftar Siswa</a>
                @if (Auth::user()->role === 'admin')
                    <a href="{{ route('absensi.createSiswa') }}">Tambah Siswa</a>
                @endif
                @if (Auth::user()->role === 'guru' || Auth::user()->role === 'admin')
                    <a href="{{ route('absensi.absensiHarian') }}">Absensi Harian</a>
                @endif
                <a href="{{ route('absensi.rekapitulasi') }}">Rekapitulasi Absensi</a>
            </div>
            <div class="user-info">
                @auth
                    Halo, {{ Auth::user()->name }} ({{ Auth::user()->role }}) |
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit"
                            style="background:none; border:none; color:#007bff; cursor:pointer; padding:0;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>

</html>
