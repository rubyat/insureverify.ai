# Template Blocks System: Architecture, Lifecycle, and Blueprint (for Laravel + Inertia + Vue + Tailwind)

This document explains a reusable, framework-agnostic blocks system for building page templates with a live editor. It covers:

- Architecture and concepts
- Block lifecycle (load, preview, edit, save, render)
- Content JSON schema used by the editor and renderer
- Block option types and form generation
- Server endpoints and responsibilities
- A blueprint of available blocks (IDs, names, categories, settings, and special behavior)
- Guidance to implement the same approach in a standard Laravel + Inertia + Vue + Tailwind project (no modules system required)


## 1) Concepts and Architecture

- __Block__: A discrete, renderable UI section. Each block exposes:
  - getName: Human-readable name
  - getOptions: Configuration metadata (settings, categories, editor component hints)
  - content(model): Produces HTML/View for render using the current model
  - preview(model): Produces HTML snippet for the live preview frame
  - contentAPI(model) [optional]: Normalizes model data for API-driven rendering

- __Registry__: A map of block IDs to their implementors. Example shape:
  - root → Root container block (the page root)
  - text → Text block
  - call_to_action → CTA block
  - … and others

- __Template__: A JSON document describing a tree of nodes (blocks). Each node holds:
  - id: Node identifier
  - type: Block ID
  - model: Key/values for block settings
  - parent: Parent node id (or ROOT)
  - nodes: Array of child node ids (containers only)
  - version: Format version (on ROOT)

- __Live Editor__: Vue-powered UI offering:
  - Layers panel (tree of nodes)
  - Add Block panel (from registry, grouped by categories)
  - Block Form panel (dynamic schema from settings)
  - Preview frame (iframe that requests server-side preview/render)

- __Renderer__: Server renders template by walking the node tree from ROOT, instantiating blocks, and concatenating HTML from content(model). Container blocks render children at their position.


## 2) Lifecycle Overview

- __Load__
  - Fetch template JSON for a given template (optionally by locale).
  - Server may migrate legacy formats to the current version transparently.
  - Editor initializes with:
    - current_template_items: the JSON tree
    - template metadata (title, updated at)

- __List Blocks__
  - Client requests the block list endpoint.
  - Server builds registry → flattens to an array, grouped by category, with default model values and UI hints (component, container flags).

- __Add / Edit Block__
  - User selects a block. Editor reads the block’s getOptions().settings to generate a form.
  - Form value changes update the node’s model.
  - For container blocks, the editor allows adding children to the node’s nodes array.

- __Preview__
  - Editor posts block type + model to the preview endpoint.
  - Server looks up the block by type, calls preview(model) → returns HTML string for the block’s preview.

- __Save__
  - Editor posts the entire content JSON to the save endpoint.
  - Server normalizes and persists the JSON (keeping only whitelisted node keys: type, model, nodes, parent), returns lastSaved timestamp.

- __Render (Page View)__
  - When viewing the page, server loads the template, resolves the registry, sets the traversal context, and renders from ROOT using content(model) on each node.


## 3) Content JSON Schema (Editor and Renderer)

- __Shape__ (V2/V1.1+):
  - ROOT: Special node with:
    - type: "root"
    - nodes: [childNodeId, …]
    - version: "1.1"
  - Other nodes are keyed by their id and contain:
    - type: string (block id)
    - model: object (setting values for the block)
    - parent: string (parent node id)
    - nodes: array (only if the block is a container)

- __Migration__
  - When loading legacy arrays (V1), server transforms them to the keyed V2 layout with ROOT and parent attributes.

- __Filtering__
  - Before sending to the editor or preview, the server filters nodes:
    - Validates block types against the registry
    - Fills defaults from getOptions().settings into model
    - For preview mode, attaches preview HTML per node


## 4) Block Options and Dynamic Forms

- __Common settings types__ (drive the form UI):
  - input (inputType: text|number): Single-line text/number
  - textArea: Multi-line text (or WYSIWYG editor when type is editor)
  - editor: Rich text editor
  - uploader: Upload field returning a file id/reference
  - radios: Single-choice variants (label + values)
  - checklist: Multi-select checklist (values array)
  - listItem: A repeatable list of sub-settings; each item has its own settings schema
  - spacing: Spacing control (e.g., padding/margins)
  - size: Responsive size (for grid/columns)
  - checkbox: Boolean flag

