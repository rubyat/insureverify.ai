# FYVCO Page & Template System: Architecture, Page Builder, Blocks, and SEO

This document explains how the Page and Template modules work together in the dashboard and frontend, including the drag-and-drop builder, block system, SEO, and how to extend or reuse blocks.


## High-level Overview

- __Page vs Template__
  - `Modules\Page\Models\Page` represents a CMS page (title, content, slug, status, etc.).
  - `Modules\Template\Models\Template` is a visual layout composed of blocks (drag-and-drop builder). A `Page` can link to a `Template` (`page.template_id`).
- __Admin flow__
  - Create/Edit Pages: `PageController@create|edit` loads available templates and SEO form.
  - Open Builder: From a Page edit screen, "Template Builder" links to the live editor. If the page has no template yet, one is created and attached automatically.
- __Frontend rendering__
  - If a page has a template, the template JSON is processed into HTML by the block system and injected into the page view.
- __SEO__
  - `Page` integrates with the SEO system and includes a generic SEO meta form in admin. SEO metadata is rendered on the frontend using data from the model/translation.


## Data Models and Relationships

- __Page model__: `modules/Page/Models/Page.php`
  - Table: `core_pages`
  - Fillable: `title, content, status, short_desc, image_id, header_style, custom_logo`
  - Translatable attributes: `title, content, short_desc`
  - SEO type: `page` (`$seo_type = 'page'`)
  - Relationship: `template()` → `hasOne("\\Modules\\Template\\Models\\Template", 'id', 'template_id')`
  - Rendering: `getProcessedContent()` delegates to the attached `Template` translation (`TemplateTranslation::getProcessedContent()`)
  - Caching: clears `page_detail_data_{slug}` and `home_page_data_{id}` on save/delete
  - Clone support: `saveCloneByID()` duplicates page, its template (if any), translations, and SEO rows

- __Template model__: `modules/Template/Models/Template.php`
  - Table: `core_templates`
  - Fillable: `title, content, type_id`
  - Translation class: `TemplateTranslation`
  - Key computed accessors:
    - `content_json`: returns the JSON structure (auto-migrates v1→v2 and injects preview/meta)
    - `content_live_json`: JSON tailored for live preview
  - Block registry and discovery:
    - `getAllBlocks()` collects blocks from config, active modules, plugins, custom providers, and the current theme (`ThemeManager::currentProvider()`)
    - `Template::register($map)` allows themes/modules to inject or override blocks
  - Rendering pipeline:
    - `getProcessedContent()` walks the JSON tree and calls `content()` on each block
    - `getPreview($type, $model)` renders a single block for the live editor

- __TemplateTranslation__: `modules/Template/Models/TemplateTranslation.php`
  - Table: `core_template_translations`
  - Fillable: `title, content`


## Routes and Providers

- __Page frontend routes__: `modules/Page/Routes/web.php`
  - Prefix: `config('page.page_route_prefix')`
  - `/{slug?}` → `Modules\Page\Controllers\PageController@detail` (renders either template or classic content)

- __Page admin routes__: `modules/Page/Routes/admin.php`
  - `GET /edit/{id}` → edit page
  - `GET /builder/{id}` → `PageController@toBuilder` ensures a `Template` exists and redirects to live editor
  - `POST /store/{id}` → save page and SEO

- __Template admin routes__: `modules/Template/Routes/admin.php`
  - `GET /live/{template}` → `LiveEditorController@index` (live editor shell)
  - `POST /store` → `TemplateController@store` (save template JSON)
  - `GET /getBlocks` → fetch grouped/parsed block definitions
  - Import/Export helpers

- __Template preview route__: `modules/Template/Routes/web.php`
  - `GET template/preview/{template}` → `LiveController@preview` (iframe preview)

- __Service providers__
  - `modules/Page/Providers/RouterServiceProvider.php` and `modules/Template/RouterServiceProvider.php` wire web/admin/lang routes
  - `modules/Page/ModuleProvider.php` registers config and sitemap
  - `themes/GoTrip/Page/ModuleProvider.php` hooks into `Hook::PAGE_BEFORE_SAVING` to persist theme-specific page options (e.g., footer style)
  - `themes/GoTrip/Template/ModuleProvider.php` registers theme-specific blocks via `Template::register()`


