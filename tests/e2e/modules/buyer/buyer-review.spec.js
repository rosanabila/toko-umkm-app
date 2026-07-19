import { test, expect } from '@playwright/test';
import { LoginPage } from '../../page-objects/LoginPage.js';
import { execSync } from 'child_process';

test.describe('Buyer Product Review Flow', () => {

  test('Buyer can review a product from a completed order and verify it on details page', async ({ page }) => {
    // 1. Programmatically find a completed order item without a review using Artisan Tinker
    let email, orderId, orderItemId, productName, productSlug;
    try {
      const phpCode = `
        $item = \\App\\Models\\OrderItem::whereHas('order', function($q) { 
            $q->where('status', 'completed'); 
        })->whereDoesntHave('review')->with(['order.buyer', 'product'])->first();
        if ($item) {
            echo json_encode([
                'email' => $item->order->buyer->email,
                'order_id' => $item->order_id,
                'order_item_id' => $item->id,
                'product_name' => $item->product->name,
                'product_slug' => $item->product->slug
            ]);
        }
      `;
      // Clean up string representation for shell
      const command = `php artisan tinker --execute="${phpCode.replace(/"/g, '\\"').replace(/\n/g, ' ')}"`;
      const output = execSync(command).toString().trim();
      const parsed = JSON.parse(output);
      
      email = parsed.email;
      orderId = parsed.order_id;
      orderItemId = parsed.order_item_id;
      productName = parsed.product_name;
      productSlug = parsed.product_slug;
    } catch (e) {
      throw new Error('Gagal mengambil data pesanan selesai dari seeder: ' + e.message);
    }

    expect(email).toBeTruthy();
    expect(orderId).toBeTruthy();

    // 2. Login as the buyer who owns the completed order
    const loginPage = new LoginPage(page);
    await loginPage.navigate();
    await loginPage.login(email, 'password');
    await loginPage.verifySuccessRedirect();

    // 3. Navigate to the order detail page
    await page.goto(`/buyer/order/${orderId}`);
    await expect(page.locator('h3:has-text("Rincian Barang")')).toBeVisible();

    // 4. Click "Beri Ulasan" to trigger review form
    // Since there could be multiple products, click the specific one
    await page.click(`button[onclick*="showReviewForm(${orderItemId},"]`);
    await expect(page.locator('#review-panel')).toBeVisible();

    // 5. Select 5-star rating and fill comment
    await page.click('.review-star[data-rating="5"]');
    const commentText = 'Pengujian E2E Otomatis - Produk sangat berkualitas, sangat direkomendasikan! ' + Date.now();
    await page.fill('textarea[name="comment"]', commentText);

    // 6. Submit the review
    await page.click('main form button[type="submit"]');

    // Verify success flash alert
    await expect(page.locator('text=Ulasan Anda berhasil dikirim!')).toBeVisible();

    // 7. Verify review appears on the product detail page
    await page.goto(`/product/${productSlug}`);
    await expect(page.locator('h1')).toContainText(productName);
    
    // Check if the comment text is visible in the reviews section
    await expect(page.locator(`text=${commentText}`)).toBeVisible();
  });
});
