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
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Style Fallback/Override -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Icons (Lucide Icons via CDN) -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* CSS to resolve duplication between layout sidebar and child view sidebars */
        .master-layout-has-sidebar .dashboard-container > .sidebar {
            display: none !important;
        }
        .master-layout-has-sidebar .dashboard-container {
            min-height: auto !important;
            display: block !important;
        }
        .master-layout-has-sidebar .dashboard-content {
            padding: 0 !important;
            background-color: transparent !important;
        }
    </style>
    
    @yield('styles')
</head>
<body class="font-sans bg-slate-50 text-slate-900 min-h-screen flex flex-col">

    @php
        $user = auth()->user();
        $isSeller = $user && $user->isPenjual();
        $isAdmin = $user && $user->isAdmin();
        $isBuyer = $user && $user->isPembeli();
        $showSidebar = $user && ($isSeller || $isAdmin);
        $store = $isSeller ? $user->store : null;
    @endphp

    <!-- Top Navbar -->
    <header class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-extrabold text-2xl tracking-tight bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                <i data-lucide="shopping-bag" class="text-indigo-600 w-6 h-6"></i>
                <span>TokoKita</span>
            </a>
            
            <nav class="flex items-center gap-4 sm:gap-6">
                <a href="{{ url('/') }}" class="text-sm font-semibold {{ Request::is('/') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">Belanja</a>
                
                @auth
                    @if($isBuyer)
                        <a href="{{ route('wishlist.index') }}" class="text-sm font-semibold {{ Request::is('wishlist*') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }} flex items-center gap-1">
                            <i data-lucide="heart" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Wishlist</span>
                        </a>
                        <a href="{{ route('cart.index') }}" class="text-sm font-semibold {{ Request::is('cart*') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }} flex items-center gap-1 relative">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Keranjang</span>
                            @php
                                $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-[10px] w-4 h-4 flex items-center justify-center font-bold">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('buyer.orders') }}" class="text-sm font-semibold {{ Request::is('buyer*') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">Pesanan Saya</a>
                    @elseif($isSeller)
                        <a href="{{ route('seller.dashboard') }}" class="text-sm font-semibold {{ Request::is('seller*') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">Dashboard Toko</a>
                    @elseif($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold {{ Request::is('admin*') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">Panel Admin</a>
                    @endif
                    
                    <span class="hidden md:flex items-center gap-1 text-sm text-slate-500 font-medium border-l border-slate-200 pl-4">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </span>
                    
                    <form action="{{ route('logout') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-red-600 px-3 py-1.5 rounded-md hover:bg-slate-100 transition-colors">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-indigo-600 px-3 py-1.5 rounded-md hover:bg-slate-100 transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-md shadow-sm transition-all hover:shadow-md">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Layout Container -->
    <div class="flex-1 flex {{ $showSidebar ? 'master-layout-has-sidebar' : '' }}">
        
        <!-- Sidebar for Admin / Seller -->
        @if($showSidebar)
            <aside class="w-64 bg-slate-900 text-slate-400 flex-shrink-0 hidden md:flex flex-col border-r border-slate-800">
                <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                    @if($isSeller)
                        <i data-lucide="store" class="text-indigo-500 w-5 h-5"></i>
                        <span class="text-white font-bold tracking-wide truncate">{{ $store->name ?? 'Toko Saya' }}</span>
                    @else
                        <i data-lucide="shield-check" class="text-indigo-500 w-5 h-5"></i>
                        <span class="text-white font-bold tracking-wide">Admin Panel</span>
                    @endif
                </div>
                <nav class="flex-1 p-4 space-y-1">
                    @if($isSeller)
                        <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('seller.dashboard') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                        </a>
                        <a href="{{ route('seller.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('seller.profile') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="settings" class="w-4 h-4"></i> Profil Toko
                        </a>
                        <a href="{{ route('seller.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('seller.products.*') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="package" class="w-4 h-4"></i> Produk Toko
                        </a>
                        <a href="{{ route('seller.vouchers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('seller.vouchers.*') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="ticket" class="w-4 h-4"></i> Voucher Belanja
                        </a>
                        <a href="{{ route('seller.orders') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('seller.orders') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i> Pesanan Masuk
                        </a>
                        <a href="{{ route('seller.reports') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('seller.reports') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Laporan Analitis
                        </a>
                    @elseif($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.users') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="users" class="w-4 h-4"></i> Pengguna & Role
                        </a>
                        <a href="{{ route('admin.reviews') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.reviews') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="message-square" class="w-4 h-4"></i> Moderasi Ulasan
                        </a>
                        <a href="{{ route('admin.returns') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('admin.returns') ? 'bg-indigo-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Retur Barang
                        </a>
                    @endif
                </nav>
            </aside>
        @endif

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col overflow-y-auto">
            
            <!-- Flash Message Alerts -->
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-6">
                @if(session('success'))
                    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-xl shadow-sm font-medium text-sm animate-fade-in">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3.5 rounded-xl shadow-sm font-medium text-sm animate-fade-in">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <!-- Page Content Slot -->
            <div class="flex-1">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="bg-slate-900 border-t border-slate-800 py-8 text-center text-slate-400">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="footer-logo text-white font-extrabold text-lg flex items-center justify-center gap-2 mb-2">
                        <i data-lucide="shopping-bag" class="text-indigo-500 w-5 h-5"></i>
                        TokoKita
                    </div>
                    <p class="text-sm font-medium mb-1">Platform E-Commerce Pendukung Kemajuan UMKM Indonesia</p>
                    <p class="text-xs opacity-60">&copy; 2026 TokoKita. Aplikasi Skripsi - Laravel 10 + MySQL.</p>
                </div>
            </footer>
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
    
    <!-- Custom scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
