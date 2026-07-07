<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Models\Cart;
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
        // 1. Users
        $admin = User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        $sellerBudi = User::create([
            'name' => 'Budi Setiawan',
            'email' => 'budi@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'penjual',
            'phone' => '082345678901',
        ]);

        $sellerAni = User::create([
            'name' => 'Ani Wijaya',
            'email' => 'ani@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'penjual',
            'phone' => '083456789012',
        ]);

        $sellerDedi = User::create([
            'name' => 'Dedi Kurniawan',
            'email' => 'dedi@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'penjual',
            'phone' => '084567890123',
        ]);

        $buyerWati = User::create([
            'name' => 'Wati Lestari',
            'email' => 'wati@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'pembeli',
            'phone' => '085678901234',
        ]);

        $buyerRudi = User::create([
            'name' => 'Rudi Hartono',
            'email' => 'rudi@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'pembeli',
            'phone' => '086789012345',
        ]);

        $buyerSiska = User::create([
            'name' => 'Siska Amelia',
            'email' => 'siska@tokokita.com',
            'password' => Hash::make('password'),
            'role' => 'pembeli',
            'phone' => '087890123456',
        ]);

        // 2. Stores
        $storeBudi = Store::create([
            'user_id' => $sellerBudi->id,
            'name' => 'Toko Buku Budi',
            'slug' => 'toko-buku-budi',
            'description' => 'Menjual novel best-seller, buku pelajaran sekolah, komik, dan alat tulis terlengkap.',
            'logo' => null,
            'address' => 'Jl. Braga No. 102, Sumur Bandung, Kota Bandung',
            'phone' => '082345678901',
            'operating_hours_open' => '08:00:00',
            'operating_hours_close' => '18:00:00',
            'shipping_areas' => ['Bandung', 'Jakarta', 'Sumedang', 'Cimahi'],
        ]);

        $storeAni = Store::create([
            'user_id' => $sellerAni->id,
            'name' => 'Ani Fashion Hub',
            'slug' => 'ani-fashion-hub',
            'description' => 'Supplier pakaian wanita modis, busana muslimah premium, hijab segi empat, dan outer rajut berkualitas.',
            'logo' => null,
            'address' => 'Jl. Thamrin Boulevard No. 45, Tanah Abang, Jakarta Pusat',
            'phone' => '083456789012',
            'operating_hours_open' => '09:00:00',
            'operating_hours_close' => '20:00:00',
            'shipping_areas' => ['Jakarta', 'Depok', 'Tangerang', 'Bekasi', 'Bogor'],
        ]);

        $storeDedi = Store::create([
            'user_id' => $sellerDedi->id,
            'name' => 'Dedi Elektronik',
            'slug' => 'dedi-elektronik',
            'description' => 'Menjual aksesoris gadget, powerbank, headset wireless, kabel data fast charging, dan lampu pintar.',
            'logo' => null,
            'address' => 'Mall ITC Roxy Mas Lt. 2 No. 8, Harmoni, Jakarta Barat',
            'phone' => '084567890123',
            'operating_hours_open' => '10:00:00',
            'operating_hours_close' => '19:00:00',
            'shipping_areas' => ['Jakarta', 'Surabaya', 'Medan', 'Bandung'],
        ]);

        // 3. Categories
        $catBuku = Category::create(['name' => 'Buku & Alat Tulis', 'slug' => 'buku-alat-tulis']);
        $catFashion = Category::create(['name' => 'Fashion & Pakaian', 'slug' => 'fashion-pakaian']);
        $catElektronik = Category::create(['name' => 'Elektronik & Gadget', 'slug' => 'elektronik-gadget']);
        $catKuliner = Category::create(['name' => 'Makanan & Minuman', 'slug' => 'makanan-minuman']);

        // 4. Products & Variants
        // Toko Buku Budi Products
        $pBuku1 = Product::create([
            'store_id' => $storeBudi->id,
            'category_id' => $catBuku->id,
            'name' => 'Novel Laskar Pelangi - Edisi Spesial',
            'slug' => 'novel-laskar-pelangi-edisi-spesial',
            'description' => 'Novel mega best-seller karya Andrea Hirata tentang perjuangan 10 anak Belitong menggapai mimpi sekolah.',
            'image' => null,
            'price' => 85000.00,
            'stock' => 50,
            'discount_percent' => 10.00, // Rp 76.500
        ]);
        ProductVariant::create(['product_id' => $pBuku1->id, 'name' => 'Hard Cover', 'additional_price' => 20000.00, 'stock' => 15]);
        ProductVariant::create(['product_id' => $pBuku1->id, 'name' => 'Soft Cover', 'additional_price' => 0.00, 'stock' => 35]);

        $pBuku2 = Product::create([
            'store_id' => $storeBudi->id,
            'category_id' => $catBuku->id,
            'name' => 'Buku Pemrograman Web dengan Laravel 10',
            'slug' => 'buku-pemrograman-web-dengan-laravel-10',
            'description' => 'Panduan praktis membangun aplikasi web modern berskala enterprise dari awal menggunakan framework Laravel 10.',
            'image' => null,
            'price' => 120000.00,
            'stock' => 30,
            'discount_percent' => 0.00,
        ]);
        ProductVariant::create(['product_id' => $pBuku2->id, 'name' => 'Buku Cetak', 'additional_price' => 0.00, 'stock' => 20]);
        ProductVariant::create(['product_id' => $pBuku2->id, 'name' => 'Paket + E-Book PDF', 'additional_price' => 30000.00, 'stock' => 10]);

        $pBuku3 = Product::create([
            'store_id' => $storeBudi->id,
            'category_id' => $catBuku->id,
            'name' => 'Alat Tulis Sekolah Set Lengkap',
            'slug' => 'alat-tulis-sekolah-set-lengkap',
            'description' => 'Paket alat tulis isi pensil 2B, pulpen hitam, penghapus, penggaris, dan kotak pensil anak.',
            'image' => null,
            'price' => 25000.00,
            'stock' => 100,
            'discount_percent' => 5.00, // Rp 23.750
        ]);

        // Toko Ani Fashion Hub Products
        $pFashion1 = Product::create([
            'store_id' => $storeAni->id,
            'category_id' => $catFashion->id,
            'name' => 'Hijab Segi Empat Voal Premium',
            'slug' => 'hijab-segi-empat-voal-premium',
            'description' => 'Hijab voal premium berukuran 115x115 cm. Bahan tegak di dahi, tidak terawang, adem, dan sangat lembut.',
            'image' => null,
            'price' => 45000.00,
            'stock' => 150,
            'discount_percent' => 15.00, // Rp 38.250
        ]);
        ProductVariant::create(['product_id' => $pFashion1->id, 'name' => 'Hitam', 'additional_price' => 0.00, 'stock' => 50]);
        ProductVariant::create(['product_id' => $pFashion1->id, 'name' => 'Milo (Cokelat Muda)', 'additional_price' => 0.00, 'stock' => 50]);
        ProductVariant::create(['product_id' => $pFashion1->id, 'name' => 'Navy (Biru Dongker)', 'additional_price' => 0.00, 'stock' => 50]);

        $pFashion2 = Product::create([
            'store_id' => $storeAni->id,
            'category_id' => $catFashion->id,
            'name' => 'Kemeja Flanel Unisex Retro Style',
            'slug' => 'kemeja-flanel-unisex-retro-style',
            'description' => 'Kemeja flanel lengan panjang berbahan katun flanel tebal bertekstur lembut dengan motif kotak-kotak klasik.',
            'image' => null,
            'price' => 135000.00,
            'stock' => 40,
            'discount_percent' => 0.00,
        ]);
        ProductVariant::create(['product_id' => $pFashion2->id, 'name' => 'Merah-Hitam (M)', 'additional_price' => 0.00, 'stock' => 15]);
        ProductVariant::create(['product_id' => $pFashion2->id, 'name' => 'Merah-Hitam (L)', 'additional_price' => 0.00, 'stock' => 15]);
        ProductVariant::create(['product_id' => $pFashion2->id, 'name' => 'Hijau-Hitam (L)', 'additional_price' => 5000.00, 'stock' => 10]);

        // Toko Dedi Elektronik Products
        $pElek1 = Product::create([
            'store_id' => $storeDedi->id,
            'category_id' => $catElektronik->id,
            'name' => 'Powerbank 10000mAh Fast Charge 22.5W',
            'slug' => 'powerbank-10000mah-fast-charge-225w',
            'description' => 'Powerbank slim dual-output USB + Type-C Power Delivery. Mendukung pengisian super cepat untuk Android & iPhone.',
            'image' => null,
            'price' => 189000.00,
            'stock' => 25,
            'discount_percent' => 20.00, // Rp 151.200
        ]);

        $pElek2 = Product::create([
            'store_id' => $storeDedi->id,
            'category_id' => $catElektronik->id,
            'name' => 'TWS Bluetooth Earphones Hi-Fi Sound',
            'slug' => 'tws-bluetooth-earphones-hi-fi-sound',
            'description' => 'True Wireless Stereo dengan bluetooth 5.3, latensi rendah untuk gaming, daya tahan baterai 6 jam, suara bass mantap.',
            'image' => null,
            'price' => 250000.00,
            'stock' => 15,
            'discount_percent' => 0.00,
        ]);

        // 5. Vouchers
        // Global admin voucher
        Voucher::create([
            'store_id' => null,
            'code' => 'DISKONSEPULUH',
            'type' => 'percent',
            'value' => 10.00,
            'min_spend' => 50000.00,
            'start_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'active' => true,
        ]);

        // Toko Buku Budi voucher
        Voucher::create([
            'store_id' => $storeBudi->id,
            'code' => 'BUDIBUKU5K',
            'type' => 'fixed',
            'value' => 5000.00,
            'min_spend' => 30000.00,
            'start_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'active' => true,
        ]);

        // Ani Fashion Hub voucher
        Voucher::create([
            'store_id' => $storeAni->id,
            'code' => 'ANIFASHION20K',
            'type' => 'fixed',
            'value' => 20000.00,
            'min_spend' => 100000.00,
            'start_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'active' => true,
        ]);

        // 6. Carts
        Cart::create([
            'user_id' => $buyerWati->id,
            'product_id' => $pBuku1->id,
            'product_variant_id' => $pBuku1->variants->first()->id, // Hard Cover
            'quantity' => 1,
        ]);

        Cart::create([
            'user_id' => $buyerWati->id,
            'product_id' => $pFashion1->id,
            'product_variant_id' => $pFashion1->variants->first()->id, // Hitam
            'quantity' => 2,
        ]);

        // 7. Orders & Items (Historical records for sales metrics)
        // Order 1: Completed - Wati buys from Toko Buku Budi (10 days ago)
        $ord1_total = 76500.00 + 20000.00; // Book 1 (Hard cover discounted price: Rp 96.500)
        $ord1 = Order::create([
            'order_number' => 'ORD-' . Carbon::now()->subDays(10)->format('Ymd') . '-001',
            'buyer_id' => $buyerWati->id,
            'store_id' => $storeBudi->id,
            'voucher_id' => null,
            'total_amount' => $ord1_total,
            'discount_amount' => 0.00,
            'shipping_cost' => 15000.00,
            'final_amount' => $ord1_total + 15000.00,
            'status' => 'completed',
            'notes' => 'Tolong bungkus rapi ya gan.',
            'shipping_address' => 'Jl. Dipatiukur No. 4, Coblong, Bandung',
            'shipping_recipient_name' => 'Wati Lestari',
            'shipping_recipient_phone' => '085678901234',
            'shipping_courier' => 'JNE Reguler',
            'shipping_estimate' => '1-2 Hari',
            'tracking_number' => 'JNE1234567890',
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(8),
        ]);

        OrderItem::create([
            'order_id' => $ord1->id,
            'product_id' => $pBuku1->id,
            'product_variant_id' => $pBuku1->variants->first()->id, // Hard Cover
            'quantity' => 1,
            'price' => 85000.00,
            'discount_amount' => 8500.00, // 10%
        ]);

        Payment::create([
            'order_id' => $ord1->id,
            'payment_method' => 'Transfer Bank BCA',
            'amount' => $ord1->final_amount,
            'status' => 'confirmed',
            'payment_receipt' => 'receipts/dummy-receipt-1.jpg',
            'confirmed_at' => Carbon::now()->subDays(10)->addHours(1),
            'confirmed_by' => $sellerBudi->id,
            'created_at' => Carbon::now()->subDays(10),
        ]);

        OrderHistory::create(['order_id' => $ord1->id, 'status' => 'pending', 'notes' => 'Pesanan berhasil dibuat', 'created_at' => Carbon::now()->subDays(10)]);
        OrderHistory::create(['order_id' => $ord1->id, 'status' => 'processing', 'notes' => 'Pembayaran terkonfirmasi oleh penjual', 'created_at' => Carbon::now()->subDays(10)->addHours(1)]);
        OrderHistory::create(['order_id' => $ord1->id, 'status' => 'shipped', 'notes' => 'Pesanan diserahkan ke kurir JNE', 'created_at' => Carbon::now()->subDays(9)]);
        OrderHistory::create(['order_id' => $ord1->id, 'status' => 'completed', 'notes' => 'Pesanan diterima oleh pembeli', 'created_at' => Carbon::now()->subDays(8)]);

        Review::create([
            'order_item_id' => $ord1->items->first()->id,
            'product_id' => $pBuku1->id,
            'user_id' => $buyerWati->id,
            'rating' => 5,
            'comment' => 'Bukunya sangat bagus, hard covernya mantap! Packingnya aman dilapis bubble wrap tebal. Terima kasih Budi!',
        ]);

        // Order 2: Completed - Rudi buys from Ani Fashion Hub (5 days ago)
        $ord2_total = (38250.00 * 2) + 135000.00; // 2 Voal Premium (Milo) + 1 Flanel (Merah L) = Rp 76.500 + Rp 135.000 = Rp 211.500
        $ord2 = Order::create([
            'order_number' => 'ORD-' . Carbon::now()->subDays(5)->format('Ymd') . '-002',
            'buyer_id' => $buyerRudi->id,
            'store_id' => $storeAni->id,
            'voucher_id' => null,
            'total_amount' => $ord2_total,
            'discount_amount' => 0.00,
            'shipping_cost' => 20000.00,
            'final_amount' => $ord2_total + 20000.00,
            'status' => 'completed',
            'notes' => 'Tolong kirim warna Milo sesuai pesanan.',
            'shipping_address' => 'Perumahan Dago Asri Blok C-2, Dago, Bandung',
            'shipping_recipient_name' => 'Rudi Hartono',
            'shipping_recipient_phone' => '086789012345',
            'shipping_courier' => 'SiCepat Reguler',
            'shipping_estimate' => '2-3 Hari',
            'tracking_number' => 'SIG987654321',
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(3),
        ]);

        OrderItem::create([
            'order_id' => $ord2->id,
            'product_id' => $pFashion1->id,
            'product_variant_id' => $pFashion1->variants->where('name', 'Milo (Cokelat Muda)')->first()->id ?? null,
            'quantity' => 2,
            'price' => 45000.00,
            'discount_amount' => 6750.00,
        ]);

        OrderItem::create([
            'order_id' => $ord2->id,
            'product_id' => $pFashion2->id,
            'product_variant_id' => $pFashion2->variants->where('name', 'Merah-Hitam (L)')->first()->id ?? null,
            'quantity' => 1,
            'price' => 135000.00,
            'discount_amount' => 0.00,
        ]);

        Payment::create([
            'order_id' => $ord2->id,
            'payment_method' => 'Transfer Bank Mandiri',
            'amount' => $ord2->final_amount,
            'status' => 'confirmed',
            'payment_receipt' => 'receipts/dummy-receipt-2.jpg',
            'confirmed_at' => Carbon::now()->subDays(5)->addHours(2),
            'confirmed_by' => $sellerAni->id,
            'created_at' => Carbon::now()->subDays(5),
        ]);

        OrderHistory::create(['order_id' => $ord2->id, 'status' => 'pending', 'notes' => 'Pesanan berhasil dibuat', 'created_at' => Carbon::now()->subDays(5)]);
        OrderHistory::create(['order_id' => $ord2->id, 'status' => 'processing', 'notes' => 'Pembayaran dikonfirmasi', 'created_at' => Carbon::now()->subDays(5)->addHours(2)]);
        OrderHistory::create(['order_id' => $ord2->id, 'status' => 'shipped', 'notes' => 'Paket dikirim kurir SiCepat', 'created_at' => Carbon::now()->subDays(4)]);
        OrderHistory::create(['order_id' => $ord2->id, 'status' => 'completed', 'notes' => 'Pesanan tiba di tujuan', 'created_at' => Carbon::now()->subDays(3)]);

        Review::create([
            'order_item_id' => $ord2->items->first()->id,
            'product_id' => $pFashion1->id,
            'user_id' => $buyerRudi->id,
            'rating' => 4,
            'comment' => 'Kainnya adem, warna Milo-nya sangat elegan. Kurang satu bintang karena kiriman dari kurirnya telat sehari.',
        ]);

        // Order 3: Shipped/Processing - Siska buys from Dedi Elektronik (2 days ago)
        $ord3_total = 151200.00 + 250000.00; // Powerbank + TWS = Rp 401.200
        $ord3 = Order::create([
            'order_number' => 'ORD-' . Carbon::now()->subDays(2)->format('Ymd') . '-003',
            'buyer_id' => $buyerSiska->id,
            'store_id' => $storeDedi->id,
            'voucher_id' => null,
            'total_amount' => $ord3_total,
            'discount_amount' => 0.00,
            'shipping_cost' => 25000.00,
            'final_amount' => $ord3_total + 25000.00,
            'status' => 'shipped',
            'notes' => 'Mohon dikirim secepatnya karena mau dipakai ke luar kota.',
            'shipping_address' => 'Kost Cempaka Indah, Jl. Ganesha No. 10, Coblong, Bandung',
            'shipping_recipient_name' => 'Siska Amelia',
            'shipping_recipient_phone' => '087890123456',
            'shipping_courier' => 'J&T Express',
            'shipping_estimate' => '2-3 Hari',
            'tracking_number' => 'JNT7788990011',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        OrderItem::create([
            'order_id' => $ord3->id,
            'product_id' => $pElek1->id,
            'product_variant_id' => null,
            'quantity' => 1,
            'price' => 189000.00,
            'discount_amount' => 37800.00,
        ]);

        OrderItem::create([
            'order_id' => $ord3->id,
            'product_id' => $pElek2->id,
            'product_variant_id' => null,
            'quantity' => 1,
            'price' => 250000.00,
            'discount_amount' => 0.00,
        ]);

        Payment::create([
            'order_id' => $ord3->id,
            'payment_method' => 'Transfer Bank BCA',
            'amount' => $ord3->final_amount,
            'status' => 'confirmed',
            'payment_receipt' => 'receipts/dummy-receipt-3.jpg',
            'confirmed_at' => Carbon::now()->subDays(2)->addHours(3),
            'confirmed_by' => $sellerDedi->id,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        OrderHistory::create(['order_id' => $ord3->id, 'status' => 'pending', 'notes' => 'Pesanan berhasil dibuat', 'created_at' => Carbon::now()->subDays(2)]);
        OrderHistory::create(['order_id' => $ord3->id, 'status' => 'processing', 'notes' => 'Pembayaran diterima', 'created_at' => Carbon::now()->subDays(2)->addHours(3)]);
        OrderHistory::create(['order_id' => $ord3->id, 'status' => 'shipped', 'notes' => 'Pesanan dikirim dengan resi JNT7788990011', 'created_at' => Carbon::now()->subDays(1)]);

        // Order 4: Pending - Wati buys from Toko Buku Budi (Today)
        $ord4_total = 120000.00 + 23750.00; // Laravel 10 book + Stationary set = Rp 143.750
        $ord4 = Order::create([
            'order_number' => 'ORD-' . Carbon::now()->format('Ymd') . '-004',
            'buyer_id' => $buyerWati->id,
            'store_id' => $storeBudi->id,
            'voucher_id' => null,
            'total_amount' => $ord4_total,
            'discount_amount' => 0.00,
            'shipping_cost' => 10000.00,
            'final_amount' => $ord4_total + 10000.00,
            'status' => 'pending',
            'notes' => null,
            'shipping_address' => 'Jl. Dipatiukur No. 4, Coblong, Bandung',
            'shipping_recipient_name' => 'Wati Lestari',
            'shipping_recipient_phone' => '085678901234',
            'shipping_courier' => 'J&T Express',
            'shipping_estimate' => '1-2 Hari',
            'tracking_number' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        OrderItem::create([
            'order_id' => $ord4->id,
            'product_id' => $pBuku2->id,
            'product_variant_id' => $pBuku2->variants->first()->id, // Buku cetak
            'quantity' => 1,
            'price' => 120000.00,
            'discount_amount' => 0.00,
        ]);

        OrderItem::create([
            'order_id' => $ord4->id,
            'product_id' => $pBuku3->id,
            'product_variant_id' => null,
            'quantity' => 1,
            'price' => 25000.00,
            'discount_amount' => 1250.00,
        ]);

        Payment::create([
            'order_id' => $ord4->id,
            'payment_method' => 'Transfer Bank BCA',
            'amount' => $ord4->final_amount,
            'status' => 'pending',
            'payment_receipt' => null,
            'confirmed_at' => null,
            'confirmed_by' => null,
            'created_at' => Carbon::now(),
        ]);

        OrderHistory::create(['order_id' => $ord4->id, 'status' => 'pending', 'notes' => 'Pesanan berhasil dibuat, menanti pembayaran', 'created_at' => Carbon::now()]);

        // Order 5: Returned/Refunded - Rudi buys from Toko Buku Budi (15 days ago, returned)
        $ord5_total = 76500.00; // 1 Book 1 (discounted Rp 76.500)
        $ord5 = Order::create([
            'order_number' => 'ORD-' . Carbon::now()->subDays(15)->format('Ymd') . '-005',
            'buyer_id' => $buyerRudi->id,
            'store_id' => $storeBudi->id,
            'voucher_id' => null,
            'total_amount' => $ord5_total,
            'discount_amount' => 0.00,
            'shipping_cost' => 15000.00,
            'final_amount' => $ord5_total + 15000.00,
            'status' => 'returned',
            'notes' => 'Beli untuk hadiah.',
            'shipping_address' => 'Perumahan Dago Asri Blok C-2, Dago, Bandung',
            'shipping_recipient_name' => 'Rudi Hartono',
            'shipping_recipient_phone' => '086789012345',
            'shipping_courier' => 'JNE Reguler',
            'shipping_estimate' => '1-2 Hari',
            'tracking_number' => 'JNE998877661',
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(12),
        ]);

        OrderItem::create([
            'order_id' => $ord5->id,
            'product_id' => $pBuku1->id,
            'product_variant_id' => $pBuku1->variants->first()->id, // Hard Cover
            'quantity' => 1,
            'price' => 85000.00,
            'discount_amount' => 8500.00,
        ]);

        Payment::create([
            'order_id' => $ord5->id,
            'payment_method' => 'Transfer Bank BCA',
            'amount' => $ord5->final_amount,
            'status' => 'confirmed',
            'payment_receipt' => 'receipts/dummy-receipt-5.jpg',
            'confirmed_at' => Carbon::now()->subDays(15)->addHours(1),
            'confirmed_by' => $sellerBudi->id,
            'created_at' => Carbon::now()->subDays(15),
        ]);

        OrderHistory::create(['order_id' => $ord5->id, 'status' => 'pending', 'notes' => 'Pesanan berhasil dibuat', 'created_at' => Carbon::now()->subDays(15)]);
        OrderHistory::create(['order_id' => $ord5->id, 'status' => 'processing', 'notes' => 'Pembayaran diterima', 'created_at' => Carbon::now()->subDays(15)->addHours(1)]);
        OrderHistory::create(['order_id' => $ord5->id, 'status' => 'shipped', 'notes' => 'Dikirim', 'created_at' => Carbon::now()->subDays(14)]);
        OrderHistory::create(['order_id' => $ord5->id, 'status' => 'completed', 'notes' => 'Diterima', 'created_at' => Carbon::now()->subDays(13)]);
        OrderHistory::create(['order_id' => $ord5->id, 'status' => 'returned', 'notes' => 'Permintaan retur diajukan', 'created_at' => Carbon::now()->subDays(12)]);

        OrderReturn::create([
            'order_id' => $ord5->id,
            'reason' => 'Ada halaman buku yang robek parah dan halaman kosong di bagian tengah.',
            'evidence_image' => 'returns/dummy-evidence.jpg',
            'status' => 'approved',
            'admin_notes' => 'Disetujui retur penuh. Penjual setuju mengganti barang/dana.',
            'created_at' => Carbon::now()->subDays(12),
        ]);

        // Order 6: Cancelled - Siska buys from Ani Fashion Hub (8 days ago)
        $ord6_total = 135000.00; // Flanel shirt
        $ord6 = Order::create([
            'order_number' => 'ORD-' . Carbon::now()->subDays(8)->format('Ymd') . '-006',
            'buyer_id' => $buyerSiska->id,
            'store_id' => $storeAni->id,
            'voucher_id' => null,
            'total_amount' => $ord6_total,
            'discount_amount' => 0.00,
            'shipping_cost' => 15000.00,
            'final_amount' => $ord6_total + 15000.00,
            'status' => 'cancelled',
            'notes' => 'Mau ganti kurir.',
            'shipping_address' => 'Kost Cempaka Indah, Jl. Ganesha No. 10, Bandung',
            'shipping_recipient_name' => 'Siska Amelia',
            'shipping_recipient_phone' => '087890123456',
            'shipping_courier' => 'JNE Reguler',
            'shipping_estimate' => '1-2 Hari',
            'tracking_number' => null,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ]);

        OrderItem::create([
            'order_id' => $ord6->id,
            'product_id' => $pFashion2->id,
            'product_variant_id' => $pFashion2->variants->first()->id,
            'quantity' => 1,
            'price' => 135000.00,
            'discount_amount' => 0.00,
        ]);

        OrderHistory::create(['order_id' => $ord6->id, 'status' => 'pending', 'notes' => 'Pesanan berhasil dibuat', 'created_at' => Carbon::now()->subDays(8)]);
        OrderHistory::create(['order_id' => $ord6->id, 'status' => 'cancelled', 'notes' => 'Dibatalkan oleh pembeli sebelum pembayaran', 'created_at' => Carbon::now()->subDays(8)]);

        // 8. Reviews on other items to seed ratings
        Review::create([
            'order_item_id' => $ord2->items->last()->id,
            'product_id' => $pFashion2->id,
            'user_id' => $buyerRudi->id,
            'rating' => 5,
            'comment' => 'Kemeja flanelnya pas sekali di badan, tebal dan nyaman. Jahitannya rapi. Sangat merekomendasikan penjual ini!',
        ]);
    }
}
