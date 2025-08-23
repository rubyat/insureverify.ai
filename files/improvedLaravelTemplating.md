# Improved Laravel Templating: Pages, Templates, Blocks, SEO, and DB Schema

This guide is self-contained. It explains the architecture and flow of a drag‑and‑drop page builder (Pages + Templates + Blocks), how SEO integrates, how rendering works, and provides a practical database schema.


## 1) Core Concepts

- **Page**: CMS entity with title, slug, status, and optional WYSIWYG content. A Page may reference a **Template** by `template_id`. Pages are translatable.
- **Template**: Visual layout defined by a JSON block tree. Templates and their content are translatable. Edited via a live, drag‑and‑drop builder.
- **Block**: A PHP class that declares metadata (options/settings), and renders HTML using the block's `model` data. Some blocks are containers (hold children), others are content/leaf nodes.
- **SEO**: Per‑page (and optionally per‑locale) metadata managed in admin and rendered in the frontend head.


## 2) Admin Workflow

1. **Create/Edit a Page**
   - Fill title, status, optional content and images.
   - Fill SEO fields (title/description/image/canonical, etc.).
   - Click "Template Builder" to build a visual layout.
2. **Open Template Builder**
   - If the Page has no Template, one is created and linked automatically.
   - Enter a live, iframe‑based editor with block palette, layers, and settings.
3. **Compose with Blocks**
   - Drag blocks from a searchable palette. Drop into the canvas/layer tree.
   - Select a block to edit settings via an auto‑generated form.
   - Save to persist the Template JSON.
4. **Frontend Result**
   - If a Template is linked, the Page renders the processed Template HTML.
   - Otherwise, it falls back to the Page’s classic WYSIWYG content.


## 3) Live Editor Mechanics

- **Block registry**: Collected from core, modules/plugins, and the active theme. Each block type maps to a PHP class.
- **Palette**: Blocks grouped by category (e.g., Layout, Content, Theme). Search supported.
- **Canvas/Layers**: The Template is a tree starting from a `ROOT` container node.
- **Settings Form**: Generated from each block’s `options.settings` (field types, defaults, labels).
- **Preview**: Editor posts the node `model` to a preview route; the block returns HTML for the iframe.
- **Save**: Persists full Template content as JSON (the node graph).


## 4) Template JSON (Version 2)

- **Node shape**: `{ id, type, model, nodes[], parent, component, name }`.
- **ROOT node**: The top container whose `nodes[]` are the top‑level blocks (commonly rows/sections).
- **model**: Arbitrary key‑value data for the block (e.g., text, classes, spacing).
- **Validation/enrichment**: On load/save, the system validates `type` against the registry, injects default `model` values from `options.settings`, and can add preview HTML.


## 5) Frontend Rendering Pipeline

1. **Page decides**: If the Page has `template_id`, render the Template; else render classic content.
2. **Template processing**:
   - Load the block registry.
   - Parse the Template JSON into a node tree.
   - Depth‑first from `ROOT`: for each node, find the block class by `type`, call `content(model)` to get HTML. Container blocks call `children()` to render nested nodes.
3. **SEO injection**: Controller prepares SEO meta (language‑aware when needed) and passes it to the layout head.


## 6) Block Design

- **Contract**
  - `getName()`: Label shown in the editor.
  - `getOptions()`: Metadata for the editor:
    - `category`, `icon`, `component` (UI hints)
    - `is_container` (whether it has children)
    - `settings` (fields schema, defaults)
  - `content(model)`: Returns HTML (often from a Blade view or template).
  - `preview(model)`: Optional, optimized HTML for live preview.
- **Containers**: e.g., `Root`, `Row`, `Column`. They output structure and call `children()` to render nested content.
- **Content blocks**: e.g., `Text`, `Hero`, `Card`. They output tangible content using `model` fields.
- **Registration**: Providers register block type → class. The final registry is the composition of core + modules + theme.
- **Reuse**: Add the same block type many times with different `model`. Export/Import entire Templates or duplicate a Page to clone its Template.


## 7) SEO Integration

- **Admin**: Page edit includes a standard SEO section (title, description, image, canonical, extra meta).
- **Frontend**: SEO meta computed per Page (and locale) and injected into the head.
- **Cloning**: Duplicating a Page clones its Template and associated SEO rows.
- **Sitemap**: Pages can be registered with the sitemap generator.


## 8) Extending the System

- **Create a new block**
  1. Create a PHP class extending the block base.
  2. Implement `getOptions()` with `category`, `is_container`, and `settings` (fields + defaults).
  3. Implement `content(model)` and a view (Blade or equivalent).
  4. Register the block in a provider so it appears in the editor palette.
