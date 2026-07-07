@extends('layouts.app')

@section('title', 'Moderasi Ulasan Produk')

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
                <a href="{{ route('admin.reviews') }}" class="sidebar-link active">
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
            <h1 style="font-size: 2rem;">Moderasi Ulasan & Rating</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Pantau ulasan dari pembeli. Sembunyikan ulasan yang mengandung unsur kata kotor, spam, atau SARA.</p>
        </div>

        <div class="glass-card" style="padding: 1.5rem; hover: none;">
            @if($reviews->isEmpty())
                <p style="color: var(--text-muted); text-align: center; padding: 3rem 2rem;">Belum ada ulasan produk dalam sistem.</p>
            @else
                <div class="table-responsive" style="margin-top: 0; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Pembeli</th>
                                <th>Produk & Toko</th>
                                <th style="text-align: center;">Rating</th>
                                <th>Komentar Ulasan</th>
                                <th style="text-align: center;">Status Moderasi</th>
                                <th style="text-align: center;">Aksi Kontrol</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $rev)
                            <tr>
                                <td><strong>{{ $rev->user->name }}</strong></td>
                                <td>
                                    <strong>{{ $rev->product->name }}</strong><br>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">Toko: {{ $rev->product->store->name }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <div style="color: #fbbf24; display: flex; align-items: center; justify-content: center; gap: 0.1rem;">
                                        @for($i=1; $i<=5; $i++)
                                            <i data-lucide="star" style="width: 12px; height: 12px; fill: {{ $i <= $rev->rating ? '#fbbf24' : 'none' }}; stroke: {{ $i <= $rev->rating ? '#fbbf24' : '#cbd5e1' }};"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td style="max-width: 250px;">
                                    {{ $rev->comment ?: '-' }}
                                    @if($rev->moderation_notes)
                                        <div style="font-size: 0.75rem; color: var(--status-cancelled); background: #fee2e2; padding: 0.25rem; border-radius: 4px; margin-top: 0.25rem;">
                                            Catatan: {{ $rev->moderation_notes }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($rev->is_moderated)
                                        <span class="badge badge-cancelled">Disembunyikan</span>
                                    @else
                                        <span class="badge badge-completed">Aktif (Tampil)</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <form action="{{ route('admin.moderateReview', $rev->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 0.5rem; width: 160px; margin: 0 auto;" onsubmit="return confirmAction(event, 'Lakukan tindakan moderasi pada ulasan ini?')">
                                        @csrf
                                        <input type="text" name="moderation_notes" class="form-control" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" placeholder="Catatan alasan..." value="{{ $rev->moderation_notes }}">
                                        
                                        @if($rev->is_moderated)
                                            <input type="hidden" name="is_moderated" value="0">
                                            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Tampilkan Ulasan</button>
                                        @else
                                            <input type="hidden" name="is_moderated" value="1">
                                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Sembunyikan Ulasan</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: center;">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
