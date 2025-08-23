# Media Module: Architecture, Image Picker, and Database Schema

This document explains the Media module so you can replicate it in another Laravel project. It covers the upload flow, folder and file management, the Vue-powered image picker modal, storage and resizing, permissions, and the database schema.


## Overview

- **Module location:** `modules/Media/`
- **Key parts:**
  - **Controllers:** `modules/Media/Controllers/MediaController.php`, `modules/Media/Controllers/FolderController.php`
  - **Admin controller:** `modules/Media/Admin/MediaController.php`
  - **Trait (upload):** `modules/Media/Traits/HasUpload.php`
  - **Models:** `modules/Media/Models/MediaFile.php`, `modules/Media/Models/MediaFolder.php`
  - **Helper:** `modules/Media/Helpers/FileHelper.php`
  - **Routes:** `modules/Media/Routes/admin.php`, `modules/Media/Routes/web.php`
  - **Views:** `modules/Media/Views/admin/index.blade.php`, `modules/Media/Views/browser.blade.php`, `modules/Media/Views/ckeditor.blade.php`
  - **Migrations:**
    - `database/migrations/2019_03_17_140539_create_media_files_table.php`
    - `modules/Media/Database/Migrations/2022_07_13_082318_create_media_folder_table.php`
    - `database/migrations/2023_01_05_095322_update_core_to_300.php`
    - `database/migrations/2023_09_26_154450_update_core_to_350.php`


## Upload and Validation Flow

- **Entry points:** Admin and web routes post uploads to media endpoints defined in `modules/Media/Routes/*`.
- **Core logic:** `HasUpload::uploadSingleFile(UploadedFile $file, $folder_id = 0)`
  - Determines filesystem driver via `config('filesystems.default', 'uploads')`. `local` is treated as `uploads` for public storage.
  - Builds a per-user, date-based folder path: `000X/USER_ID/YYYY/MM/DD`.
  - Generates a unique, slugged filename; avoids collisions by incrementing a suffix.
  - Stores the file using `Storage::disk($driver)` and `storePubliclyAs` for local-like drivers; `storeAs` for cloud drivers.
  - Extracts image dimensions via `getimagesize()` when MIME is an image (checked by `FileHelper::checkMimeIsImage`).
  - Creates a `MediaFile` record with metadata (name, path, size, mime, width/height, user info, folder, privacy, driver).
- **Validation:** Controllers apply Laravel validation rules and custom checks (file type, size, image dimensions where required). Errors returned with JSON helpers.
- **Privacy:** `is_private` flag controls whether files are served directly or via a signed/authenticated route that streams the file from storage.


## Folder and File Management

- **Folders:** Managed by `FolderController` with CRUD routes in `modules/Media/Routes/web.php`.
  - Unique constraint on `(parent_id, name)` prevents duplicates within the same parent.
  - Folder tree is user-scoped when needed (see `MediaFolder` scopes).
- **Files:** Managed by `MediaController` and admin controller for listing, searching, renaming (if supported), moving to folders, editing images, and deleting.
- **Permissions:** Menu and controller actions check permissions/authorization (see `modules/Media/ModuleProvider.php` admin menu permissions and controller gates/policies).


## Image Resizing, URLs, and Helpers

- **Helper:** `FileHelper` centralizes URL generation and resize logic.
- **Sizes:** Common presets like `thumb`, `medium`, `large` (and original). Resizes are generated on demand and cached.
- **Drivers:** Works with `uploads`/`local` and cloud drivers like S3/GCS. URL generation adapts to the current driver.
- **HTML:** Helpers can return image tags with proper `src` and `srcset` when needed.


## Private Files and Preview

- **Preview redirects:** `MediaController` has endpoints to handle preview/redirects to the appropriate URL (public) or to a controller action (private).
- **Private serving:** For `is_private = 1`, files are streamed via a controller that checks auth/permissions and reads from the configured disk.


## Image Editing

- **Route:** Exposed in `modules/Media/Routes/admin.php` and `modules/Media/Routes/web.php`.
- **Controller:** `MediaController` includes methods to crop/resize/update images with permission checks and metadata updates.


## Image Picker Modal (Vue) and Form Integration

- **View:** `modules/Media/Views/browser.blade.php` provides a reusable modal (used by admin and CKEditor).
- **UI features:**
  - Search, filters, and view toggles (grid/list)
  - Folder navigation and management (create/rename/delete where authorized)
  - Upload button with drag-and-drop support
  - Pagination and infinite scroll where available
  - Single/multi selection with clear selection feedback
- **How forms use it:**
  - Include the browser modal partial and its assets on pages needing an image/file picker.
  - Bind a form input (hidden or text) to the selected file's ID or URL.
  - The modal emits an event or invokes a callback with the selected media; the page script updates the form field and preview.
- **CKEditor integration:** `modules/Media/Views/ckeditor.blade.php` wires the modal to CKEditor dialogs so images/files can be inserted into rich text content.


## Routes Summary