- __Container hints__
  - is_container: true → the block can hold children
  - parent_of / child_of: Allowed structure rules
  - component: UI hint for the editor (e.g., RowBlock, ColumnBlock, RegularBlock)

- __Default values__
  - When building the block catalog for the editor, the server compiles defaults from each setting (std or type-specific defaults like [] for listItem).


## 5) Server Endpoints (suggested for Laravel + Inertia)

- __GET /admin/blocks__
  - Returns grouped blocks: { category: { name, open, items: [...] } }
  - Each item: { id, name, category, model (defaults), is_container?, component?, parent_of?, child_of?, settings (schema) }

- __GET /admin/templates/{id}/live__
  - Returns the Inertia/Vue live editor page with serialized template JSON for the selected language.

- __POST /admin/templates/preview__
  - Body: { block: string, model: object }
  - Returns: { preview: string }

- __POST /admin/templates__ (create/update)
  - Body: { id?, content: JSON, lang? }
  - Saves JSON; returns lastSaved timestamp and (optionally) redirect URL for new templates.

- __GET /templates/{id} (render)__
  - Server-side render path to output the full HTML processed content.


## 6) Rendering Algorithm (Server-side)

- Resolve the registry (blocks map).
- Parse template JSON; ensure ROOT + version exist.
- Prepare a traversal context for child resolution.
- Start from ROOT:
  - Instantiate the ROOT block
  - Set the node id on the instance (for child resolution)
  - Call content(model) and return the final HTML string
- Container blocks should call something like children() at the position where their children must be injected.


## 7) Implementation Guidance (Laravel + Inertia + Vue + Tailwind)

- __Registry__
  - Create a service or static class that registers blocks by ID.
  - Provide a way to programmatically register theme- or app-specific blocks and to hide/override some IDs if needed.

- __Blocks__
  - Implement each block as a PHP class exposing getName, getOptions, content, preview, and an optional contentAPI.
  - If you prefer full SPA rendering, you can mirror blocks in Vue and generate HTML client-side, but the server endpoints above are proven for WYSIWYG preview fidelity.

- __Editor UI__
  - Build a Vue SPA screen with three panels (layers, canvas/iframe preview, form panel) and a header for language switching, preview, and save.
  - Use Tailwind for styling and utility-first layout. The preview can be an iframe pointing to a preview route, or directly render server responses in a sandboxed container.

- __Asset fields__
  - Uploader fields return a file ID. Server-side content() resolves URLs for rendering (e.g., to full-size URLs). For client-only rendering, store full URLs in model at save time.

- __Data-driven blocks__
  - Some blocks rely on data (locations, categories, lists). Replace these with API calls in content() or pre-hydrate models before rendering in SPA.


## 8) Block Blueprint (IDs, Names, Categories, Settings, Behavior)

Below is a consolidated blueprint of blocks. “Settings” lists key fields only (not exhaustive). Use these to build your form schemas. Categories group blocks in the editor.

- __root__
  - Name: Root
  - Category: Container
  - Behavior: Container. Renders children only.
  - Settings: None

- __text__
  - Name: Text
  - Category: Other Block
  - Settings:
    - content (editor)
    - class (input text)
    - padding (spacing)
  - Behavior: Renders rich text; padding/class applied to wrapper.

- __call_to_action__
  - Name: Call To Action
  - Category: Other Block
  - Settings:
    - title (text)
    - sub_title (text)
    - link_title (text)
    - link_more (text)
    - style (radios: normal, style_2, style_3)
    - bg_color (text, hex)
    - bg_image (uploader)
  - Behavior: CTA section with optional background color/image and style variants.

- __video_player__
  - Name: Video Player
  - Category: Other Block
  - Settings:
    - title (text)
    - youtube (text URL)
    - bg_image (uploader)
  - Behavior: Video section; sets a per-render unique id.

- __faqs__
  - Name: FAQ List
  - Category: Other Block
  - Settings:
    - style (radios: style_1, style_2)
    - title (text)
    - list_item (listItem: title, sub_title/editor)
  - Behavior: Two layout variants for FAQs.

