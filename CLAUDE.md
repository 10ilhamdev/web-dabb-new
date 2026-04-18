# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

ANRI Web DABB - A Laravel 12 CMS application for Indonesian National Archive (ANRI). Contains role-based dashboards, virtual room/content features, and a flexible CMS system.

## Common Commands

```bash
# Full dev environment (PHP server + queue + logs + Vite)
composer run dev

# Run tests
composer run test

# Vite dev server
npm run dev

# Production build
npm run build

# Laravel artisan
php artisan <command>
```

## Architecture

### Backend
- **Framework**: Laravel 12 with PHP 8.2+
- **Database**: MySQL (host.docker.internal:3306, database: dabb)
- **Auth**: Laravel Breeze + Google OAuth
- **Queue**: Database driver

### Frontend
- **Styling**: Tailwind CSS v3 with forms plugin
- **JS**: Alpine.js v3, page-flip (book rendering), html2canvas
- **Build**: Vite 7

### Key Patterns

**Route groups** use middleware `'role:role_name'` for role-based access:
- `admin` - Full CMS access
- `pegawai` - Employee dashboard
- `umum` - General public
- `pelajar_mahasiswa` - Student
- `instansi_swasta` - Private sector

**CMS Structure**:
- `Feature` → main feature (e.g., "Pameran", "Profil")
- `FeaturePage` → pages under a feature with optional `page_num`
- `FeaturePageSection` → content blocks within pages (text, image, etc.)

**Virtual Content Types**:
- `VirtualRoom` - 360 panoramic viewer
- `Virtual3dRoom` - 3D room with 4 walls + 1 door
- `Book` + `VirtualBookPage` - Digital flipbook
- `VirtualSlideshowPage` + `VirtualSlideshowSlide` - Virtual exhibition

**Profile Pages** (under Profil feature):
- `Profile` - Main profile page
- `ProfileSection` - Content sections
- Sub-menu items via `profile_sub` table

### Database Conventions

- MySQL with utf8mb4_unicode_ci collation
- DB credentials in `.env` (ilhamdev/ilhamdev for local)
- Use `host.docker.internal` as DB_HOST on Windows Docker

### API Integrations

- **Google OAuth**: Login via Google (credentials in .env)
- **Google Drive**: Stream files via `/gdrive-stream/{fileId}` using GOOGLE_DRIVE_API_KEY
- **Gemini**: Chat bot at `/api/chat` using GEMINI_API_KEY

### Important Files

- `routes/web.php` - Main routes (auth + CMS routes in group with 'auth' middleware)
- `app/Models/Feature.php` - Core feature model
- `app/Http/Controllers/FeaturePageController.php` - Public page rendering
- `resources/views/pages/` - Public view templates
- `resources/views/cms/` - CMS/backend templates

### Development Notes

- APP_PORT=8080 in `.env`
- SESSION_LIFETIME=120 minutes
- Use `php artisan make:model` + migration for new models
- Virtual content controllers are in `app/Http/Controllers/Cms/`