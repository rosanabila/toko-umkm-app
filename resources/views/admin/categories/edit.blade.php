@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">
            <i data-lucide="shield-check" style="color: var(--primary);"></i>
            <span>Admin Panel</span>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                    <i data-lucide="layout-dashboard"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users') }}" class="sidebar-link">
                    <i data-lucide="users"></i> Pengguna & Role
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reviews') }}" class="sidebar-link">
                    <i data-lucide="message-square"></i> Moderasi Ulasan
                </a>
            </li>
            <li>
                <a href="{{ route('admin.returns') }}" class="sidebar-link">
                    <i data-lucide="refresh-cw"></i> Retur Barang
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link active">
                    <i data-lucide="grid"></i> Kelola Kategori
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 2rem;">Edit Kategori</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Ubah nama kategori produk UMKM.</p>
        </div>

        <div class="glass-card" style="max-width: 600px; hover: none; padding: 1.5rem;">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div style="background-color: var(--status-cancelled-light); color: var(--status-cancelled); padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-size: 0.9rem;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="form-group">
                    <label for="name" class="form-label">Nama Kategori</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Elektronik, Kerajinan Tangan" value="{{ old('name', $category->name) }}" required>
                    <div id="name-error" style="color: var(--status-cancelled); font-size: 0.8rem; margin-top: 0.25rem; display: none;"></div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Perbarui Kategori</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nameInput = document.getElementById('name');
        const nameError = document.getElementById('name-error');
        const form = document.querySelector('form');

        function validateName() {
            const val = nameInput.value.trim();
            if (val.length < 3) {
                nameError.innerText = 'Nama kategori minimal harus terdiri dari 3 karakter.';
                nameError.style.display = 'block';
                return false;
            } else {
                nameError.style.display = 'none';
                return true;
            }
        }

        nameInput.addEventListener('input', validateName);

        form.addEventListener('submit', (e) => {
            const isValid = validateName();
            if (!isValid) {
                e.preventDefault();
                alert('Silakan perbaiki kesalahan validasi sebelum memperbarui kategori.');
            }
        });
    });
</script>
@endsection
