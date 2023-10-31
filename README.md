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