<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - {{ $settings['title'] ?? 'Xổ số 3 miền' }}</title>
    <link rel="shortcut icon" href="{{ isset($settings['favicon']) && $settings['favicon'] != '' ? sourceSetting($settings['favicon']) : '/favicon.ico' }}" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
        }

        .auth-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .auth-header {
            background: #c90205;
            padding: 30px;
            text-align: center;
        }

        .auth-header .logo {
            display: inline-block;
            margin-bottom: 15px;
        }

        .auth-header .logo img {
            height: 50px;
            width: auto;
        }

        .auth-header h1 {
            color: #fff;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .auth-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-top: 8px;
        }

        .auth-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #c90205;
            box-shadow: 0 0 0 3px rgba(201, 2, 5, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #c90205;
        }

        .form-check label {
            color: #666;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #c90205;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #a80104;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(201, 2, 5, 0.3);
        }

        .auth-footer {
            text-align: center;
            padding: 20px 30px 30px;
            border-top: 1px solid #eee;
        }

        .auth-footer p {
            color: #666;
            font-size: 14px;
        }

        .auth-footer a {
            color: #c90205;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }

        .back-home:hover {
            color: #c90205;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc3545;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #999;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            padding: 0 15px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <a href="/" class="back-home">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Quay lại trang chủ
        </a>

        <div class="auth-card">
            <div class="auth-header">
                <a href="/" class="logo">
                    <img src="{{ isset($settings['logo']) && $settings['logo'] != '' ? sourceSetting($settings['logo']) : '/images/logo.svg' }}" alt="Logo">
                </a>
                <h1>Đăng nhập</h1>
                <p>Chào mừng bạn quay trở lại!</p>
            </div>

            <div class="auth-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="Nhập email của bạn"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Nhập mật khẩu"
                               required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ghi nhớ đăng nhập</label>
                    </div>

                    <button type="submit" class="btn-submit">Đăng nhập</button>
                </form>
            </div>

            <div class="auth-footer">
                <p>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>
</body>
</html>
