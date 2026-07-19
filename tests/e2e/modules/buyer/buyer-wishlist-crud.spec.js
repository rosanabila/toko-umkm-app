import { test, expect } from '../../fixtures/auth-fixtures.js';

test.describe('Buyer Wishlist CRUD Operations', () => {

  test('Buyer can add a product to wishlist and delete it from wishlist', async ({ buyerPage }) => {
    // 1. Go to homepage
    await buyerPage.goto('/');
    await expect(buyerPage.locator('h1:has-text("Dukung UMKM Lokal")')).toBeVisible();

    // 2. Click "Lihat Detail" on the first product card
    const firstProductDetailBtn = buyerPage.locator('.product-card >> text=Lihat Detail').first();
    await firstProductDetailBtn.click();
    await expect(buyerPage.locator('h1')).toBeVisible();

    const productName = await buyerPage.locator('h1').textContent();
    expect(productName).toBeTruthy();
    const cleanProductName = productName.trim();

    // 3. Click "Simpan ke Wishlist"
    await buyerPage.click('text=Simpan ke Wishlist');

    // Verify success flash alert
    await expect(buyerPage.locator('text=Produk berhasil ditambahkan ke daftar keinginan.')).toBeVisible();

    // 4. Go to Wishlist page
    await buyerPage.goto('/wishlist');
    await expect(buyerPage.locator('h1:has-text("Daftar Keinginan Saya")')).toBeVisible();

    // Verify product is in the wishlist table
    const wishRow = buyerPage.locator(`tr:has-text("${cleanProductName}")`);
    await expect(wishRow).toBeVisible();

    // 5. Delete from wishlist
    // Set up dialog listener to accept confirmation
    buyerPage.once('dialog', async dialog => {
      expect(dialog.message()).toContain('Apakah Anda yakin ingin menghapus produk ini dari daftar keinginan?');
      await dialog.accept();
    });

    await wishRow.locator('text=Hapus').click();

    // Verify success deleted alert
    await expect(buyerPage.locator('text=Produk berhasil dihapus dari daftar keinginan.')).toBeVisible();
    await expect(buyerPage.locator(`tr:has-text("${cleanProductName}")`)).not.toBeVisible();
  });
});