## Admin UI: Classic Page Edit vs. Template Builder

- __Classic Page Edit__: `modules/Page/Views/admin/detail.blade.php`
  - Fields: Title, WYSIWYG Content, Feature Image, Logo, Status
  - SEO: includes `Modules/Core/Views/admin/seo-meta/seo-meta.blade.php`
  - "Template Builder" button appears when the page is created, linking to `route('page.admin.builder', ['id' => $row->id])`

- __Template Builder (List-based)__: `modules/Template/Views/admin/detail.blade.php`
  - Vue app `#booking-core-template-detail` backed by `public/themes/admin/module/template/admin/detail.js`
  - Left panel lists searchable blocks (grouped by category)
  - Right panel is a draggable list of blocks
  - Clicking a block opens a modal form powered by `vue-form-generator` using each block’s `settings`
  - Save triggers `POST /module/template/store` with `content` as JSON

- __Live Editor (iframe-based)__: `modules/Template/Views/admin/live/index.blade.php`
  - Vue app `#live-editor` backed by `public/themes/admin/module/template/admin/live/index.js`
  - Left: layer tree and add-block palette. Middle: iframe preview. Right: dynamic block settings form
  - Uses `window.postMessage` to communicate with the iframe for selection, add/delete, sort, and save actions
  - Save posts to `POST /module/template/store` and shows "Last saved" time


## Template JSON Structure (V2)

- `Template::convertToV2()` normalizes content to a graph with a `ROOT` node and child node references
- Each node: `{ id, type, model, nodes[], parent, component, name }`
- `Template::filterContentJson()` resolves block metadata:
  - Validates block types against the registry
  - Injects default `model` based on `settings.std` of each block
  - Adds `preview` HTML for live mode


## Block System

- __Base class__: `modules/Template/Blocks/BaseBlock.php`
  - `getOptions()` returns settings: tabs, fields, category, container info, component, defaults, etc.
  - `content($model)` returns a Blade view or HTML
  - `preview($model)` renders content for live preview
  - `children()` renders child nodes of container blocks

- __Core container blocks__ (example classes):
  - `RootBlock`: renders `children()` for ROOT
  - `Row`: container representing a section
  - `Column`: child of row; may define grid size in `settings`

- __Example content block__: `Text`
  - Options define editor field and style settings (padding, class)
  - `content()` returns `Template::frontend.blocks.text` view

- __Where blocks come from__
  - `Modules/Template/ModuleProvider::getTemplateBlocks()` base registry
  - Theme overrides or additional blocks via `themes/GoTrip/Template/ModuleProvider::getTemplateBlocks()` then `Template::register([...])`
  - Resulting registry is composed in `Template::getAllBlocks()` from core, modules, plugins, custom, and current theme

- __How to add a new reusable block__
  1. Create a class `Modules\Template\Blocks\MyBlock` extending `BaseBlock`
  2. Implement `getName()`, `getOptions()` (define `settings`, `category`, `component`, etc.), and `content($model)` (return a Blade view)
  3. Add a Blade view under `modules/Template/Views/frontend/blocks/my-block/index.blade.php` (or in theme namespace)
  4. Register the block id → class in a service provider:
     - Core: add to `Modules/Template/ModuleProvider::getTemplateBlocks()`
     - Or theme: in `themes/GoTrip/Template/ModuleProvider::boot()`, call `Template::register(['my_block' => \Modules\Template\Blocks\MyBlock::class])`
  5. The block automatically appears in the builder palette (grouped by `category`)


## Reusing Blocks

- Blocks are inherently reusable because the registry maps ids to classes
- In the builder, you can add the same block multiple times with different `model` data
- To share configurations:
  - Export a template (`GET /module/template/exportTemplate/{id}`) and import into another site
  - Or duplicate a page; cloning a `Page` also clones its `Template` and translations


## Frontend Rendering

- __Page frontend__: `modules/Page/Views/frontend/detail.blade.php`
  - If `page.template_id` exists: `{{ $row->getProcessedContent() }}` outputs the built HTML
  - Else: renders classic WYSIWYG `content`

- __Template rendering pipeline__
  1. `Page->getProcessedContent()` calls the attached `TemplateTranslation->getProcessedContent()`
  2. `Template->getProcessedContent()` loads block registry, parses JSON, prepares `BaseBlock::$_blocksToRenders`/`$_allBlocks`
  3. Walks from `ROOT` and calls each block’s `content($model)`; container blocks use `children()` to render descendants


