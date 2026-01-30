<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - {{ $settings['title'] ?? 'Xổ số 3 miền' }}</title>
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
            margin-bottom: 18px;
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
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            margin-top: 2px;
            flex-shrink: 0;
            accent-color: #c90205;
        }

        .form-check label {
            color: #666;
            font-size: 13px;
            cursor: pointer;
            line-height: 1.5;
        }

        .form-check a {
            color: #c90205;
            text-decoration: none;
        }

        .form-check a:hover {
            text-decoration: underline;
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

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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

        .password-hint {
            color: #888;
            font-size: 12px;
            margin-top: 5px;
        }

        .password-strength {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { width: 33%; background: #dc3545; }
        .strength-medium { width: 66%; background: #f5a623; }
        .strength-strong { width: 100%; background: #16a34a; }
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
                <h1>Đăng ký tài khoản</h1>
                <p>Tạo tài khoản để theo dõi kết quả xổ số</p>
            </div>

            <div class="auth-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <div class="form-group">
                        <label for="name">Họ và tên</label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Nhập họ và tên"
                               required>
                    </div>

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
                               placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
                               required>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <p class="password-hint">Mật khẩu phải có ít nhất 6 ký tự</p>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Xác nhận mật khẩu</label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="form-control"
                               placeholder="Nhập lại mật khẩu"
                               required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">
                            Tôi đồng ý với <a href="/dieu-khoan-su-dung.html" target="_blank">Điều khoản sử dụng</a>
                            và <a href="/chinh-sach-bao-mat.html" target="_blank">Chính sách bảo mật</a>
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">Đăng ký</button>
                </form>
            </div>

            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');

            strengthBar.className = 'password-strength-bar';

            if (password.length === 0) {
                strengthBar.style.width = '0';
            } else if (password.length < 6) {
                strengthBar.classList.add('strength-weak');
            } else if (password.length < 10 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
    </script>
</body>
</html>
