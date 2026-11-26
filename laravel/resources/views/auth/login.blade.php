@extends('layouts.app')

@section('title', 'Đăng nhập quản trị · Trưng bày Sách')

@section('content')
    <div class="auth-page">
        <form class="auth-card" action="{{ route('login.store') }}" method="post">
            @csrf
            <span class="eyebrow">KHU VỰC QUẢN TRỊ</span>
            <h1>Đăng nhập</h1>
            <p>Đăng nhập để quản lý danh mục và dữ liệu sách.</p>
            
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror

            <label>
                Email
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@domain.com" required autofocus>
            </label>

            <label>
                Mật khẩu
                <input type="password" name="password" placeholder="••••••••" required>
            </label>

            <label class="checkbox">
                <input type="checkbox" name="remember" value="1"> Duy trì đăng nhập
            </label>

            <button class="button" type="submit">Đăng nhập</button>
        </form>
    </div>
@endsection