## SEO Integration

- __Admin SEO meta__: Page edit includes `@include('Core::admin/seo-meta/seo-meta')`
- __Frontend SEO__: In `PageController@detail`, `seo_meta` is built via `getSeoMetaWithTranslation()` and injected into the page view context
- __Cloning__: `Page::saveCloneByID()` duplicates SEO rows for both origin and translations (via `Modules\Core\Models\SEO`)
- __Sitemap__: `modules/Page/ModuleProvider.php` wires pages into sitemap via `SitemapHelper`


## Dashboard → Live Builder Flow

- From `Page` admin: click "Template Builder" (`route('page.admin.builder')`)
- `PageController@toBuilder($id)`:
  - Ensures the `Page` has a `Template` (creates one titled as the page if missing)
  - Sets `show_template = 1` and saves the page
  - Redirects to `route('template.admin.live.index', ['template' => $template, 'ref' => 'page', 'refId' => $id])`
- `LiveEditorController@index` resolves reference links (back to page edit, and frontend preview URL)


## Theme-specific Behavior (GoTrip)

- __Page save hook__: `themes/GoTrip/Page/ModuleProvider.php`
  - Listens to `Hook::PAGE_BEFORE_SAVING` to persist theme fields like `footer_style` and `disable_subscribe_default`
- __Blocks__: `themes/GoTrip/Template/ModuleProvider.php`
  - Registers and hides certain blocks for the theme
  - Adds theme-specific blocks like `list_all_service`, `subscribe`, `download_app`, `login_register`, `text_image`, `offer_block`, `mobile_qrcode`, etc.


## Practical Recipes

- __Attach a template to a page__
  1. Create or edit a page in admin
  2. Click "Template Builder" and build the layout using blocks; saving writes `Template.content` JSON
  3. The page now renders the template on the frontend

- __Add custom SEO for a page__
  - Open the page in admin and fill the SEO form partial
  - The frontend `PageController@detail` passes `seo_meta` to the Blade layout to output appropriate tags

- __Create a new block__
  - Implement class + view + register in provider
  - Define `settings` in `getOptions()` to expose fields in the builder sidebar/modal

- __Reuse a block configuration__
  - Add the same block multiple times with different `model`
  - Export the template and import it on another environment/site


## Key Files to Review

- __Page Admin & Frontend__
  - `modules/Page/Admin/PageController.php`
  - `modules/Page/Controllers/PageController.php`
  - `modules/Page/Views/admin/detail.blade.php`
  - `modules/Page/Views/frontend/detail.blade.php`
  - `modules/Page/Routes/admin.php`, `modules/Page/Routes/web.php`

- __Template Model & Builder__
  - `modules/Template/Models/Template.php`, `TemplateTranslation.php`
  - `modules/Template/Admin/TemplateController.php`
  - `modules/Template/Admin/LiveEditorController.php`
  - `modules/Template/Views/admin/detail.blade.php`
  - `modules/Template/Views/admin/live/index.blade.php`
  - `public/themes/admin/module/template/admin/detail.js`
  - `public/themes/admin/module/template/admin/live/index.js`

- __Blocks__
  - `modules/Template/Blocks/` (core blocks like `BaseBlock`, `RootBlock`, `Row`, `Column`, `Text`, etc.)
  - Theme blocks: `themes/GoTrip/Template/Blocks/`
  - Block templates: `modules/Template/Views/frontend/blocks/...` and theme equivalents


## Notes and Gotchas

- __Content versioning__: Templates migrate automatically to V2 structure; future changes may add more migrations
- __Caching__: Page detail payload is cached for 6 hours by slug; saving a page clears caches
- __Permissions__: Saving templates/pages requires proper permissions (`template_create`, `template_update`, `page_*`)
- __Localization__: Template content and title are translatable; live editor shows a language nav


## Extending Further

- Add new block categories by returning `category` in `getOptions()` and group logic in `TemplateController@getBlocks()` will auto-group
- Build complex blocks with nested children by setting `is_container: true` and using `children()`
- Implement API rendering via `contentAPI()` in a block class and consume with `Template::getProcessedContentAPI()`
