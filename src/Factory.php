<?php

/**
 * Factory Class for Spam Destroyer
 *
 * This class is responsible for creating instances of other classes in the plugin.
 */

declare(strict_types=1);

namespace SpamDestroyer;

/**
 * Factory Class
 *
 * Creates instances of the main classes used in the Inpsyde Coding Test Plugin.
 */
class Factory
{
    /**
     * Plugin slug.
     *
     * @var string
     */
    protected const SLUG = 'spam-destroyer';

    /**
     * Plugin version.
     *
     * @var string
     */
    protected const VERSION = '3.0';

    /**
     * Create instances of main plugin classes.
     *
     * @return object[] An array containing instances of the main plugin classes.
     */
    public static function create(): array
    {
        return [
            new Admin\Meta(),
            /*
            new Admin(),
            new API(),
            new Page(),
            new Refresh(),
            new Table(),
            new Translations(),
            */
        ];
    }
}
