@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 4rem 2rem; background: radial-gradient(circle at top right, rgba(130, 90, 246, 0.05), transparent 40%), radial-gradient(circle at bottom left, rgba(95, 90, 246, 0.05), transparent 40%);">
    <div class="glass-card" style="width: 100%; max-width: 450px;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 2rem; margin-bottom: 0.5rem;" class="text-gradient">Selamat Datang</h2>
            <p style="color: var(--text-muted);">Silakan masuk untuk mengelola toko atau berbelanja produk UMKM.</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div style="background-color: var(--status-cancelled-light); color: var(--status-cancelled); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; font-size: 0.9rem;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="password" class="form-label">Kata Sandi</label>
                </div>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan kata sandi Anda" required>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                <input type="checkbox" name="remember" id="remember" style="accent-color: var(--primary); cursor: pointer;">
                <label for="remember" style="font-size: 0.85rem; color: var(--text-muted); cursor: pointer; user-select: none;">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk Sekarang</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
            Belum punya akun? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600; text-decoration: underline;">Daftar di sini</a>
        </div>
    </div>
</div>
@endsection
