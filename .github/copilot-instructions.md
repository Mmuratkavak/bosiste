# Copilot Instructions for Bosiste

## Project Overview
This is a **Laravel 12 + Filament 3 + Spatie Permission** multi-tenant business management application. The codebase implements a SaaS model where users (tenant owners) can manage their business profiles, with public-facing data separated from private tenant secrets.

## Architecture

### Core Models & Data Flow
- **User** (using Laravel auth + Spatie roles)
  - Has many `Tenant`s (business accounts)
  - Can have roles: `super_admin`, `business_owner`
  - Access control: `canAccessPanel()` checks ban status + roles
  
- **Tenant** (multi-tenant isolation boundary)
  - Owned by a User (`owner_id` FK)
  - Has `status` (pending|active) and `plan` (free|pro)
  - Has domain field for custom domains
  - Stores `feature_flags` as JSON array
  
- **BusinessProfile** (public-facing)
  - One-to-one with Tenant
  - Contains: name, slug (unique), description, location (JSON: lat/lng), logo_path
  
- **TenantSecret** (private data)
  - One-to-one with Tenant  
  - Contains: private_phone, contract_file, admin_notes
  - **Critical:** Always protect with authorization queries

### Key Relationships
```
User (1) → (M) Tenant
Tenant (1) → (1) BusinessProfile
Tenant (1) → (1) TenantSecret
```

## Developer Workflows

### Setup
```bash
composer setup  # Run once: installs, generates .env, generates key, migrates, builds npm
```

### Local Development
```bash
composer dev  # Runs concurrently: artisan serve, queue:listen, pail logs, npm run dev
```

### Testing
```bash
composer test  # Clears config and runs PHPUnit
```

### Asset Building
```bash
npm run dev     # Development with Vite + Tailwind v4
npm run build   # Production build
```

## Project-Specific Patterns

### Filament Admin Panel Structure
- Location: `app/Filament/Resources/` and `Pages/`, `Widgets/`
- AdminPanelProvider at `app/Providers/Filament/AdminPanelProvider.php` configures the dashboard
- Resources auto-discovered from `Filament/Resources` directory
- Currently has: `TenantResource`, `UserResource` with standard CRUD operations
- Navigation groups: 'Yönetim' (Management)
- Turkish language used throughout forms and labels

### Database Migrations Pattern
- Migrations check column existence before adding: `if (!Schema::hasColumn(...))` 
- **Important:** Use this pattern to handle repeated runs safely
- Enums used for status/plan fields
- JSON columns used for flexible data (feature_flags, location)

### Authorization Pattern
- **Tenant isolation:** Always query with tenant context via relationships
- **Role-based access:** Use Spatie `hasRole()` and `canAccessPanel()` method
- **Ban checking:** `User::is_banned` boolean prevents panel access
- **Email-based access:** `@visitgokceada.com` email gets automatic access

### Form/Table Structure (Filament Resources)
- Forms use `Forms\Components\Section` for logical grouping
- Turkish labels throughout (e.g., 'İşletme Sahibi', 'Temel Bilgiler')
- Relationships use `.relationship()` with searchable/preload
- Tables use standard Filament column helpers
- Unique constraints handled with `ignoreRecord: true` on edit

## Integration Points

### External Dependencies
- **Laravel 12:** Core framework with service providers in `config/app.php`
- **Filament 3.2:** Admin UI auto-discovery, form builder, table builder
- **Spatie Permission 6.24:** Role/permission management with pivot tables
- **Tailwind v4 + Vite:** Frontend bundling via `vite.config.js`
- **Laravel Tinker:** REPL for command line debugging
- **phpunit 11.5:** Testing framework with PSR-4 autoload for `Tests/` namespace

### Important Files
- `config/permission.php` - Role/permission table configuration
- `bootstrap/app.php` - Application binding and middleware setup
- `routes/web.php` - Public routes (minimal in this SaaS app)
- `phpunit.xml` - Test configuration with `.env.testing`

## Common Tasks

### Adding a New Filament Resource
1. Create `app/Filament/Resources/YourModelResource.php` implementing `Resource` interface
2. Define model class, navigation icon, and group
3. Implement `form()` and `table()` methods
4. Auto-discovered by AdminPanelProvider discovery mechanism
5. Add Turkish labels for UI consistency

### Querying Across Tenants Safely
```php
// Correct: scope to owner's tenants
$tenants = auth()->user()->tenants()->where('status', 'active');

// For admin: all tenants
$tenants = Tenant::query();
```

### Managing Feature Flags
```php
// feature_flags stored as JSON array
$tenant->feature_flags = ['feature_x' => true, 'feature_y' => false];
$tenant->save();

// Access with array methods
if ($tenant->feature_flags['feature_x'] ?? false) { ... }
```

### Running Migrations Safely
- Migrations check column existence before operations
- Safe to re-run: won't fail if columns already exist
- Use `if (!Schema::hasColumn())` pattern for conditional adds

## Testing Approach
- Unit tests in `tests/Unit/`
- Feature tests in `tests/Feature/`
- Use `TestCase` base class which extends Laravel's
- Run with `composer test` or `php artisan test`
- Config cleared before tests run via script

## Important Notes
- **Multi-tenant boundaries:** Always consider tenant isolation in data queries
- **Turkish localization:** UI text is in Turkish (not default English)
- **JSON data:** feature_flags and location are JSON columns - cast appropriately
- **Cascade deletes:** User deletion cascades to Tenants, Tenants to BusinessProfile/TenantSecret
- **Ban field:** Check `is_banned` in `canAccessPanel()` before allowing admin access
