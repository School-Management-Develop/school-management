<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel=icon href="{{ asset('assets/img/image.png') }}" type="image/png">

    <style>
        :root {
            --bg: #f8fafc;
            --card: rgba(255, 255, 255, 0.92);
            --text: #0f172a;
            --muted: #64748b;
            --border: #dbe2ea;
            --accent: #4f46e5;
            --accent-soft: rgba(79, 70, 229, 0.10);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: "Inter", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(99, 102, 241, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.10), transparent 24%),
                linear-gradient(135deg, #f8fafc, #eef2ff, #f8fafc);
        }

        .auth-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 620px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 28px;
            overflow: hidden;
            background: var(--card);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.10);
        }

        .backdrop-blur {
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .auth-body {
            padding: 42px 42px 0 42px;
        }

        .small-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #e0e7ff;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .logo-img {
            width: 78px;
            height: 78px;
            object-fit: cover;
            border-radius: 18px;
            border: 4px solid #fff;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
            margin-bottom: 16px;
        }

        .form-title {
            font-size: 2.35rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
            line-height: 1.1;
        }

        .form-subtitle {
            color: var(--muted);
            font-size: 1rem;
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            font-size: 0.98rem;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 20px;
        }

        .input-wrap .left-icon {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            z-index: 3;
        }

        .input-wrap .toggle-password {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 1rem;
            z-index: 3;
            cursor: pointer;
            padding: 4px;
        }

        .form-control {
            height: 58px;
            border-radius: 17px;
            border: 1.5px solid var(--border);
            padding-left: 48px;
            padding-right: 48px;
            font-size: 1rem;
            box-shadow: none !important;
            transition: all 0.2s ease;
            background: rgba(241, 245, 249, 0.95);
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-soft) !important;
            background: #fff;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            /* margin-bottom: 24px; */
            padding: 0 6.5% 6.5% 6.5%;
        }

        .meta-link {
            color: var(--accent);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
        }

        .meta-link:hover {
            text-decoration: underline;
        }

        .muted-text {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .btn-login {
            height: 58px;
            border: none;
            border-radius: 17px;
            /* background: linear-gradient(135deg, #065f46, #10b981); */
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.22s ease;
            box-shadow: 0 10px 18px rgba(15, 23, 42, 0.12);
        }

        .btn-login:hover {
            transform: translateY(-1px);
            opacity: 0.98;
        }

        .bottom-note {
            margin-top: 22px;
            text-align: center;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .alert {
            border-radius: 14px;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .auth-card {
                border-radius: 22px;
            }

            .auth-body {
                padding: 28px 20px 32px;
            }

            .form-title {
                font-size: 1.9rem;
            }

            .form-subtitle {
                font-size: 0.95rem;
            }

            .form-control,
            .btn-login {
                height: 54px;
                border-radius: 15px;
            }

            .logo-img {
                width: 72px;
                height: 72px;
            }
        }

        /* Layout */
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
        }

        .login-box {
            width: 100%;
            /* margin: 100px auto; */
            text-align: center;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 12px;
            /* background: #4f46e5; */
            background: linear-gradient(135deg, #065f46, #22c55e);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-login:hover {
            /* background: #4338ca; */
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.35);
            filter: brightness(1.05);
        }

        /* Forgot link */
        .forgot {
            margin-top: 15px;
        }

        .forgot a {
            text-decoration: none;
            color: #6b7280;
            font-size: 14px;
        }

        .forgot a:hover {
            color: #4f46e5;
        }

        /* Modal background */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s ease;
        }

        /* Show modal */
        .modal.show {
            opacity: 1;
            visibility: visible;
        }

        /* Modal box */
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 15px;
            width: 380px;
            text-align: center;
            transform: translateY(40px) scale(0.95);
            opacity: 0;
            transition: 0.3s ease;
        }

        /* Animation */
        .modal.show .modal-content {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        /* Close button */
        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 22px;
            cursor: pointer;
        }

        /* Admin list */
        .admin-list {
            list-style: none;
            padding: 0;
            /* margin-top: 15px; */
        }

        .admin-list li {
            margin: 10px 0;
        }

        .admin-list a {
            display: block;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 8px;
            text-decoration: none;
            color: #111;
            transition: 0.2s;
        }

        .admin-list a:hover {
            background: #e0e7ff;
        }

        .contact {
            color: #0d00ff;
            /* font-weight: 600; */
        }

        .contact:hover {
            color: #4f46e5;
        }

        /* Soft modern look */
        .bi-telegram {
            background: #0088cc;
            color: #fff;
            padding: 8px;
            border-radius: 50%;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .admin-list a {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-list a:hover .bi-telegram {
            transform: scale(1.15);
            background: #0ea5e9;
        }

        /* Container spacing */
        .modal-content {
            text-align: center;
            padding: 25px 20px;
        }

        /* Title */
        .modal-content h3 {
            font-size: 22px;
            font-weight: 600;
            color: #111827;
            /* margin-bottom: 8px; */
            letter-spacing: 0.3px;
        }

        /* Subtitle */
        .modal-content p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* Highlight Telegram text */
        .modal-content p span {
            color: #0088cc;
            font-weight: 500;
        }

        /* Animated underline */
        .modal-content h3::after {
            content: "";
            display: block;
            width: 0;
            height: 3px;
            margin: 10px auto 0;
            background: linear-gradient(90deg, #4f46e5, #22c55e);
            border-radius: 2px;
            transition: width 0.4s ease;
        }

        /* Animate when modal opens */
        .modal.show .modal-content h3::after {
            width: 50px;
        }

        /* Fade + slide text */
        .modal-content h3,
        .modal-content p {
            opacity: 0;
            transform: translateY(10px);
            transition: 0.4s ease;
        }

        .modal.show .modal-content h3 {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.1s;
        }

        .modal.show .modal-content p {
            opacity: 1;
            /* transform: translateY(0); */
            transition-delay: 0.2s;
        }
    </style>
</head>

<body>
    <div class="container auth-section">
        <div class="auth-card">
            <div class="auth-body">

                <div class="mb-2">
                    <img src="{{ asset('assets/img/image.png') }}" alt="logo" class="logo-img">
                </div>

                <div class="form-title">IT Department</div>
                <div class="form-subtitle">Please sign in to access the System </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Username or Email</label>
                        <div class="input-wrap">
                            <i class="bi bi-person left-icon"></i>
                            <input type="text" name="login" class="form-control"
                                placeholder="Enter username or email" value="{{ old('login') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-wrap">
                            <i class="bi bi-lock left-icon"></i>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="Enter your password" required>
                            <button type="button" class="toggle-password" id="togglePassword">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="login-box">

                        <!-- Login Button -->
                        <button type="submit" class="btn-login">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Login to Dashboard
                        </button>

                        <!-- Forgot Password -->
                        <p class="forgot">
                            <a href="#" id="openAdmin">Forgot password? <span class="contact">Contact
                                    Admin</span></a>
                        </p>

                    </div>

                </form>

                {{-- <div class="bottom-note">
                    Protected admin access with secure session handling
                </div> --}}
            </div>
            <div class="meta-row">
                {{-- <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label muted-text" for="remember">
                        Remember me
                    </label>
                </div> --}}


                <!-- Admin Popup -->
            </div>
        </div>
    </div>

    <div class="modal" id="adminModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Contact Admin</h3>
            <p>
                Please message one of our <span>admins on Telegram</span>
                for password recovery
            </p>
            {{-- Replace the hardcoded <ul class="admin-list"> --}}
            <ul class="admin-list">
                @foreach (\App\Models\User::whereNotNull('telegram_username')->where('status', 1)->get() as $u)
                    <li>
                        <a href="https://t.me/{{ $u->telegram_username }}" target="_blank">
                            <i class="bi bi-telegram"></i>{{ $u->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            this.innerHTML = type === 'password' ?
                '<i class="bi bi-eye-slash"></i>' :
                '<i class="bi bi-eye"></i>';
        });

        const openBtn = document.getElementById("openAdmin");
        const modal = document.getElementById("adminModal");
        const closeBtn = document.querySelector(".close");
        const auth = document.querySelector(".auth-section");
        // Open
        openBtn.onclick = (e) => {
            e.preventDefault();
            modal.classList.add("show");
            document.body.classList.add("backdrop-blur"); // Optional: prevent background scroll when modal is open

        };

        // Close (X)
        closeBtn.onclick = () => {
            modal.classList.remove("show");
            document.body.classList.remove("backdrop-blur"); // Restore background when modal is closed
            // Restore opacity when modal is closed
        };

        // Close (click outside)
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.classList.remove("show");
            }
        };

        // Close (ESC key)
        document.onkeydown = (e) => {
            if (e.key === "Escape") {
                modal.classList.remove("show");
            }
        };
    </script>
</body>

</html>
