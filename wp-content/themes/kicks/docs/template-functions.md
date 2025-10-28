
- [`xd_register_archive_page( $post_type, $option_name = null )`](#xd_register_archive_page-post_type-option_name--null-)
	- [Parameters](#parameters)
	- [Returns](#returns)
	- [Functionality](#functionality)
	- [Admin UI Integration](#admin-ui-integration)
	- [Notes](#notes)
	- [Example](#example)
- [`xd_register_ui_page( $post_type, $type, $soft_route_enabled = false, $option_name = null )`](#xd_register_ui_page-post_type-type-soft_route_enabled--false-option_name--null-)
	- [Parameters](#parameters-1)
	- [Returns](#returns-1)
	- [Functionality](#functionality-1)
	- [Embedding Behavior](#embedding-behavior)
	- [Example](#example-1)
- [`xd_register_static_page( $args )`](#xd_register_static_page-args-)
	- [Parameters](#parameters-2)
		- [Accepted Keys](#accepted-keys)
	- [Returns](#returns-2)
	- [What It Does](#what-it-does)
	- [Example](#example-2)


## `xd_register_archive_page( $post_type, $option_name = null )`

Registers an archive page setting for a custom post type in the WordPress **Reading Settings** panel. This enables site administrators to designate a static page to act as the archive view for a custom post type (e.g., portfolio, case studies), with full integration into WordPress's admin UI, front-end routing, and rewrite system.

---

### Parameters

| Parameter      | Type     | Description                                                                 |
|----------------|----------|-----------------------------------------------------------------------------|
| `$post_type`   | `string` | The custom post type slug. This determines which post type the archive page setting is registered for. |
| `$option_name` | `string|null` | _(Optional)_ A custom option name to store the selected page. Defaults to `page_for_{$post_type}s`, following WordPress conventions. |

---

### Returns

An associative array of callback functions registered by the function. These can be referenced if you want to manually remove them later using `remove_action()` or `remove_filter()`:

- `admin_init_cb`: Registers the setting and renders the dropdown on the Reading Settings page.
- `display_post_states_cb`: Adds a post state label (e.g., "Portfolio page") to the Pages list in the admin.
- `add_admin_notice_cb`: Displays a warning in the block editor if the user is editing the archive page.
- `filter_post_state_cb`: Adds a post state label when rendered elsewhere.
- `add_pagination_cb`: Modifies rewrite rules to support pagination for the archive.
- `edit_page_link_cb`: Adds a shortcut to the admin bar for editing the archive page.
- `option_updated_cb`: Flushes rewrite rules and updates internal tracking when the setting is changed.

---

### Functionality

- Adds a custom dropdown to **Settings → Reading** where admins can assign a WordPress page to serve as the archive for the custom post type.
- Ensures proper **rewrite rules** are registered for paginated URLs under the selected archive page.
- Updates the WordPress admin UI:
  - Displays the selected archive page with a post state label (e.g., “Portfolio page”).
  - Warns users editing the archive page directly via the block editor.
- Automatically flushes and updates routing rules when the option is changed.

---

### Admin UI Integration

| Feature                        | Location                             |
|--------------------------------|--------------------------------------|
| Archive page dropdown          | Settings → Reading                   |
| Archive page label             | Pages list (`/wp-admin/edit.php?post_type=page`) |
| Warning notice in editor       | Block editor of the selected page    |
| Edit link in admin bar         | Visible when viewing the archive URL |
| Pagination rewrite rules       | URLs like `/portfolio/page/2`        |

---

### Notes

- If the theme includes `xd_theme_version_compare()` and the version is `>= 1.0.07`, the rewrite rules will prioritize post type archives (`post_type=query_var`).
- In older versions, the fallback is a `pagename` rewrite which may not trigger `is_post_type_archive()` correctly.

---

### Example

```php
add_action( 'init', function () {
    xd_register_archive_page( 'portfolio' );
} );
```


---



## `xd_register_ui_page( $post_type, $type, $soft_route_enabled = false, $option_name = null )`

Registers a "collection page" for a custom post type with a UI-based display mechanism — typically a **modal** or **flyout** — allowing individual posts to be embedded within a parent page at a custom route like `/collection-page/post-slug`.

This approach enables highly interactive UIs where post content (like a modal flyout) can be loaded inline via JavaScript, rather than requiring a full page load to view a single post.

---

### Parameters

| Parameter             | Type      | Description                                                                                     |
|-----------------------|-----------|-------------------------------------------------------------------------------------------------|
| `$post_type`          | `string`  | The post type to register.                                                                     |
| `$type`               | `string`  | The interface type. Typically `'flyout'` or `'modal'`, which hooks into custom front-end JS behaviors. |
| `$soft_route_enabled` | `bool`    | Controls how broadly the embedded UI appears. If `true`, JavaScript enhances links to open the modal/flyout **on any page**. If `false`, the embedded view is only activated on the designated collection page. |
| `$option_name`        | `string|null` | _(Optional)_ Custom option name for the collection page. Defaults to `page_for_{$post_type}s`. |

---

### Returns

A merged array of all callback functions registered, including those from `xd_register_archive_page()` and the additional UI-based logic in this function:

- `add_rewrite_rule_cb`
- `post_type_rewrite_cb`
- `save_page_cb`
- `add_tags_cb`
- `collection_meta_cb`
- `ui_types_filter_cb`
- All standard archive callbacks (see `xd_register_archive_page()`)

---

### Functionality

- Adds a dropdown setting to the Reading Settings admin page for selecting a "collection page".
- Modifies the post type's rewrite slug to match the selected page's slug (e.g., `/resources/{slug}`).
- Adds a custom rewrite tag (e.g., `%resource-slug%`) to resolve post content from the URL.
- Resolves the embedded post dynamically and injects its data into the page at render time.
- Optionally injects Yoast SEO metadata for the embedded post.
- Triggers 404 behavior if a slug does not resolve to a valid post.

---

### Embedding Behavior

Depending on the value of `$soft_route_enabled`, this function changes how embedded UIs behave:

| Setting               | Behavior |
|-----------------------|----------|
| `soft_route_enabled = true`  | JS-enhanced links across the entire site will open the post in a modal/flyout. |
| `soft_route_enabled = false` | Only links on the **collection page** are enhanced. On other pages, links navigate to the canonical single post view. |

This is ideal for:

- **Global UI usage**: Case studies opening in modals anywhere on the site.
- **Scoped UI usage**: A flyout UI only available on the project grid page.

---

### Example

```php
add_action( 'init', function () {
    xd_register_ui_page( 'case_study', 'modal', true );
} );
```

---

## `xd_register_static_page( $args )`

Registers a virtual static page in WordPress — a page that doesn't exist in the database but is generated dynamically at runtime using provided arguments.

This function is useful for lightweight, fully custom pages where you want complete control over content rendering, query vars, SEO output, and routing — without creating a physical post or template file.

---

### Parameters

| Parameter   | Type | Description |
|-------------|------|-------------|
| `$args`     | `array{slug: string, title: string, content: string, disable_wpseo?: bool, callback?: callable, query_vars?: array}` | Configuration array for the static page. |

#### Accepted Keys

| Key            | Type       | Description                                                                 |
|----------------|------------|-----------------------------------------------------------------------------|
| `slug`         | `string`   | The URL path to use for the static page (e.g., `about-company`).           |
| `title`        | `string`   | The page title to display.                                                 |
| `content`      | `string`   | The HTML content for the page.                                             |
| `callback`     | `callable` | _(Optional)_ A function that returns an array with `title` and `content`. Overrides static values. |
| `query_vars`   | `array`    | _(Optional)_ Custom public query vars to allow in the request.             |
| `disable_seo`  | `bool`     | _(Optional)_ If true, disables Yoast SEO output on this page.              |

---

### Returns

An array of callbacks that are registered for this page:

- `$static_post` – Handles virtual post object creation.
- `$pre_get_shortlink` – Prevents broken shortlink behavior.
- `$rewrite_rule` – Registers the page slug into rewrite rules.
- `$disable_wpseo` – Removes Yoast SEO head rendering (if enabled and requested).

These can be used with `remove_action()` or `remove_filter()` to unregister the page.

---

### What It Does

- **Creates a virtual post object** for the given slug, responding with custom content and title when the URL is accessed.
- **Does not require database storage** — no physical `page` is created in WordPress.
- Adds the slug to rewrite rules so the page is publicly accessible.
- Optionally disables SEO output from Yoast (if available) to avoid unnecessary metadata on synthetic content.
- Accepts a `callback` to dynamically generate the post content and title at runtime.
- Allows extending the set of accepted public query variables if needed.

---

### Example

```php
xd_register_static_page( array(
    'slug'    => 'privacy-policy',
    'title'   => 'Privacy Policy',
    'content' => '<h2>We care about your privacy</h2><p>... custom content here ...</p>',
) );
```

---