import { expect } from '@playwright/test';

export class LoginPage {
  constructor(page) {
    this.page = page;
  }

  async navigate() {
    await this.page.goto('/login');
  }

  async login(email, password) {
    await this.page.fill('input[name="email"]', email);
    await this.page.fill('input[name="password"]', password);
    await this.page.click('main form button[type="submit"]');
  }

  async verifySuccessRedirect(expectedPath = '/') {
    await expect(this.page).toHaveURL(new RegExp(expectedPath));
  }
}
