import { test as base } from '@playwright/test';
import { LoginPage } from '../page-objects/LoginPage.js';
import { ACCOUNTS } from '../helpers/accounts.js';

export const test = base.extend({
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
