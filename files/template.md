
# 🚀 Template Builder System (Laravel + Inertia + Vue + TailwindCSS)

## Core Concepts

* **Page**

  * Represents a CMS entry with title, slug, status, featured image, optional WYSIWYG fallback content.
  * Each Page directly stores its **Template JSON** (no separate `template` table).
  * Pages include SEO fields.

* **Template JSON (Block Tree)**

  * Drag-and-drop structured layout composed of blocks.
  * Blocks define structure (`Row`, `Column`) or content (`Text`, `Hero`, `Card`).
  * JSON format is hierarchical: `{ id, type, model, nodes[] }`.

* **Block**

  * A PHP class (registered at runtime).
  * Provides metadata (`getOptions()`) to generate dynamic Vue forms.
  * Provides frontend rendering (`content(model)`), and preview rendering for the editor.
  * Supports both **container blocks** (Root, Row, Column) and **content blocks** (Text, Hero, etc.).

* **SEO**

  * Managed per-page (title, description, image, canonical URL).
  * Injected into frontend `<head>` section.

---

## Admin Workflow

1. **Create/Edit a Page**

   * Fill in title, slug, featured image, and SEO fields.
   * Open the **Visual Template Builder**.

2. **Template Builder (Inertia + Vue UI)**

   * Drag and drop blocks from a **palette** (Vue component).
   * Edit block settings via **auto-generated Vue forms** (based on `getOptions()` metadata).
   * Preview updates live inside an iframe or Vue preview component.
   * Save → JSON block tree is persisted on the Page.

3. **Frontend Rendering**

   * Controller decides:

     * If template JSON exists → render the block tree.
     * Otherwise → render fallback WYSIWYG content.
   * Rendering pipeline: depth-first traversal of JSON tree → call each block’s renderer → output Blade/Vue components.
   * SEO meta is injected dynamically.

---

## Database Schema

```sql
-- Pages (includes both page content and template JSON)
CREATE TABLE pages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(255) UNIQUE,
  title VARCHAR(255) NOT NULL,
  status TINYINT UNSIGNED DEFAULT 1,          -- 1: published, 0: draft
  short_desc TEXT NULL,
  image_id BIGINT UNSIGNED NULL,              -- media reference
  custom_logo BIGINT UNSIGNED NULL,           -- media reference
  header_style VARCHAR(100) NULL,
  show_template TINYINT UNSIGNED DEFAULT 1,   -- render template vs fallback content
  content LONGTEXT NULL,                      -- WYSIWYG fallback
  template JSON NULL,                         -- block tree { id, type, model, nodes[] }
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL,
  INDEX idx_pages_status (status)
);

-- SEO (per page, language-neutral here for simplicity)
CREATE TABLE seo (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  page_id BIGINT UNSIGNED NOT NULL,
  seo_title VARCHAR(255) NULL,
  seo_description TEXT NULL,
  seo_image_id BIGINT UNSIGNED NULL,          -- media reference
  canonical_url VARCHAR(500) NULL,
  meta_json JSON NULL,                        -- extra tags (og/twitter/etc.)
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_seo_page (page_id),
  CONSTRAINT fk_seo_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
);
```

---

## Vue + Tailwind UI (Inertia Frontend)

* **Page Editor**

  * Vue form for title, slug, featured image, SEO fields.
  * "Open Template Builder" button.

* **Template Builder (Vue Component)**

  * **Block Palette**: searchable list of registered blocks.
  * **Canvas/Tree View**: JSON visualized as nested Vue components.
  * **Block Settings Panel**: auto-generated form from block `options` metadata.
  * **Live Preview**: iframe or Vue-based preview of rendered block.

* **Styling**:

  * TailwindCSS for layouts, forms, drag-and-drop UI.
  * Grid utilities for Rows/Columns.
  * Reusable Vue components for Text, Hero, Card, etc.

---

## Extending the System

* **Add a new Block**

  * Create PHP class extending BlockBase.
  * Define `getOptions()` (form metadata).
  * Implement `content(model)` (frontend rendering).
  * Register it in a service provider.
  * Vue auto-discovers it into the block palette.

* **Themes**

  * Register additional blocks (e.g., Hero, FeatureGrid).
  * Hide/unregister blocks not needed for a theme.

---

## Quick Summary

* Pages include **both metadata and a JSON template**.
* JSON describes a **block tree** rendered by PHP + Vue.
* SEO integrated per page.
* Built for **Laravel + Inertia + Vue + TailwindCSS**.
* Extensible by adding new block classes and Vue components.

---

Do you also want me to **write the Vue/Inertia template-builder component structure** (with Tailwind layout) so you get a ready-to-code starting point?
