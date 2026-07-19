---
name: laravel-e2e-testing
description: Panduan penulisan test end-to-end (E2E) Playwright dengan TypeScript untuk platform e-commerce TokoKita.
---

# Panduan Pengujian E2E Playwright - TokoKita

Panduan ini mendokumentasikan standar penulisan pengujian *end-to-end* (E2E) menggunakan **Playwright** dan **TypeScript** pada proyek TokoKita untuk memastikan semua alur bisnis berjalan dengan sempurna.

---

## 1. Struktur Folder Pengujian (`tests/e2e/`)
Pengujian E2E diletakkan secara terpisah di bawah direktori `tests/e2e/` dengan arsitektur sebagai berikut:

```text
tests/e2e/
├── fixtures/             # Fixtures kustom untuk otentikasi role-based
│   └── auth-fixtures.ts
├── helpers/              # Skrip helper (misal: penarik akun seeder)
│   └── accounts.ts
├── page-objects/         # Implementasi Page Object Model (POM)
│   ├── LoginPage.ts
│   └── DashboardPage.ts
└── modules/              # Kumpulan berkas pengetesan (.spec.ts)
    ├── admin/            # Tes modul Administrator
    ├── buyer/            # Tes modul Pembeli (e.g. keranjang, wishlist)
    └── seller/           # Tes modul Penjual (e.g. produk, voucher)
```

---

## 2. Konvensi Penamaan Berkas
*   **Page Object Model (POM)**: Menggunakan **PascalCase** berakhiran `Page.ts` (misal: `LoginPage.ts`, `ProductDetailPage.ts`).
*   **Berkas Spesifikasi Pengujian (.spec.ts)**: Menggunakan **kebab-case** dengan akhiran `.spec.ts` (misal: `seller-product-crud.spec.ts`, `buyer-cart-checkout.spec.ts`).

---

## 3. Helper Kredensial Akun Seeder
Untuk menjaga kestabilan data pengujian, buat berkas helper `tests/e2e/helpers/accounts.ts` yang mengembalikan kredensial statis sesuai data yang dipopulasikan oleh `DatabaseSeeder`:

```typescript
// tests/e2e/helpers/accounts.ts
export const ACCOUNTS = {
  admin: {
    email: 'admin@tokokita.com',
    password: 'password',
  },
  seller: {
    budi: { email: 'budi@tokokita.com', password: 'password' },
    ani: { email: 'ani@tokokita.com', password: 'password' },
    dedi: { email: 'dedi@tokokita.com', password: 'password' },
  },
  buyer: {
    wati: { email: 'wati@tokokita.com', password: 'password' },
    iwan: { email: 'iwan@tokokita.com', password: 'password' },
  }
};
```

---

## 4. Pola Page Object Model (POM)
Gunakan pola POM untuk memisahkan selektor elemen HTML dan interaksi halaman dari kode pengetesan inti agar mudah dipelihara.

### Contoh POM Login (`tests/e2e/page-objects/LoginPage.ts`):
```typescript
import { Page, expect } from '@playwright/test';

export class LoginPage {
  constructor(private readonly page: Page) {}

  async navigate() {
    await this.page.goto('/login');
  }

  async login(email: string, password: string) {
    await this.page.fill('input[name="email"]', email);
    await this.page.fill('input[name="password"]', password);
    await this.page.click('button[type="submit"]');
  }

  async verifySuccessRedirect(expectedPath: string = '/') {
    await expect(this.page).toHaveURL(new RegExp(expectedPath));
  }
}
```

---

## 5. Custom Fixture untuk Otentikasi Role-Based
Bandingkan dengan melakukan login manual di setiap file tes, buat fixture kustom di `tests/e2e/fixtures/auth-fixtures.ts` untuk menyediakan sesi halaman yang sudah ter-otentikasi secara otomatis:

```typescript
// tests/e2e/fixtures/auth-fixtures.ts
import { test as base, Page } from '@playwright/test';
import { LoginPage } from '../page-objects/LoginPage';
import { ACCOUNTS } from '../helpers/accounts';

type AuthFixtures = {
  buyerPage: Page;
  sellerPage: Page;
  adminPage: Page;
};

export const test = base.extend<AuthFixtures>({
  buyerPage: async ({ page }, use) => {
    const loginPage = new LoginPage(page);
    await loginPage.navigate();
    await loginPage.login(ACCOUNTS.buyer.wati.email, ACCOUNTS.buyer.wati.password);
    await loginPage.verifySuccessRedirect();
    await use(page);
  },

  sellerPage: async ({ page }, use) => {
    const loginPage = new LoginPage(page);
    await loginPage.navigate();
    await loginPage.login(ACCOUNTS.seller.budi.email, ACCOUNTS.seller.budi.password);
    await loginPage.verifySuccessRedirect('/seller/dashboard');
    await use(page);
  },

  adminPage: async ({ page }, use) => {
    const loginPage = new LoginPage(page);
    await loginPage.navigate();
    await loginPage.login(ACCOUNTS.admin.email, ACCOUNTS.admin.password);
    await loginPage.verifySuccessRedirect('/admin/dashboard');
    await use(page);
  },
});

export { expect } from '@playwright/test';
```

### Cara Penggunaan di Berkas Tes (`.spec.ts`):
```typescript
// tests/e2e/modules/seller/seller-dashboard.spec.ts
import { test, expect } from '../../fixtures/auth-fixtures';

test('Penjual dapat memantau halaman dashboard toko', async ({ sellerPage }) => {
  await expect(sellerPage.locator('h1')).toContainText('Dashboard Penjual');
  await expect(sellerPage.locator('.kpi-grid')).toBeVisible();
});
```

---

## 6. Perintah Eksekusi Pengujian (CLI Commands)

Berikut adalah ringkasan perintah CLI untuk menjalankan dan mengelola pengujian Playwright:

*   **Jalankan seluruh test E2E**:
    ```bash
    npx playwright test
    ```
*   **Jalankan satu file tes spesifik**:
    ```bash
    npx playwright test tests/e2e/modules/seller/seller-product-crud.spec.js
    ```
*   **Jalankan dengan tampilan browser terlihat (debugging visual)**:
    ```bash
    npx playwright test --headed
    ```
*   **Jalankan tes spesifik dalam mode debug interaktif**:
    ```bash
    npx playwright test --debug
    ```
*   **Buka laporan HTML setelah eksekusi selesai**:
    ```bash
    npx playwright show-report docs/testing/playwright-report
    ```
*   **Buka Trace Viewer untuk meninjau tes yang gagal**:
    ```bash
    npx playwright show-trace docs/testing/test-results/<test-folder-name>/trace.zip
    ```
```
