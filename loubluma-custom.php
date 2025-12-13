<?php
/**
 * Plugin Name: Loubluma Custom
 * Description: Custom plugin for loubluma.com.
 * Author: Mathieu Nogaret
 * Version: 0.1.0
 */

require __DIR__ . '/tooltipy-glossary-patch.php';
require __DIR__ . '/whitespace-patch.php';
require __DIR__ . '/help-popup-patch.php';

add_filter( 'astra_tablet_breakpoint', function () {
    return 1080;
});

