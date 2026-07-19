@extends('layouts.app')

@section('title', 'Manajemen Kategori')

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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 2rem;">Manajemen Kategori Produk</h1>
                <p style="color: var(--text-muted); margin-top: 0.25rem;">Atur kategori katalog produk untuk mempermudah navigasi pembeli.</p>
            </div>
            
            <div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm"><i data-lucide="plus"></i> Tambah Kategori</a>
            </div>
        </div>

        <div class="glass-card" style="padding: 1.5rem; hover: none;">
            @if($categories->isEmpty())
                <p style="color: var(--text-muted); text-align: center; padding: 4rem 2rem;">Belum ada kategori yang dibuat.</p>
            @else
                <div class="table-responsive" style="margin-top: 0; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center;">ID</th>
                                <th>Nama Kategori</th>
                                <th>Slug</th>
                                <th style="text-align: center; width: 150px;">Jumlah Produk</th>
                                <th style="text-align: center; width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td style="text-align: center;">{{ $category->id }}</td>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td><code>{{ $category->slug }}</code></td>
                                <td style="text-align: center;">
                                    <span class="badge badge-processing">{{ $category->products_count }} produk</span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem;"><i data-lucide="edit-2" style="width: 14px; height: 14px;"></i></a>
                                    
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display: inline;" onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem;" {{ $category->products_count > 0 ? 'disabled style=opacity:0.5;cursor:not-allowed title=Kategori_ini_sedang_digunakan' : '' }}><i data-lucide="trash-2" style="width: 14px; height: 14px;"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: center;">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
