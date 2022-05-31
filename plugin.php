<?php
/**
 * Plugin Name: WPGraphQL Tax Query
 * Plugin URI: https://github.com/eonvisualmedia/wp-graphql-tax-query
 * Description: Adds tax_query to the wp-graphql plugin
 * Author: Eon Visual Media
 * Version: 1.0.1
 *
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

// To make this plugin work properly for both Composer users and non-composer
// users we must detect whether the project is using a global autolaoder. We
// can do that by checking whether our autoloadable classes will autoload with
// class_exists(). If not it means there's no global autoloader in place and
// the user is not using composer. In that case we can safely require the
// bundled autoloader code.
if (! \class_exists('\WPGraphQL\Extensions\TaxQuery')) {
    require_once __DIR__.'/vendor/autoload.php';
}

// Load the actual plugin code
\WPGraphQL\Extensions\TaxQuery\Loader::init();
