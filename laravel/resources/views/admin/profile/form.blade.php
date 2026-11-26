@extends('layouts.admin')
@section('heading', 'Quản lý tài khoản')
@section('content')
    <form class="admin-card form-card" method="post" action="{{ route('admin.profile.update') }}">
        @csrf

        <div class="form-header-bar">
            <div>
                <h2>Thông tin cá nhân & Đổi mật khẩu</h2>
                <p class="form-subtitle">Cập nhật họ tên, địa chỉ email quản trị và thay đổi mật khẩu đăng nhập.</p>
            </div>
        </div>

        <div class="form-sections">
            <!-- Section 1: Thông tin cá nhân -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">1</span>
                    <h3>Thông tin tài khoản</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Họ và tên <span class="req">*</span></label>
                        <input class="form-control" name="name" value="{{ old('name', $user->name) }}" placeholder="Ví dụ: Quản trị viên" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Địa chỉ Email đăng nhập <span class="req">*</span></label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="admin@example.com" required>
                    </div>
                </div>
            </div>

            <!-- Section 2: Đổi mật khẩu -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">2</span>
                    <h3>Đổi mật khẩu <span class="sub-label">(Để trống nếu không muốn đổi mật khẩu)</span></h3>
                </div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input class="form-control" type="password" name="current_password" placeholder="Nhập mật khẩu hiện tại của bạn">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mật khẩu mới</label>
                        <input class="form-control" type="password" name="new_password" placeholder="Ít nhất 6 ký tự">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input class="form-control" type="password" name="new_password_confirmation" placeholder="Nhập lại mật khẩu mới">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer-actions">
            <button class="admin-button" type="submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Lưu thông tin tài khoản
            </button>
        </div>
    </form>
@endsection
