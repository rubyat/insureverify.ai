# Improved Media Module (Path-agnostic Guide)

A complete, implementation-ready overview of a Media module you can port to any Laravel (or similar) project without relying on repository-specific file paths.


## 1) What It Provides

- Upload files (images/docs/video/audio), store metadata, and organize into folders.
- Browse/search/select via an image/file picker modal for use in forms and rich text editors.
- Generate driver-aware URLs for originals and resized images (thumb/medium/large).
- Serve private files through authenticated streaming endpoints.
- Optional image editing (crop/resize/rotate) and versioning.


## 2) Data Model (Schema You Can Recreate)

- media_folders
  - id (PK), name (string), parent_id (nullable FK), user_id (nullable FK)
  - created_at, updated_at
  - unique: (parent_id, name)

- media_files
  - id (PK)
  - file_name (base name without extension)
  - file_path (path including extension, relative within storage)
  - file_size (bytes), file_type (ext/logical), file_extension (ext), mime (MIME type)
  - width, height (for images; 0 for non-images)
  - driver (storage disk key, e.g., uploads, s3, gcs)
  - folder_id (nullable FK), user_id (nullable FK), author_id (nullable FK)
  - is_private (boolean), file_edit (text/JSON of edit metadata), origin (optional)
  - deleted_at (soft delete), created_at, updated_at

Types: use VARCHAR for names/mime/driver; BIGINT for ids/size; INT for width/height; TINYINT(1) for booleans; TEXT for paths/edits.


## 3) Upload Lifecycle (Implementation Pattern)

1. Validate input file (allow-list types, max size, optional dimension rules).
2. Pick storage driver (configurable). Treat your public-local driver differently from cloud to set visibility correctly.
3. Compute destination path:
   - Per-user partitioning and date-based subfolders are common (e.g., user shards + yyyy/mm/dd).
4. Generate a slugged base name, ensure uniqueness by appending numeric suffix if a conflict exists.
5. Store the file on the chosen driver.
6. If image, detect width/height via an image library (e.g., getimagesize/Intervention).
7. Create media_files record capturing all metadata listed above.
8. Return JSON with media ID and a canonical URL (or enough info to derive it).


## 4) URLs, Privacy, and Delivery

- Public files: return direct URLs from the public bucket/disk.
- Private files: return a route to a controller that enforces auth/permissions and streams bytes from storage; do not expose raw storage URLs.
- Provide a helper/service that, given a media object, returns:
  - Original URL
  - Resized URLs (see below)
  - Driver-aware behavior (uploads/local vs. s3/gcs)


## 5) Resizing Strategy (On-Demand & Cached)

- Define presets: thumb, medium, large (and custom as needed).
- On first request of a preset, generate the resized asset and store it (or cache it in a derived path). Subsequent requests serve cached versions.
- Libraries: GD/Imagick/Intervention Image.
- Optionally build srcset helpers for responsive images.


## 6) Folders, Listing, and Search

- Folders: CRUD with name uniqueness scoped to each parent. Optional per-user/tenant scoping.
- Listing APIs: filter by folder_id, type (image/video/doc), search term, date range; support pagination.
- Enforce permissions on create/upload/edit/delete.


## 7) Image Editing (Optional)

- Operations: crop, resize, rotate.
- Persistence options:
  - Create a new physical file variant and point the record to it; or
  - Store edit JSON in file_edit and apply edits dynamically (heavier, more complex caching).
- Keep originals if needed for non-destructive workflows.


## 8) The Image/File Picker Modal (UX Contract)

- Capabilities:
  - Search/filter, grid/list view, folder navigation, drag-and-drop upload
  - Single or multi-select with clear selection state
  - Pagination/infinite scroll
- Interaction pattern:
  - Open the modal programmatically from your form/editor.
  - When the user confirms selection, the modal invokes a callback with selected media objects.
  - The caller updates form fields (e.g., hidden input with media ID) and preview UI.

Example integration (framework-agnostic pseudocode):
```html
<input type="hidden" id="hero_image_id" name="hero_image_id">
<img id="hero_image_preview" style="display:none" />
<button type="button" id="pick_hero_image">Choose image</button>

<script>
  document.getElementById('pick_hero_image').addEventListener('click', function () {
    openMediaBrowser({
      multiple: false,
      filter: { type: 'image' },
      onSelect: function (items) {
        const item = items[0]; // {id, url, width, height, mime, ...}
        document.getElementById('hero_image_id').value = item.id;
        const img = document.getElementById('hero_image_preview');
        img.src = item.url; // or a thumbnail URL provided by your API
        img.style.display = 'block';
      }
    });
  });

  // Implement openMediaBrowser to render the modal, call your list/upload APIs,
  // and invoke onSelect(selectedItems) when the user confirms.
</script>
```


## 9) Minimal API Contract (Rename as Needed)

- POST /media/upload
  - Body: file, folder_id?, is_private?
  - Returns: { id, url, mime, width, height, folder_id, is_private }
- GET /media/list
  - Query: page, per_page, search, folder_id, type, date_range
  - Returns: paginated list of media objects
- POST /media/folders
  - Body: name, parent_id
- PATCH /media/folders/{id}
  - Body: name, parent_id?
- DELETE /media/folders/{id}
- GET /media/private/{id}
  - Auth required; streams the file if permitted
- POST /media/edit-image/{id}
  - Body: operations (crop/resize/rotate)


## 10) Implementation Notes

- Encapsulate uploading in a service/trait that handles validation, naming, storage, and metadata persistence.
- Centralize URL and resize logic in a helper/service that is driver-aware and privacy-aware.
- Enforce permissions via policies/gates or middleware consistently across upload, edit, delete, and folder actions.
- Offload heavy image jobs to background workers when possible.


## 11) Extensibility

- Add CDN integration and signed URLs.
- Watermarking and additional resize profiles.
- Extract and store EXIF (images) or duration/bitrate (audio/video).
- Multi-tenant scoping of folders/files.


## 12) Quick Checklist to Port This Module

- Create tables for media_files and media_folders using the schema above.
- Implement an upload service and a URL/resize helper.
- Build endpoints for upload, list, folders CRUD, private streaming, and (optional) image editing.
- Implement a reusable media browser modal that calls these endpoints and returns selected items.
- Wire the modal into your forms and editor integrations.
- Add permissions, tests, and background jobs as needed.
