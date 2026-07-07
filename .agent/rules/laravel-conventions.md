# Laravel Conventions for TokoKita

## Naming Conventions
- Models: PascalCase singular (User, Product, OrderItem)
- Controllers: PascalCase + Controller suffix (ProductController)
- Tables: snake_case plural (users, order_items)
- Columns: snake_case (created_at, user_id)
- Routes: kebab-case (product-categories, order-items)

## Code Standards
- Gunakan PHP 8.1+ features (enums, named arguments, match expression)
- Selalu gunakan Form Request untuk validasi input
- Gunakan Eloquent relationships, hindari query raw SQL
- Tambahkan PHPDoc pada setiap method public
- Gunakan Laravel conventions: fillable, casts, appends

## Security
- Jangan simpan credential di kode, gunakan .env
- Selalu gunakan CSRF protection pada form
- Gunakan policy untuk authorization, bukan middleware manual
