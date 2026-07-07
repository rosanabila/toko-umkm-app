@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 4rem 2rem; background: radial-gradient(circle at top right, rgba(130, 90, 246, 0.05), transparent 40%), radial-gradient(circle at bottom left, rgba(95, 90, 246, 0.05), transparent 40%);">
    <div class="glass-card" style="width: 100%; max-width: 500px;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 2rem; margin-bottom: 0.5rem;" class="text-gradient">Gabung TokoKita</h2>
            <p style="color: var(--text-muted);">Dapatkan akses belanja produk lokal atau mulai berjualan hari ini!</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div style="background-color: var(--status-cancelled-light); color: var(--status-cancelled); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; font-size: 0.9rem;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Nomor Telepon (WhatsApp)</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Contoh: 08123456789" value="{{ old('phone') }}">
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Daftar Sebagai</label>
                <div style="display: flex; gap: 1.5rem; margin-top: 0.25rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="role" value="pembeli" style="accent-color: var(--primary); cursor: pointer;" checked>
                        <span>Pembeli Umum</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="radio" name="role" value="penjual" style="accent-color: var(--primary); cursor: pointer;">
                        <span>Pemilik UMKM (Penjual)</span>
                    </label>
                </div>
            </div>

            <div class="grid-2" style="margin-bottom: 0; gap: 1rem;">
                <div class="form-group">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Min. 6 karakter" required>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ketik ulang sandi" required>
                </div>
            </div>

            <button type="submit" class="btn btn-accent" style="width: 100%; margin-top: 1rem;">Daftar Akun Sekarang</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
            Sudah punya akun? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600; text-decoration: underline;">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