- **Complex/nested blocks**: Mark as container via `is_container: true` and use `children()` to render nested nodes.
- **Localization**: Both Page and Template title/content are translatable. The live editor supports language switching.


## 9) Practical Recipes

- **Attach a Template to a Page**: Edit Page → click Template Builder → compose and Save → Page now renders the Template.
- **Add custom SEO**: Fill the SEO form on the Page; frontend head tags reflect those values.
- **Reuse a configuration**: Add the same block type multiple times with different `model`, or export/import the whole Template, or duplicate the Page.


## 10) Database Schema (MySQL)

The following schema matches the behavior described above. Adjust table names/columns to your conventions.

```sql
-- 1) Pages: translatable and optionally linked to a Template
CREATE TABLE core_pages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(255) UNIQUE,
  template_id BIGINT UNSIGNED NULL,
  status TINYINT UNSIGNED DEFAULT 1,          -- 1=published, 0=draft
  short_desc TEXT NULL,
  image_id BIGINT UNSIGNED NULL,              -- media reference
  custom_logo BIGINT UNSIGNED NULL,           -- media reference
  header_style VARCHAR(100) NULL,
  show_template TINYINT UNSIGNED DEFAULT 1,   -- render template vs classic content
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL,
  INDEX idx_pages_template (template_id),
  INDEX idx_pages_status (status)
);

-- 2) Page translations: title/content per locale
CREATE TABLE core_page_translations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  origin_id BIGINT UNSIGNED NOT NULL,         -- references core_pages.id
  locale VARCHAR(10) NOT NULL,                -- e.g., 'en', 'fr'
  title VARCHAR(255) NOT NULL,
  content LONGTEXT NULL,                      -- classic WYSIWYG fallback
  short_desc TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_page_locale (origin_id, locale),
  INDEX idx_page_trans_origin (origin_id),
  INDEX idx_page_trans_locale (locale)
);

-- 3) Templates: translatable JSON layout
CREATE TABLE core_templates (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type_id VARCHAR(100) NULL,                  -- optional categorization/type label
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL
);

-- 4) Template translations: title + content JSON per locale
CREATE TABLE core_template_translations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  origin_id BIGINT UNSIGNED NOT NULL,         -- references core_templates.id
  locale VARCHAR(10) NOT NULL,
  title VARCHAR(255) NOT NULL,
  content JSON NULL,                          -- V2 node graph: { id, type, model, nodes[], ... }
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_template_locale (origin_id, locale),
  INDEX idx_template_trans_origin (origin_id),
  INDEX idx_template_trans_locale (locale)
);

-- 5) SEO: generic table usable for multiple object types (including pages)
CREATE TABLE core_seo (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  object_model VARCHAR(100) NOT NULL,         -- e.g., 'page'
  object_id BIGINT UNSIGNED NOT NULL,         -- target object id (e.g., core_pages.id)
  locale VARCHAR(10) NULL,                    -- null=default/global, else per-locale
  seo_title VARCHAR(255) NULL,
  seo_description TEXT NULL,
  seo_image_id BIGINT UNSIGNED NULL,          -- media reference
  canonical_url VARCHAR(500) NULL,
  meta_json JSON NULL,                        -- extra tags (og/twitter/custom)
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX idx_seo_target (object_model, object_id),
  INDEX idx_seo_locale (locale)
);

-- Optional strict integrity (enable as needed)
-- ALTER TABLE core_pages
--   ADD CONSTRAINT fk_pages_template FOREIGN KEY (template_id) REFERENCES core_templates(id) ON DELETE SET NULL;
-- ALTER TABLE core_page_translations
--   ADD CONSTRAINT fk_page_trans_origin FOREIGN KEY (origin_id) REFERENCES core_pages(id) ON DELETE CASCADE;
-- ALTER TABLE core_template_translations
--   ADD CONSTRAINT fk_template_trans_origin FOREIGN KEY (origin_id) REFERENCES core_templates(id) ON DELETE CASCADE;
```


## 11) Quick Summary

- **Pages** reference **Templates**; Templates are JSON block trees.
- The **builder** edits that JSON via a registry of **Blocks**.
- **Rendering** walks the JSON and calls each block’s renderer, with containers rendering their children.
- **SEO** is per‑page (and optionally per‑locale) and injected into the frontend head.
- **Extensibility**: Add blocks by writing a class + view + provider registration; themes can add/hide blocks.
