# Spam Destroyer WordPress Plugin

## Description

Spam Destroyer is a WordPress plugin designed to annihilate comment spam on your website. This plugin uses a combination of JavaScript and PHP to verify the authenticity of comments. 

## Features

- Cookie-based verification
- JavaScript key validation
- Easy to use, zero config

## Installation

1. Download the plugin and extract it.
2. Upload the `spam-destroyer` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

After activation, the plugin works automatically. No configuration needed.

## Files

- `assets/kill-spam.js`: Handles client-side spam validation.
- `index.php`: Main file that initializes the plugin and contains the class autoloader.
- `src/class-links.php`: Adds helpful links in admin panel.
- `src/class-asset-loading.php`: Manages asset loading.
- `src/class-catcher.php`: Contains spam prevention logic.
- `src/class-set-keys.php`: Handles key generation and settings.
- `src/class-spam-checking.php`: Responsible for checking comments.
- `src/class-factory.php`: Factory class for object creation.
- `src/class-shared.php`: Shared settings and data.
- `composer.json`: Implements Composer, but this is an alpha build, so not in WordPress repo yet.
- `phpcs.xml`: States that the plugin uses the official WordPress coding standards.
- `uninstall.php`: Removes data on uninstall.

## Methods

### src/admin/class-links.php

#### `init()`
Initialize the class.

#### `plugins_page_meta( array $plugin_meta, string $plugin_file ): array`
Add a settings page link to the plugin list.

#### `add_meta( array $plugin_meta ): array`
Add links to the plugin meta array.

### src/frontend/class-asset-loading.php

#### `__construct( \SpamDestroyer\Shared $shared )`
Class constructor.

#### `load_payload()`
Loading the JavaScript payload.

### src/frontend/assets/class-catcher.php

#### `__construct( \SpamDestroyer\Shared $shared, \SpamDestroyer\Frontend\Spam_Checking $spam_checking, \SpamDestroyer\Frontend\Asset_Loading $asset_loading )`
Class constructor.

#### `init()`
Initializes the spam prevention measures.

#### `register_spam_prevention_hooks_and_filters()`
Registers spam prevention hooks and filters.

#### `display_extra_input_field()`
Display the extra input field on the page.

#### `get_extra_input_field( string $html = '' ): string`
An extra input field, which is intentionally filled with garble, but will be replaced dynamically with JS later.

### src/frontend/class-set-keys.php

#### `__construct( \SpamDestroyer\Shared $shared )`
Class constructor.

#### `init()`
Initializing the class.

#### `set_keys()`
Set various keys.

#### `generate_new_key(): string`
Generate a new unique key.

### src/frontend/class-spam-checking.php

#### `__construct( \SpamDestroyer\Shared $shared )`
Class constructor.

#### `filter_spam_comments( array $comment ): array`
Checks if this is a spam comment.

#### `kill_spam_dead( string $error )`
Be gone evil demon spam!

#### `validate_trackbacks_and_pingbacks( array $comment ): array`
Checking trackbacks and pingbacks for spam.

#### `validate_comment( array $comment ): array`
Check a comment.

### src/class-factory.php

#### `create(): array`
Create instances of the main plugin classes.

### src/class-shared.php

#### `get_spam_key(): string`
Get the stored spam key.

#### `update_spam_key( $key ): bool`
Update the spam key.

#### `get_stored_plugin_version(): string`
Get the stored plugin version.

#### `update_stored_plugin_version( string $version ): bool`
Update the stored plugin version.

#### `get_server_ip(): string`
Get the server IP address.

#### `get_web_ip( array $comment ): string`
Get the web IP address from the comment author's URL.

#### `get_error_explanation( $error ): string`
Retrieve an explanation of an error.

## Custom Hooks and Filters

- `spam_destroyer_cookie_lifetime`: Modify the cookie expiry time.

## Contributing

If you'd like to contribute, please fork the repository and submit a pull request.

## Author

Ryan Hellyer <ryanhellyer@gmail.com>

## License

Copyright © Ryan Hellyer

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.