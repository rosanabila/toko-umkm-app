<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Review;
use App\Models\OrderHistory;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Administrator
        User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        // 2. Create Sellers & Stores
        $sellerBudi = User::create([
            'name' => 'Budi Setiawan',
            'email' => 'budi@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'penjual',
            'phone' => '082134567890',
        ]);
        $storeBudi = Store::create([
            'user_id' => $sellerBudi->id,
            'name' => 'Toko Buku Budi',
            'slug' => 'toko-buku-budi',
            'description' => 'Menyediakan aneka novel, buku pemrograman web, kamus bahasa asing, dan alat tulis sekolah lengkap dengan harga grosir.',
            'logo' => null,
            'address' => 'Kios Blok A No. 12, Pasar Buku Kwitang, Senen, Jakarta Pusat',
            'phone' => '082134567890',
            'operating_hours_open' => '08:00:00',
            'operating_hours_close' => '18:00:00',
            'shipping_areas' => 'Jakarta, Depok, Tangerang, Bekasi',
        ]);

        $sellerAni = User::create([
            'name' => 'Ani Wijaya',
            'email' => 'ani@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'penjual',
            'phone' => '083123456789',
        ]);
        $storeAni = Store::create([
            'user_id' => $sellerAni->id,
            'name' => 'Ani Fashion Hub',
            'slug' => 'ani-fashion-hub',
            'description' => 'Supplier busana muslimah premium, hijab segi empat voal, kemeja flanel retro, dan celana chino modis untuk remaja dan dewasa.',
            'logo' => null,
            'address' => 'Lantai Dasar Blok B No. 34, Pasar Grosir Tanah Abang, Jakarta Pusat',
            'phone' => '083123456789',
            'operating_hours_open' => '09:00:00',
            'operating_hours_close' => '17:00:00',
            'shipping_areas' => 'Seluruh Indonesia',
        ]);

        $sellerDedi = User::create([
            'name' => 'Dedi Kurniawan',
            'email' => 'dedi@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'penjual',
            'phone' => '084123456780',
        ]);
        $storeDedi = Store::create([
            'user_id' => $sellerDedi->id,
            'name' => 'Dedi Elektronik',
            'slug' => 'dedi-elektronik',
            'description' => 'Pusat aksesoris gadget, powerbank fast charging, headphone wireless, mouse silent, dan peralatan lampu pintar rumah tangga.',
            'logo' => null,
            'address' => 'Mall ITC Roxy Mas Lt. 2 Kios No. 10, Harmoni, Jakarta Barat',
            'phone' => '084123456780',
            'operating_hours_open' => '10:00:00',
            'operating_hours_close' => '20:00:00',
            'shipping_areas' => 'Jakarta, Bogor, Depok, Tangerang, Bekasi, Bandung, Surabaya',
        ]);

        $sellers = [$storeBudi, $storeAni, $storeDedi];

        // 3. Create Buyers (10 Pembeli)
        $buyerNames = ['Wati Astuti', 'Iwan Fals', 'Budianto Pratama', 'Citra Lestari', 'Eko Prasetyo', 'Farhan Hakim', 'Gita Gutawa', 'Hadi Wijaya', 'Indah Permata', 'Joko Susilo'];
        $buyerEmails = ['wati@tokokita.com', 'iwan@tokokita.com', 'budianto@tokokita.com', 'citra@tokokita.com', 'eko@tokokita.com', 'farhan@tokokita.com', 'gita@tokokita.com', 'hadi@tokokita.com', 'indah@tokokita.com', 'joko@tokokita.com'];
        $buyerPhones = ['085123456781', '085123456782', '085123456783', '085123456784', '085123456785', '085123456786', '085123456787', '085123456788', '085123456789', '085123456790'];
        
        $buyers = [];
        for ($i = 0; $i < 10; $i++) {
            $buyers[] = User::create([
                'name' => $buyerNames[$i],
                'email' => $buyerEmails[$i],
                'password' => Hash::make('password'),
                'role' => 'pembeli',
                'phone' => $buyerPhones[$i],
            ]);
        }

        // 4. Create Categories
        $catBuku = Category::create(['name' => 'Buku & Alat Tulis', 'slug' => 'buku-alat-tulis']);
        $catFashion = Category::create(['name' => 'Fashion & Pakaian', 'slug' => 'fashion-pakaian']);
        $catElektronik = Category::create(['name' => 'Elektronik & Gadget', 'slug' => 'elektronik-gadget']);
        $catKuliner = Category::create(['name' => 'Makanan & Minuman', 'slug' => 'makanan-minuman']);
        $catRumah = Category::create(['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga']);

        // 5. Create Vouchers
        Voucher::create([
            'store_id' => null,
            'code' => 'DISKONSEPULUH',
            'type' => 'percent',
            'value' => 10.00,
            'min_spend' => 50000.00,
            'start_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'active' => true,
        ]);
        $voucherBudi = Voucher::create([
            'store_id' => $storeBudi->id,
            'code' => 'BUDIBUKU5K',
            'type' => 'fixed',
            'value' => 5000.00,
            'min_spend' => 40000.00,
            'start_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'active' => true,
        ]);
        $voucherAni = Voucher::create([
            'store_id' => $storeAni->id,
            'code' => 'ANIFASHION20K',
            'type' => 'fixed',
            'value' => 20000.00,
            'min_spend' => 100000.00,
            'start_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'active' => true,
        ]);
        $voucherDedi = Voucher::create([
            'store_id' => $storeDedi->id,
            'code' => 'DEDIELEK15K',
            'type' => 'fixed',
            'value' => 15000.00,
            'min_spend' => 80000.00,
            'start_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'active' => true,
        ]);

        // 6. Create 50 Products (distributed across stores & categories)
        // Products definition lists
        $books = [
            'Novel Laskar Pelangi' => 85000.00, 'Buku Pemrograman Web Laravel 10' => 125000.00,
            'Komik Si Juki Edisi Lebaran' => 45000.00, 'Kamus Lengkap Inggris Indonesia' => 75000.00,
            'Paket Alat Tulis Sekolah Lengkap' => 25000.00, 'Pensil Warna Joyko 24 Warna' => 38000.00,
            'Buku Gambar Kiky Ukuran A3' => 12000.00, 'Buku Tulis Sinar Dunia 58 Lembar' => 42000.00,
            'Rautan Pensil Putar Deli Desktop' => 48000.00, 'Pulpen Gel Pilot G2 Box Isi 12' => 144000.00
        ];
        $fashions = [
            'Hijab Segi Empat Voal Laser Cut' => 42000.00, 'Kemeja Flanel Unisex Retro Checked' => 135000.00,
            'Kaos Polos Cotton Combed 30s' => 49000.00, 'Celana Panjang Chino Slimfit Stretch' => 149000.00,
            'Jaket Hoodie Jumper Fleece Tebal' => 175000.00, 'Kaus Kaki Pendek Angkel Anti Slip' => 12000.00,
            'Jaket Denim Jeans Denim Klasik Blue' => 199000.00, 'Rok Plisket Panjang Premium Ceruti' => 65000.00,
            'Baju Gamis Syari Muslimah Cantik' => 220000.00, 'Sepatu Sneakers Canvas Kanvas Lokal' => 110000.00
        ];
        $electronics = [
            'Powerbank Robot 10000mAh Dual Port' => 129000.00, 'TWS Wireless Bluetooth Earphone V5.3' => 189000.00,
            'Kabel Charger Type-C Fast Charging' => 18000.00, 'Adaptor Kepala Charger PD 20W USB-C' => 68000.00,
            'Mouse Wireless Silent Logitech M220' => 195000.00, 'Keyboard Wireless Bluetooth Mini' => 99000.00,
            'Lampu LED Bohlam Pintar RGB Wifi 9W' => 115000.00, 'Speaker Bluetooth Portable Waterproof' => 245000.00,
            'Stand Holder HP Meja Penyangga Lipat' => 15000.00, 'Ring Light Selfie Ringlight Tripod' => 78000.00
        ];
        $foods = [
            'Keripik Singkong Balado Khas Padang' => 25000.00, 'Kopi Susu Gula Aren Botol 1 Liter' => 45000.00,
            'Kue Nastar Wisman Premium Toples 500g' => 120000.00, 'Sambal Bawang Pedas Gurih Toples' => 22000.00,
            'Teh Melati Tubruk Wangi Khas Solo' => 10000.00, 'Baso Aci Garut Instan Pedas Kuah' => 18000.00,
            'Makaroni Pedas Daun Jeruk Asin Gurih' => 15000.00, 'Bumbu Instan Rendang Padang Instan' => 8000.00,
            'Madu Hutan Murni Asli Sumbawa 250ml' => 95000.00, 'Cokelat batang Batangan Karamel Bar' => 18000.00
        ];
        $households = [
            'Sprei Kasur Katun Halus Ukuran No 1' => 185000.00, 'Gantungan Baju Hanger Kayu Isi 10' => 48000.00,
            'Kotak Box Organizer Penyimpanan Baju' => 35000.00, 'Set Pisau Dapur Baja Stainless 5in1' => 65000.00,
            'Talenan Kayu Pinus Alami Estetik' => 22000.00, 'Termos Air Panas Stainless Steel 500ml' => 79000.00,
            'Sikat Pembersih Kamar Mandi Serbaguna' => 15000.00, 'Rak Sepatu Plastik Bongkar Pasang 4' => 45000.00,
            'Keset Kaki Kamar Mandi Microfiber Lembut' => 28000.00, 'Hanger Jilbab Ring Gantungan Hijab' => 18000.00
        ];

        // Seed products helper function
        $allProducts = [];
        
        // 1. Books (Toko Budi)
        foreach ($books as $name => $price) {
            $p = Product::create([
                'store_id' => $storeBudi->id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . strtolower(Str::random(4)),
                'description' => "Produk berkualitas tinggi: $name, sangat cocok untuk keperluan sekolah, hobi membaca, atau hadiah edukasi. Dijual dengan harga grosir terbaik.",
                'image' => null,
                'price' => $price,
                'stock' => rand(20, 100),
                'discount_percent' => rand(0, 3) * 5.00, // 0%, 5%, 10%, 15%
            ]);
            $p->categories()->attach($catBuku->id);
            $allProducts[] = $p;
            
            // Add variants to some
            if (rand(0, 1)) {
                ProductVariant::create(['product_id' => $p->id, 'name' => 'Edisi Cetak', 'additional_price' => 0.00, 'stock' => rand(10, 40)]);
                ProductVariant::create(['product_id' => $p->id, 'name' => 'Edisi Digital PDF', 'additional_price' => -10000.00, 'stock' => 999]);
            }
        }

        // 2. Fashions (Toko Ani)
        foreach ($fashions as $name => $price) {
            $p = Product::create([
                'store_id' => $storeAni->id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . strtolower(Str::random(4)),
                'description' => "Pilihan trendi: $name. Desain eksklusif, jahitan super rapi, adem saat dipakai, dan pas untuk menunjang penampilan kasual sehari-hari.",
                'image' => null,
                'price' => $price,
                'stock' => rand(20, 150),
                'discount_percent' => rand(0, 2) * 10.00, // 0%, 10%, 20%
            ]);
            $p->categories()->attach($catFashion->id);
            $allProducts[] = $p;
            
            if (rand(0, 1)) {
                ProductVariant::create(['product_id' => $p->id, 'name' => 'Warna Hitam', 'additional_price' => 0.00, 'stock' => rand(15, 50)]);
                ProductVariant::create(['product_id' => $p->id, 'name' => 'Warna Milo', 'additional_price' => 0.00, 'stock' => rand(15, 50)]);
                ProductVariant::create(['product_id' => $p->id, 'name' => 'Warna Navy', 'additional_price' => 5000.00, 'stock' => rand(15, 50)]);
            }
        }

        // 3. Electronics (Toko Dedi)
        foreach ($electronics as $name => $price) {
            $p = Product::create([
                'store_id' => $storeDedi->id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . strtolower(Str::random(4)),
                'description' => "Gadget canggih: $name. Mendukung performa tinggi, material kuat, garansi toko resmi, kompatibel dengan berbagai merk device modern.",
                'image' => null,
                'price' => $price,
                'stock' => rand(10, 40),
                'discount_percent' => rand(0, 4) * 5.00, // 0-20%
            ]);
            $p->categories()->attach($catElektronik->id);
            $allProducts[] = $p;
            
            if (rand(0, 1)) {
                ProductVariant::create(['product_id' => $p->id, 'name' => 'Garansi 1 Bulan', 'additional_price' => 0.00, 'stock' => rand(5, 20)]);
                ProductVariant::create(['product_id' => $p->id, 'name' => 'Garansi 1 Tahun', 'additional_price' => 25000.00, 'stock' => rand(5, 20)]);
            }
        }

        // 4. Kuliner (Toko Budi - sebagai tambahan kuliner instan)
        foreach ($foods as $name => $price) {
            $p = Product::create([
                'store_id' => $storeBudi->id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . strtolower(Str::random(4)),
                'description' => "Cita rasa khas Indonesia: $name. Higienis, tanpa bahan pengawet kimia berbahaya, halal, dan dikemas dengan aman untuk pengiriman luar kota.",
                'image' => null,
                'price' => $price,
                'stock' => rand(30, 200),
                'discount_percent' => rand(0, 2) * 5.00, // 0%, 5%, 10%
            ]);
            $p->categories()->attach($catKuliner->id);
            $allProducts[] = $p;
            
            if (rand(0, 1)) {
                ProductVariant::create(['product_id' => $p->id, 'name' => 'Kemasan Standard', 'additional_price' => 0.00, 'stock' => rand(20, 50)]);
                ProductVariant::create(['product_id' => $p->id, 'name' => 'Kemasan Premium Jar', 'additional_price' => 8000.00, 'stock' => rand(10, 30)]);
            }
        }

        // 5. Rumah Tangga (Toko Ani - sebagai tambahan rumah tangga)
        foreach ($households as $name => $price) {
            $p = Product::create([
                'store_id' => $storeAni->id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . strtolower(Str::random(4)),
                'description' => "Peralatan praktis: $name. Membantu menata rumah menjadi rapi, bersih, estetik, dan nyaman dihuni bersama keluarga tercinta.",
                'image' => null,
                'price' => $price,
                'stock' => rand(15, 80),
                'discount_percent' => rand(0, 2) * 5.00,
            ]);
            $p->categories()->attach($catRumah->id);
            $allProducts[] = $p;
        }

        // 7. Generate 105 Orders with various statuses and payment records
        $orderStatuses = ['completed', 'completed', 'completed', 'completed', 'completed', 'processing', 'shipped', 'pending', 'cancelled', 'returned'];
        $reviewComments = [
            5 => ['Barang bagus sekali sesuai deskripsi!', 'Sangat puas belanja di toko ini. Respon cepat.', 'Kualitas bintang lima, harga bersahabat.', 'Recommended seller! Mantap.'],
            4 => ['Produk oke, berfungsi dengan baik.', 'Bagus, packing rapi, kiriman cepat.', 'Barang oke, sesuai harga.', 'Cukup puas, responsif.'],
            3 => ['Kualitas standar, pengiriman lumayan lama.', 'Biasa saja, sesuai dengan harganya.', 'Barang ada lecet sedikit tapi masih bisa dipakai.'],
            2 => ['Kurang sesuai ekspektasi, bahan agak tipis.', 'Respon penjual lambat, barang telat datang.'],
            1 => ['Sangat kecewa, barang pecah/rusak!', 'Tidak sesuai foto. Kapok belanja di sini.', 'Salah kirim varian dan tidak ada solusi.']
        ];
        
        $couriers = ['JNE', 'J&T', 'SiCepat', 'GoSend'];
        $banks = ['BCA Transfer', 'Mandiri Transfer', 'BRI Transfer', 'BNI Transfer'];
        $estimates = ['1-2 Hari', '2-3 Hari', '3-4 Hari', 'Sameday'];

        for ($o = 1; $o <= 105; $o++) {
            $buyer = $buyers[rand(0, 9)];
            $store = $sellers[rand(0, 2)];
            
            // Fetch products belonging to this store
            $storeProducts = Product::where('store_id', $store->id)->get();
            if ($storeProducts->isEmpty()) continue;
            
            // Add 1 to 2 items
            $itemCount = rand(1, 2);
            $selectedProducts = $storeProducts->random(min($itemCount, $storeProducts->count()));
            
            // Calculate base pricing
            $subtotal = 0;
            $itemsData = [];
            
            foreach ($selectedProducts as $prod) {
                $qty = rand(1, 2);
                $price = $prod->price;
                $disc = $prod->discount_percent;
                
                // Pick variant if exists
                $variantId = null;
                $varName = '';
                $additionalPrice = 0;
                
                $variant = $prod->variants->isEmpty() ? null : $prod->variants->random();
                if ($variant) {
                    $variantId = $variant->id;
                    $varName = $variant->name;
                    $additionalPrice = $variant->additional_price;
                }
                
                $itemPrice = $price + $additionalPrice;
                $itemDiscountAmount = $itemPrice * ($disc / 100);
                
                $subtotal += ($itemPrice - $itemDiscountAmount) * $qty;
                
                $itemsData[] = [
                    'product_id' => $prod->id,
                    'product_variant_id' => $variantId,
                    'quantity' => $qty,
                    'price' => $itemPrice,
                    'discount' => $disc,
                ];
            }
            
            // Determine voucher eligibility
            $voucherId = null;
            $discountAmount = 0.00;
            
            if (rand(0, 1)) {
                // Try applying store specific or global voucher
                $voucher = Voucher::where(function ($query) use ($store) {
                    $query->where('store_id', $store->id)
                          ->orWhereNull('store_id');
                })->where('min_spend', '<=', $subtotal)->first();
                
                if ($voucher) {
                    $voucherId = $voucher->id;
                    if ($voucher->type === 'percent') {
                        $discountAmount = $subtotal * ($voucher->value / 100);
                    } else {
                        $discountAmount = $voucher->value;
                    }
                }
            }
            
            $shippingCost = rand(1, 3) * 9000.00; // Rp 9K, 18K, 27K
            $grandTotal = $subtotal - $discountAmount + $shippingCost;
            
            // Status selection
            $status = $orderStatuses[rand(0, 9)];
            
            // Scattered creation dates over the last 30 days
            $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $orderNumber = 'ORD-' . $createdAt->format('Ymd') . '-' . strtoupper(Str::random(5));
            
            // Insert Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $buyer->id,
                'store_id' => $store->id,
                'voucher_id' => $voucherId,
                'total_amount' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'final_amount' => $grandTotal,
                'status' => $status,
                'shipping_address' => 'Jl. Kebagusan Dalam No. ' . rand(1, 99) . ', Pasar Minggu, Jakarta Selatan',
                'shipping_recipient_name' => $buyer->name,
                'shipping_recipient_phone' => $buyer->phone,
                'shipping_courier' => $couriers[rand(0, 3)],
                'shipping_estimate' => $estimates[rand(0, 3)],
                'tracking_number' => ($status === 'shipped' || $status === 'completed') ? 'TRK' . $createdAt->format('Ymd') . rand(1000, 9999) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Save order items
            foreach ($itemsData as $itemInfo) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemInfo['product_id'],
                    'product_variant_id' => $itemInfo['product_variant_id'],
                    'quantity' => $itemInfo['quantity'],
                    'price' => $itemInfo['price'],
                    'discount_amount' => $itemInfo['discount'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Create reviews for completed orders (50% probability)
                if ($status === 'completed' && rand(0, 1)) {
                    $rating = rand(3, 5); // Bias towards positive reviews
                    if (rand(0, 9) === 0) $rating = rand(1, 2); // Occasional negative review
                    
                    $comments = $reviewComments[$rating];
                    $comment = $comments[rand(0, count($comments) - 1)];

                    Review::create([
                        'order_item_id' => $orderItem->id,
                        'product_id' => $itemInfo['product_id'],
                        'user_id' => $buyer->id,
                        'rating' => $rating,
                        'comment' => $comment,
                        'is_moderated' => false,
                        'created_at' => $createdAt->addDays(rand(1, 3)),
                        'updated_at' => $createdAt->addDays(rand(1, 3)),
                    ]);
                }
            }

            // Create payments
            $payStatus = 'pending';
            if ($status === 'completed' || $status === 'shipped' || $status === 'processing') {
                $payStatus = 'confirmed';
            } elseif ($status === 'returned') {
                $payStatus = 'refunded';
            } elseif ($status === 'cancelled') {
                $payStatus = 'failed';
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $banks[rand(0, 3)],
                'amount' => $grandTotal,
                'status' => $payStatus,
                'payment_receipt' => ($payStatus !== 'pending') ? 'uploads/receipts/proof_' . $order->id . '.jpg' : null,
                'confirmed_at' => ($payStatus === 'confirmed') ? $createdAt->addMinutes(rand(10, 120)) : null,
                'confirmed_by' => ($payStatus === 'confirmed') ? $store->user_id : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Create tracking histories logs
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'notes' => 'Pesanan berhasil dibuat oleh pembeli.',
                'created_at' => $createdAt,
            ]);
            
            if ($payStatus === 'confirmed') {
                OrderHistory::create([
                    'order_id' => $order->id,
                    'status' => 'processing',
                    'notes' => 'Pembayaran dikonfirmasi. Penjual sedang memproses pengemasan barang.',
                    'created_at' => $createdAt->addMinutes(rand(10, 120)),
                ]);
            }

            if ($status === 'shipped' || $status === 'completed') {
                OrderHistory::create([
                    'order_id' => $order->id,
                    'status' => 'shipped',
                    'notes' => 'Barang diserahkan ke kurir pengiriman dengan resi: ' . $order->tracking_number,
                    'created_at' => $createdAt->addHours(rand(12, 36)),
                ]);
            }

            if ($status === 'completed') {
                OrderHistory::create([
                    'order_id' => $order->id,
                    'status' => 'completed',
                    'notes' => 'Barang telah diterima oleh pembeli. Transaksi selesai.',
                    'created_at' => $createdAt->addDays(rand(2, 5)),
                ]);
            }

            // Create returns (for returned orders status)
            if ($status === 'returned') {
                OrderReturn::create([
                    'order_id' => $order->id,
                    'reason' => 'Barang pecah/robek di bagian pojok saat unboxing paket.',
                    'evidence_image' => 'uploads/returns/proof_' . $order->id . '.jpg',
                    'status' => rand(0, 1) ? 'approved' : 'pending',
                    'admin_notes' => 'Telah diperiksa bukti foto dan disetujui untuk dikembalikan.',
                    'created_at' => $createdAt->addDays(rand(1, 3)),
                    'updated_at' => $createdAt->addDays(rand(1, 3)),
                ]);
                
                OrderHistory::create([
                    'order_id' => $order->id,
                    'status' => 'returned',
                    'notes' => 'Pembeli mengajukan komplain retur pengembalian barang rusak.',
                    'created_at' => $createdAt->addDays(rand(1, 3)),
                ]);
            }
        }

        // 8. Populating summary daily sales report database values
        // Gather all completed orders
        $completedOrders = Order::where('status', 'completed')->get();
        foreach ($completedOrders as $order) {
            $date = Carbon::parse($order->created_at)->format('Y-m-d');
            
            // Check if summary entry exists
            $summary = \DB::table('daily_sales_summaries')
                ->where('store_id', $order->store_id)
                ->where('date', $date)
                ->first();
                
            if ($summary) {
                \DB::table('daily_sales_summaries')
                    ->where('id', $summary->id)
                    ->update([
                        'total_sales' => $summary->total_sales + $order->final_amount,
                        'order_count' => $summary->order_count + 1,
                        'updated_at' => Carbon::now(),
                    ]);
            } else {
                \DB::table('daily_sales_summaries')->insert([
                    'store_id' => $order->store_id,
                    'date' => $date,
                    'total_sales' => $order->final_amount,
                    'order_count' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