- __testimonial__
  - Name: List Testimonial
  - Category: Other Block
  - Settings:
    - title (text)
    - list_item (listItem: name, desc, number_star, avatar/uploader)
  - Behavior: Testimonial carousel/list; resolves avatar URLs on render.

- __mobile_qrcode__
  - Name: Mobile QR Code Block
  - Category: Other Block
  - Settings:
    - title (text)
    - qr_code_image (uploader)
    - link (text)
    - link_text (text)
  - Behavior: QR CTA; resolves image URL.

- __how_it_works__
  - Name: How It Works
  - Category: Other Block
  - Settings:
    - title (text)
    - list_item (listItem: title, sub_title/textArea, icon_image/uploader, order/number)
    - background_image (uploader)
  - Behavior: Steps/process list; resolves icon/background images.

- __site_map_2__
  - Name: Site Map Block
  - Category: Other Block
  - Settings:
    - title (text)
    - style (radios: style_1, style_2)
    - list_item (listItem: title, link_listing/textArea)
  - Behavior: Two layout variants; lists navigational links.

- __custom_links__
  - Name: Custom Links
  - Category: Other Block
  - Settings:
    - title (text)
    - list_item (listItem: title, link)
  - Behavior: Simple titled link list.

- __featured_image__
  - Name: Featured Image
  - Category: Other Block
  - Settings:
    - title (text)
    - image_id (uploader)
    - image_tag (text)
  - Behavior: Displays a single image with optional title/tag; resolves image URL.


- __row__ (pattern, optional container)
  - Name: Section
  - Category: Layout
  - is_container: true
  - parent_of: [column]
  - component: RowBlock
  - Settings: [] (usually styling could be added)

- __column__ (pattern, optional container)
  - Name: Column
  - Category: Layout
  - is_container: true
  - child_of: [row]
  - component: ColumnBlock
  - Settings:
    - size (size control; responsive column sizes)


## 9) Form Generation Notes

- The editor can auto-generate forms by reading settings:
  - id: field key in model
  - type: control type (input, textArea, editor, uploader, radios, checklist, listItem, spacing, size, checkbox)
  - inputType: for input (text|number)
  - values: for radios/checklist
  - settings: for listItem sub-fields
  - tab: optional grouping into tabs (e.g., content vs style)

- For listItem, the editor should provide add/remove/reorder and reflect a list of objects, each mapped by the nested settings schema.


## 10) Tailwind/Styling Guidance

- Blocks that expose class or padding allow Tailwind utility classes or spacing maps.
- Keep block HTML semantic and utility-first; avoid hardcoded CSS where a Tailwind class is appropriate.
- Consider component-level slots if you later migrate rendering to Vue components.


## 11) Data Hydration Patterns

- Some blocks load taxonomies or locations. Recommended approaches:
  - Hydrate server-side in content(model) and embed necessary lists
  - Or, return minimal structure and let the Vue component fetch data via API before mounting the search UI

- For images, store IDs and resolve URLs in content() or store the resolved URL in model at save-time if you prefer fully client-driven render.


## 12) Error Handling and Validation

- When saving templates, validate that content is present and node types exist in the registry.
- For preview, require block type and validate that the class exists with a preview method.
- Return structured errors for the editor to surface to users.


## 13) Internationalization (i18n)

- Keep translations per template translation. The editor can switch languages and save per-locale content.
- On load, server selects the content JSON for the requested locale; on save, it updates the correct translation entry.


## 14) Example Editor-State Variables (client)

- current_template_items: JSON tree of nodes
- current_template_title: string
- current_last_saved: datetime string
- template_id: number
- current_menu_lang: locale string
- template_i18n: map of UI strings used by the editor


## 15) Putting It Together (Minimal Steps)

1) Create a registry service that returns a map of block IDs to classes.
2) Implement block classes with getName, getOptions, content, preview.
3) Build endpoints: list blocks, preview block, save template, and view template.
4) Build the Vue editor:
   - Left: Layers tree + Add Block drawer (grouped by category)
   - Center: Preview frame or server-rendered HTML pane
   - Right: Dynamic form generated from block settings
5) Render templates on the public page by walking the node tree and delegating to blocks’ content() with container support.


---

This blueprint is complete and independent. You can directly adapt it for a standard Laravel + Inertia + Vue + Tailwind stack without a modules system.
