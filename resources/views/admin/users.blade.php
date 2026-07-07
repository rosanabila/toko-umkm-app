@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

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
                <a href="{{ route('admin.users') }}" class="sidebar-link active">
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
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 2rem;">Manajemen Pengguna & Role</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Atur hak akses akun pengguna terdaftar (Admin, Penjual/UMKM, Pembeli Umum).</p>
        </div>

        <div class="glass-card" style="padding: 1.5rem; hover: none;">
            <div class="table-responsive" style="margin-top: 0; border: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th style="text-align: center;">Role Saat Ini</th>
                            <th style="text-align: center;">Ubah Akses Role</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?: '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge badge-{{ $user->role === 'admin' ? 'completed' : ($user->role === 'penjual' ? 'processing' : 'pending') }}">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($user->id === auth()->id())
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">Akun Anda sendiri</span>
                                @else
                                    <form action="{{ route('admin.updateUserRole', $user->id) }}" method="POST" style="display: flex; gap: 0.25rem; justify-content: center; align-items: center;" onsubmit="return confirmAction(event, 'Perbarui akses role pengguna ini?')">
                                        @csrf
                                        <select name="role" class="form-control" style="width: 120px; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                                            <option value="pembeli" {{ $user->role === 'pembeli' ? 'selected' : '' }}>Pembeli</option>
                                            <option value="penjual" {{ $user->role === 'penjual' ? 'selected' : '' }}>Penjual</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.6rem;"><i data-lucide="check" style="width: 12px; height: 12px;"></i></button>
                                    </form>
                                @endif
                            </td>
                            <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