- **Admin:** `modules/Media/Routes/admin.php` registers the admin media index, list APIs, and image edit endpoints under admin middleware/permissions.
- **Web/API:** `modules/Media/Routes/web.php` covers:
  - Private file viewing/streaming
  - Image editing endpoints (with auth)
  - Folder management CRUD (with auth)
  - Media list/search/upload APIs (with auth)


## Database Schema

The schema spans `media_files` and `media_folders`. Multiple migrations add columns over time; below is the consolidated model.

### Table: media_folders

- `id` BIGINT UNSIGNED PK
- `name` VARCHAR(191)
- `parent_id` BIGINT UNSIGNED NULL
- `user_id` BIGINT UNSIGNED NULL  — owner/creator
- `created_at` TIMESTAMP NULL
- `updated_at` TIMESTAMP NULL
- Unique index on `(parent_id, name)`

Example DDL:
```sql
CREATE TABLE `media_folders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `media_folders_parent_name_unique` (`parent_id`,`name`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Table: media_files

- `id` BIGINT UNSIGNED PK
- `file_name` VARCHAR(191) — name without extension
- `file_path` TEXT or VARCHAR — storage path including extension
- `file_size` BIGINT UNSIGNED — bytes
- `file_type` VARCHAR(50) — extension or logical type
- `mime` VARCHAR(100) — MIME type
- `driver` VARCHAR(50) — storage disk (e.g., uploads, s3)
- `file_extension` VARCHAR(10) — extension
- `width` INT — for images
- `height` INT — for images
- `folder_id` BIGINT UNSIGNED NULL — FK to `media_folders.id`
- `user_id` BIGINT UNSIGNED NULL — uploader/owner
- `author_id` BIGINT UNSIGNED NULL — optional separate author field (added later)
- `is_private` TINYINT(1) DEFAULT 0 — private files served via controller
- `file_edit` TEXT NULL — serialized image edit info (crop, etc.)
- `origin` VARCHAR(50) NULL — source indicator (optional, varies by codebase)
- Soft deletes: `deleted_at` TIMESTAMP NULL
- Timestamps: `created_at`, `updated_at`

Example DDL (consolidated from migrations):
```sql
CREATE TABLE `media_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(191) NOT NULL,
  `file_path` text NOT NULL,
  `file_size` bigint unsigned DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `mime` varchar(100) DEFAULT NULL,
  `driver` varchar(50) DEFAULT NULL,
  `file_extension` varchar(10) DEFAULT NULL,
  `width` int DEFAULT 0,
  `height` int DEFAULT 0,
  `folder_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `author_id` bigint unsigned DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `file_edit` text DEFAULT NULL,
  `origin` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_files_folder_id_index` (`folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Note: exact column names and types may vary slightly between migrations. Inspect the listed migrations for your environment.


## How to Reuse in Another Project

1. **Models and Tables**
   - Copy `MediaFile` and `MediaFolder` or re-implement with the same columns.
   - Run equivalent migrations shown above.

2. **Storage**
   - Set `filesystems.default` appropriately. Treat `local` as `uploads` for public files if you follow the same pattern.
   - Configure S3/GCS disks as needed.

3. **Upload Trait**
   - Port `HasUpload` and adapt namespace/imports.
   - Ensure controllers call `uploadSingleFile()` when handling `UploadedFile` instances.

4. **Helpers**
   - Add `FileHelper` for URL generation and resizing.
   - Implement resize presets and on-demand generation/caching.

5. **Controllers and Routes**
   - Wire routes from `modules/Media/Routes/*` into your project.
   - Implement private file streaming endpoints that honor `is_private`.

6. **Views and Modal**
   - Include `modules/Media/Views/browser.blade.php` and its assets on pages with image pickers.
   - Provide a JS hook/callback to receive the selected media (ID/URL) and update your form fields.

7. **Permissions**
   - Add gates/policies and admin menu entries similar to `modules/Media/ModuleProvider.php`.

8. **CKEditor**
   - Include `modules/Media/Views/ckeditor.blade.php` for rich text usage.

9. **Testing**
   - Test uploads for different file types, sizes, and dimensions.
   - Verify private file access is gated and URLs are correct per driver.


## Key References in This Codebase

- `modules/Media/Traits/HasUpload::uploadSingleFile()` — upload, naming, storage, metadata
- `modules/Media/Helpers/FileHelper` — URL building, resizing, image checks
- `modules/Media/Models/MediaFile` — attributes and accessors
- `modules/Media/Models/MediaFolder` — folder tree and scoping
- `modules/Media/Controllers/MediaController` — listing, private view, edit
- `modules/Media/Controllers/FolderController` — folder CRUD
- `modules/Media/Views/browser.blade.php` — Vue modal for picking files
- `modules/Media/Views/ckeditor.blade.php` — CKEditor integration
- `modules/Media/Routes/admin.php`, `modules/Media/Routes/web.php` — endpoints


## Notes and Extensibility

- Add custom resize profiles, watermarks, or CDN integration in `FileHelper`.
- Extend `MediaFile` with additional metadata (EXIF, duration for audio/video).
- Plug into your permission system to scope folders/files per role or tenant.
- Consider background jobs for heavy image processing instead of on-demand.
