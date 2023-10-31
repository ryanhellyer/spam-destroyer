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

- `kill-spam.js`: Handles client-side spam validation.
- `class-links.php`: Adds helpful links in admin panel.
- `class-asset-loading.php`: Manages asset loading.
- `class-catcher.php`: Contains spam prevention logic.
- `class-set-keys.php`: Handles key generation and settings.
- `class-spam-checking.php`: Responsible for checking comments.
- `class-factory.php`: Factory class for object creation.
- `class-shared.php`: Shared settings and data.

## Hooks and Filters

- `spam_destroyer_cookie_lifetime`: Modify the cookie expiry time.

## Contributing

If you'd like to contribute, please fork the repository and submit a pull request.

## Author

Ryan Hellyer <ryanhellyer@gmail.com>

## License

Copyright © Ryan Hellyer

---

Feel free to change or update as necessary.