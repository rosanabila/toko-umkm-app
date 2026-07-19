import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

test.describe('Buyer Product Search and Filter', () => {

  let targetProduct;

  test.beforeAll(() => {
    // Programmatically fetch an existing product to base our search/filters on
    try {
      const phpCode = `
        $product = \\App\\Models\\Product::with('categories')->where('stock', '>', 0)->first();
        if ($product) {
            echo json_encode([
                'name' => $product->name,
                'category_slug' => $product->categories->first()->slug,
                'category_name' => $product->categories->first()->name,
                'price' => (float)$product->price
            ]);
        }
      `;
      const command = `php artisan tinker --execute="${phpCode.replace(/"/g, '\\"').replace(/\n/g, ' ')}"`;
      const output = execSync(command).toString().trim();
      targetProduct = JSON.parse(output);
    } catch (e) {
      throw new Error('Gagal memuat data produk untuk pengetesan filter: ' + e.message);
    }
  });

  test('Should search products by name', async ({ page }) => {
    await page.goto('/');

    // Type product name in search input
    await page.fill('#search', targetProduct.name);
    await page.click('button:has-text("Filter")');

    // Assert that target product is visible
    await expect(page.locator('.product-card').first()).toBeVisible();
    await expect(page.locator(`.product-name:has-text("${targetProduct.name}")`)).toBeVisible();
  });

  test('Should filter products by category', async ({ page }) => {
    await page.goto('/');

    // Select category in dropdown
    await page.selectOption('#category', targetProduct.category_slug);
    await page.click('button:has-text("Filter")');

    // Assert that target product (which belongs to this category) is visible
    await expect(page.locator('.product-card').first()).toBeVisible();
    await expect(page.locator(`.product-name:has-text("${targetProduct.name}")`)).toBeVisible();
    
    // Assert all cards displayed have the correct category badge
    const badges = page.locator('.product-card .badge');
    const count = await badges.count();
    for (let i = 0; i < count; i++) {
      const badgeText = await badges.nth(i).innerText();
      expect(badgeText.trim().toUpperCase()).toBe(targetProduct.category_name.toUpperCase());
    }
  });

  test('Should filter products by price range', async ({ page }) => {
    await page.goto('/');

    // Set price range around target product price
    const minPrice = Math.max(0, targetProduct.price - 5000);
    const maxPrice = targetProduct.price + 5000;

    await page.fill('#price_min', minPrice.toString());
    await page.fill('#price_max', maxPrice.toString());
    await page.click('button:has-text("Filter")');

    // Assert target product is visible
    await expect(page.locator(`.product-name:has-text("${targetProduct.name}")`)).toBeVisible();
  });

  test('Should combine name, category, and price range filters', async ({ page }) => {
    await page.goto('/');

    // Combine all filters matching our target product
    await page.fill('#search', targetProduct.name);
    await page.selectOption('#category', targetProduct.category_slug);
    await page.fill('#price_min', Math.max(0, targetProduct.price - 1000).toString());
    await page.fill('#price_max', (targetProduct.price + 1000).toString());
    
    await page.click('button:has-text("Filter")');

    // Target product must be visible
    await expect(page.locator(`.product-name:has-text("${targetProduct.name}")`)).toBeVisible();
    
    // Clicking clear should reset url and inputs
    await page.click('a:has-text("Bersihkan")');
    await expect(page.locator('#search')).toHaveValue('');
    await expect(page.locator('#category')).toHaveValue('');
    await expect(page.locator('#price_min')).toHaveValue('');
    await expect(page.locator('#price_max')).toHaveValue('');
  });
});
