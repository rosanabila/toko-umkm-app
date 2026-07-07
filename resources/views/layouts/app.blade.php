<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TokoKita') - E-Commerce UMKM</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="TokoKita adalah platform e-commerce UMKM terintegrasi untuk mendukung produk lokal Indonesia.">
    <meta name="keywords" content="toko umkm, belilokal, tokokita, e-commerce indonesia, laravel, skripsi">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Icons (Optional Lucide Icons via CDN) -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @yield('styles')
</head>
<body>
    <header>
        <div class="nav-container">
            <a href="{{ url('/') }}" class="logo">
                <i data-lucide="shopping-bag" style="color: var(--primary);"></i>
                <span>TokoKita</span>
            </a>
            
            <nav class="nav-links">
                <a href="{{ url('/') }}" class="nav-link {{ Request::is('/') ? 'active' : '' }}">Belanja</a>
                
                @auth
                    @if(auth()->user()->isPembeli())
                        <a href="{{ route('cart.index') }}" class="nav-link {{ Request::is('cart*') ? 'active' : '' }}" style="position: relative;">
                            <i data-lucide="shopping-cart" style="width: 18px; height: 18px; display: inline; vertical-align: middle;"></i>
                            Keranjang
                            @php
                                $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span style="position: absolute; top: -5px; right: -5px; background: var(--status-cancelled); color: white; border-radius: 50%; font-size: 10px; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; font-weight: 700;">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('buyer.orders') }}" class="nav-link {{ Request::is('buyer*') ? 'active' : '' }}">Pesanan Saya</a>
                    @elseif(auth()->user()->isPenjual())
                        <a href="{{ route('seller.dashboard') }}" class="nav-link {{ Request::is('seller*') ? 'active' : '' }}">Toko Saya (Penjual)</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin*') ? 'active' : '' }}">Panel Admin</a>
                    @endif
                    
                    <span style="color: var(--text-muted); font-size: 0.9rem; margin-left: 0.5rem; display: flex; align-items: center; gap: 0.25rem;">
                        <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                        <strong>{{ auth()->user()->name }}</strong>
                    </span>
                    
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;" onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin keluar?')">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.75rem;">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    <main style="min-height: calc(100vh - 180px); display: flex; flex-direction: column;">
        @if(session('success'))
            <div class="alert-box" style="background-color: var(--status-completed-light); color: var(--status-completed); border: 1px solid var(--status-completed); padding: 1rem 2rem; max-width: 1200px; margin: 1rem auto; width: calc(100% - 4rem); border-radius: var(--radius-md); font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert-box" style="background-color: var(--status-cancelled-light); color: var(--status-cancelled); border: 1px solid var(--status-cancelled); padding: 1rem 2rem; max-width: 1200px; margin: 1rem auto; width: calc(100% - 4rem); border-radius: var(--radius-md); font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="alert-triangle"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        <div class="footer-logo">
            <i data-lucide="shopping-bag" style="color: white; display: inline-block; vertical-align: middle; margin-right: 0.25rem;"></i>
            TokoKita
        </div>
        <p style="margin-bottom: 0.5rem;">Platform E-Commerce Pendukung Kemajuan UMKM Indonesia</p>
        <p style="font-size: 0.8rem; opacity: 0.7;">&copy; 2026 TokoKita. Aplikasi Skripsi - Laravel 10 + MySQL.</p>
    </footer>

    <!-- Initialize icons -->
    <script>
        lucide.createIcons();
    </script>
    
    <!-- Custom scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
