import { test, expect } from '../../fixtures/auth-fixtures.js';

test.describe('Seller Voucher CRUD Operations', () => {

  test('Seller can navigate to vouchers, create, edit, and delete a voucher', async ({ sellerPage }) => {
    // 1. Go to Vouchers index page
    await sellerPage.goto('/seller/vouchers');
    await expect(sellerPage.locator('h1:has-text("Voucher Belanja")')).toBeVisible();

    // 2. Click on "Tambah Voucher"
    await sellerPage.click('text=Tambah Voucher');
    await expect(sellerPage).toHaveURL(/\/seller\/vouchers\/create/);

    // 3. Fill in voucher details
    const voucherCode = 'E2ETEST' + Math.floor(Math.random() * 10000);
    await sellerPage.fill('input[name="code"]', voucherCode);
    await sellerPage.selectOption('select[name="type"]', 'fixed');
    await sellerPage.fill('input[name="value"]', '10000');
    await sellerPage.fill('input[name="min_spend"]', '50000');
    await sellerPage.selectOption('select[name="active"]', '1');
    
    // Dates
    const today = new Date().toISOString().split('T')[0];
    const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    await sellerPage.fill('input[name="start_date"]', today);
    await sellerPage.fill('input[name="end_date"]', nextWeek);
    
    // Submit form
    await sellerPage.click('main form button[type="submit"]');

    // Verify redirected back and success message
    await expect(sellerPage).toHaveURL(/\/seller\/vouchers/);
    await expect(sellerPage.locator('text=Voucher belanja berhasil ditambahkan!')).toBeVisible();

    // 4. Edit the voucher
    // Click on Edit button for the created voucher
    await sellerPage.click(`tr:has-text("${voucherCode}") >> .btn-secondary`);
    await expect(sellerPage.locator('h1:has-text("Edit Voucher")')).toBeVisible();

    // Change value
    await sellerPage.fill('input[name="value"]', '15000');
    await sellerPage.click('main form button[type="submit"]');

    // Verify updated voucher
    await expect(sellerPage).toHaveURL(/\/seller\/vouchers/);
    await expect(sellerPage.locator('text=Voucher belanja berhasil diperbarui.')).toBeVisible();

    // 5. Delete the voucher
    // Set up dialog listener to accept confirmation
    sellerPage.once('dialog', async dialog => {
      expect(dialog.message()).toContain('Hapus voucher belanja ini?');
      await dialog.accept();
    });

    await sellerPage.click(`tr:has-text("${voucherCode}") >> .btn-danger`);

    // Verify deleted
    await expect(sellerPage.locator('text=Voucher belanja berhasil dihapus.')).toBeVisible();
  });
});
