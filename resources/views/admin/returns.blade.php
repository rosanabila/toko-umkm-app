@extends('layouts.app')

@section('title', 'Moderasi Komplain Retur')

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
                <a href="{{ route('admin.returns') }}" class="sidebar-link active">
                    <i data-lucide="refresh-cw"></i> Retur Barang
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 2rem;">Moderasi Komplain Retur Barang</h1>
            <p style="color: var(--text-muted); margin-top: 0.25rem;">Tinjau alasan komplain kerusakan barang, periksa bukti foto, dan setujui/tolak pengajuan retur.</p>
        </div>

        <div class="glass-card" style="padding: 1.5rem; hover: none;">
            @if($returns->isEmpty())
                <p style="color: var(--text-muted); text-align: center; padding: 3rem 2rem;">Belum ada pengajuan komplain retur barang dalam sistem.</p>
            @else
                <div class="table-responsive" style="margin-top: 0; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>No. Order</th>
                                <th>Pembeli & Toko</th>
                                <th>Alasan Komplain Retur</th>
                                <th style="text-align: center;">Bukti Foto</th>
                                <th style="text-align: center;">Status Komplain</th>
                                <th style="text-align: center;">Tindakan Moderasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returns as $ret)
                            <tr>
                                <td><strong>{{ $ret->order->order_number }}</strong></td>
                                <td>
                                    <strong>Pembeli:</strong> {{ $ret->order->buyer->name }}<br>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">Toko: {{ $ret->order->store->name }}</span>
                                </td>
                                <td style="max-width: 250px;">
                                    {{ $ret->reason }}
                                    @if($ret->admin_notes)
                                        <div style="font-size: 0.75rem; color: #4f46e5; background: #e0e7ff; padding: 0.25rem; border-radius: 4px; margin-top: 0.25rem;">
                                            Catatan Admin: {{ $ret->admin_notes }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($ret->evidence_image)
                                        <a href="{{ asset($ret->evidence_image) }}" target="_blank" style="color: var(--primary); text-decoration: underline; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i data-lucide="image" style="width: 14px; height: 14px;"></i> Lihat Foto
                                        </a>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.85rem;">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($ret->status === 'approved')
                                        <span class="badge badge-completed">Disetujui</span>
                                    @elseif($ret->status === 'rejected')
                                        <span class="badge badge-cancelled">Ditolak</span>
                                    @else
                                        <span class="badge badge-pending">Pending Tinjau</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($ret->status === 'pending')
                                        <form action="{{ route('admin.moderateReturn', $ret->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 0.5rem; width: 180px; margin: 0 auto;" onsubmit="return confirmAction(event, 'Proses keputusan komplain retur ini?')">
                                            @csrf
                                            <input type="text" name="admin_notes" class="form-control" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;" placeholder="Catatan komplain..." required>
                                            
                                            <div style="display: flex; gap: 0.25rem;">
                                                <button type="submit" name="status" value="approved" class="btn btn-primary btn-sm" style="flex: 1; padding: 0.35rem 0.5rem; font-size: 0.8rem; background-color: var(--status-completed); box-shadow: none;">Setujui</button>
                                                <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm" style="flex: 1; padding: 0.35rem 0.5rem; font-size: 0.8rem;">Tolak</button>
                                            </div>
                                        </form>
                                    @else
                                        <span style="font-size: 0.85rem; color: var(--text-muted);">Selesai diproses</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 2rem; display: flex; justify-content: center;">
                    {{ $returns->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
