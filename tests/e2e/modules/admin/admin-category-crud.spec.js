import { test, expect } from '@playwright/test';
import { LoginPage } from '../../page-objects/LoginPage.js';
import { ACCOUNTS } from '../../helpers/accounts.js';

test.describe('Admin Category CRUD Operations', () => {

  test('Admin can navigate to categories, create, edit, and delete a category', async ({ page }) => {
    // 1. Login as Admin
    const loginPage = new LoginPage(page);
    await loginPage.navigate();
    await loginPage.login(ACCOUNTS.admin.email, ACCOUNTS.admin.password);
    await loginPage.verifySuccessRedirect('/admin/dashboard');

    // 2. Navigate to Category Index
    await page.click('aside a:has-text("Kelola Kategori")');
    await expect(page.locator('h1:has-text("Manajemen Kategori Produk")')).toBeVisible();

    // 3. Navigate to Create Category Form
    await page.click('a:has-text("Tambah Kategori")');
    await expect(page.locator('h1:has-text("Tambah Kategori Baru")')).toBeVisible();

    // Test real-time validation (Min 3 chars)
    const nameInput = page.locator('#name');
    const nameError = page.locator('#name-error');
    
    await nameInput.fill('Ab');
    await expect(nameError).toBeVisible();
    await expect(nameError).toContainText('Nama kategori minimal harus terdiri dari 3 karakter.');

    const categoryName = 'Kerajinan Kayu E2E Test ' + Date.now();
    await nameInput.fill(categoryName);
    await expect(nameError).toBeHidden();

    // Submit form
    await page.click('button:has-text("Simpan Kategori")');

    // Verify redirected back to index and success alert
    await expect(page.locator('h1:has-text("Manajemen Kategori Produk")')).toBeVisible();
    await expect(page.locator('text=Kategori baru berhasil ditambahkan.')).toBeVisible();
    await expect(page.locator(`tr:has-text("${categoryName}")`)).toBeVisible();

    // 4. Edit the category
    await page.click(`tr:has-text("${categoryName}") >> .btn-secondary`);
    await expect(page.locator('h1:has-text("Edit Kategori")')).toBeVisible();

    const categoryNameEdited = categoryName + ' Edited';
    await page.fill('#name', categoryNameEdited);
    await page.click('button:has-text("Perbarui Kategori")');

    // Verify edited category in table
    await expect(page.locator('h1:has-text("Manajemen Kategori Produk")')).toBeVisible();
    await expect(page.locator('text=Kategori berhasil diperbarui.')).toBeVisible();
    await expect(page.locator(`tr:has-text("${categoryNameEdited}")`)).toBeVisible();

    // 5. Delete the category
    // Set up dialog listener to accept deletion confirmation
    page.once('dialog', async dialog => {
      expect(dialog.message()).toContain('Apakah Anda yakin ingin menghapus kategori ini?');
      await dialog.accept();
    });

    await page.click(`tr:has-text("${categoryNameEdited}") >> .btn-danger`);

    // Verify deleted
    await expect(page.locator('text=Kategori berhasil dihapus.')).toBeVisible();
    await expect(page.locator(`tr:has-text("${categoryNameEdited}")`)).toBeHidden();
  });
});
