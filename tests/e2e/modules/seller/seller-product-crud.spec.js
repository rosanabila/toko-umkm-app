import { test, expect } from '../../fixtures/auth-fixtures.js';

test.describe('Seller Product CRUD Operations', () => {

  test('Seller can navigate to products, create, edit, and delete a product', async ({ sellerPage }) => {
    // 1. Go to Products index page
    await sellerPage.goto('/seller/products');
    await expect(sellerPage.locator('h1:has-text("Manajemen Produk")')).toBeVisible();

    // 2. Click on "Tambah Produk"
    await sellerPage.click('text=Tambah Produk');
    await expect(sellerPage).toHaveURL(/\/seller\/products\/create/);

    // 3. Fill in product details
    const productName = 'Kemeja Flanel E2E Test ' + Date.now();
    await sellerPage.fill('input[name="name"]', productName);
    
    // Select the first category
    await sellerPage.selectOption('select[name="category_id"]', { index: 1 });
    
    await sellerPage.fill('input[name="price"]', '125000');
    await sellerPage.fill('input[name="discount_percent"]', '10');
    await sellerPage.fill('input[name="stock"]', '30');
    await sellerPage.fill('textarea[name="description"]', 'Deskripsi produk flanel hasil pengujian E2E otomatis.');
    
    // Submit form
    await sellerPage.click('main form button[type="submit"]');

    // Verify redirected back and success message
    await expect(sellerPage).toHaveURL(/\/seller\/products/);
    await expect(sellerPage.locator('text=Produk berhasil ditambahkan!')).toBeVisible();

    // 4. Edit the product
    // Click on Edit button for the created product
    await sellerPage.click(`tr:has-text("${productName}") >> .btn-secondary`);
    await expect(sellerPage.locator('h1:has-text("Edit Produk")')).toBeVisible();

    // Change fields
    await sellerPage.fill('input[name="price"]', '150000');
    await sellerPage.click('main form button[type="submit"]');

    // Verify updated product
    await expect(sellerPage).toHaveURL(/\/seller\/products/);
    await expect(sellerPage.locator('text=Produk berhasil diperbarui!')).toBeVisible();

    // 5. Delete the product
    // Set up dialog listener to accept confirmation
    sellerPage.once('dialog', async dialog => {
      expect(dialog.message()).toContain('Apakah Anda yakin ingin menghapus produk ini dari toko?');
      await dialog.accept();
    });

    await sellerPage.click(`tr:has-text("${productName}") >> .btn-danger`);

    // Verify deleted
    await expect(sellerPage.locator('text=Produk berhasil dihapus.')).toBeVisible();
  });
});
