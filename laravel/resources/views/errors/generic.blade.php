<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status }} · KHO - CA</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #111827;
            background: #f7f7f5;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; }
        .error-card {
            width: min(100%, 620px);
            padding: clamp(32px, 7vw, 64px);
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 20px 60px rgba(17, 24, 39, .08);
            text-align: center;
        }
        .brand { margin-bottom: 28px; color: #27578e; font-size: 24px; font-weight: 800; letter-spacing: .14em; }
        .status { margin: 0; color: #d8dee8; font-size: clamp(64px, 15vw, 116px); font-weight: 800; line-height: .95; letter-spacing: -.06em; }
        h1 { margin: 24px 0 10px; font-size: clamp(22px, 4vw, 30px); line-height: 1.2; }
        p { margin: 0 auto; max-width: 470px; color: #64748b; font-size: 16px; line-height: 1.7; }
        .actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; margin-top: 30px; }
        .button {
            display: inline-flex; align-items: center; justify-content: center; min-height: 44px;
            padding: 0 20px; border-radius: 999px; text-decoration: none; font-size: 14px; font-weight: 700;
        }
        .button-primary { color: #fff; background: #111827; }
        .button-secondary { color: #334155; border: 1px solid #dbe2ea; background: #fff; }
        .hint { margin-top: 24px; color: #94a3b8; font-size: 12px; }
    </style>
</head>
<body>
    @php
        $currentUrl = url()->current();
        $backUrl = url()->previous();
        if ($backUrl === $currentUrl) {
            $backUrl = $isAdminRequest ? route('login') : route('home');
        }
    @endphp

    <main class="error-card" role="alert">
        <div class="brand">KHO - CA</div>
        <div class="status">{{ $status }}</div>
        <h1>Không thể hoàn tất yêu cầu</h1>
        <p>{{ $message }}</p>

        <div class="actions">
            <a class="button button-primary" href="{{ $backUrl }}">Quay lại</a>
            @if ($isAdminRequest)
                <a class="button button-secondary" href="{{ route('login') }}">Đăng nhập quản trị</a>
            @else
                <a class="button button-secondary" href="{{ route('home') }}">Về trang chủ</a>
            @endif
        </div>

        <div class="hint">Nếu lỗi tiếp tục xảy ra, hãy thử tải lại trang hoặc liên hệ quản trị viên.</div>
    </main>
</body>
</html>
