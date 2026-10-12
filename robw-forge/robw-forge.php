<?php
/**
 * Plugin Name: Robw Git Forge
 * Description: Small private WordPress Git forge. HTTPS smart Git, repository ACLs, browser UI, issues and signed webhooks. Requires the separate Robw OAuth provider.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Requires Plugins: robw-oauth
 * License: GPL-2.0-or-later
 * Text Domain: robw-forge
 */
defined('ABSPATH') || exit;
require_once __DIR__.'/src/git.php';
require_once __DIR__.'/src/store.php';
require_once __DIR__.'/src/hooks.php';
require_once __DIR__.'/src/http.php';
add_filter('robw_oauth_repository_access',function($allowed,$uid,$repo,$write){if(!str_starts_with($repo,'robw-forge/'))return$allowed;$id=substr($repo,11);try{[$raw,$r]=\RobwForge\Store::read($id);return \RobwForge\Store::allowed($r,$uid,$write);}catch(Throwable $e){return false;}},10,4);
add_action('template_redirect',['RobwForge\\HTTP','route'],-90);
add_action('admin_menu',function(){add_menu_page('Git Forge','Git Forge','read','robw-forge',['RobwForge\\HTTP','admin'],'dashicons-editor-code');});
add_action('robw_forge_deliver',['RobwForge\\Hooks','cron']);
register_activation_hook(__FILE__,function(){if(!wp_next_scheduled('robw_forge_deliver'))wp_schedule_event(time()+60,'hourly','robw_forge_deliver');});
register_deactivation_hook(__FILE__,function(){wp_clear_scheduled_hook('robw_forge_deliver');});
