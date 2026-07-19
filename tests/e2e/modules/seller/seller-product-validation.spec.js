import { test, expect } from '@playwright/test';
import { LoginPage } from '../../page-objects/LoginPage.js';
import { ACCOUNTS } from '../../helpers/accounts.js';

test.describe('Seller Product Validation Flow', () => {

  test('Should show real-time error messages for invalid fields', async ({ page }) => {
    // 1. Login as Seller
    const loginPage = new LoginPage(page);
    await loginPage.navigate();
    await loginPage.login(ACCOUNTS.seller.budi.email, ACCOUNTS.seller.budi.password);
    await loginPage.verifySuccessRedirect('/seller/dashboard');

    // 2. Navigate to product index and click "Tambah Produk"
    await page.click('aside a:has-text("Produk Toko")');
    await expect(page.locator('h1:has-text("Manajemen Produk")')).toBeVisible();

    await page.click('a:has-text("Tambah Produk")');
    await expect(page.locator('h1:has-text("Tambah Produk Baru")')).toBeVisible();

    // 3. Test Product Name Validation (Min 5 chars)
    const nameInput = page.locator('#name');
    const nameError = page.locator('#name-error');
    
    await nameInput.fill('Kaos');
    await expect(nameError).toBeVisible();
    await expect(nameError).toContainText('Nama produk minimal harus terdiri dari 5 karakter.');

    await nameInput.fill('Kaos Polos');
    await expect(nameError).toBeHidden();

    // 4. Test Price Validation (Non-negative)
    const priceInput = page.locator('#price');
    const priceError = page.locator('#price-error');

    await priceInput.fill('-1000');
    await expect(priceError).toBeVisible();
    await expect(priceError).toContainText('Harga base tidak boleh bernilai negatif.');

    await priceInput.fill('50000');
    await expect(priceError).toBeHidden();

    // 5. Test Stock Validation (Integer, non-negative)
    const stockInput = page.locator('#stock');
    const stockError = page.locator('#stock-error');

    await stockInput.fill('10.5');
    await expect(stockError).toBeVisible();
    await expect(stockError).toContainText('Stok utama harus berupa angka bulat dan tidak boleh negatif.');

    await stockInput.fill('-5');
    await expect(stockError).toBeVisible();

    await stockInput.fill('20');
    await expect(stockError).toBeHidden();
  });
});
