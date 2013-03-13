<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'sho_bookmarklet';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.1';
$plugin['author'] = 'Stephan Hochhaus';
$plugin['author_uri'] = 'http://yauh.de';
$plugin['description'] = 'Use a simple bookmarklet to create a link from any webpage you visit';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public       : only on the public side of the website (default)
// 1 = public+admin : on both the public and admin side
// 2 = library      : only when include_plugin() or require_plugin() is called
// 3 = admin        : only on the admin side
$plugin['type'] = '3';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '0';

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
/**
 * sho_bookmarklet plugin for Textpattern CMS.
 *
 * @author Stephan Hochhaus
 * @date 2013-
 * @license GNU GPLv2
 * 
 * Copyright (C) 2013 Stephan Hochhaus <http://yauh.de>
 * Licensed under GNU Genral Public License version 2
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

new sho_bookmarklet();

class sho_bookmarklet {

	static public $version = '0.0.1';
	
	/**
	 * @var array Stores plugin areas
	 */
	
	protected $plugin_areas = array();

	/**
	 * Does installing and uninstalling.
	 * @param string $event The admin-side event.
	 * @param string $step The admin-side / plugin-lifecycle step.
	 */

	
	/**
	 * Constructor
	 */
	
	public function __construct() {
		add_privs('sho_bookmarklet', '1,2');
		add_privs('plugin_prefs.sho_bookmarklet', '1,2');
		register_tab('extensions', 'sho_bookmarklet', gTxt('sho_bookmarklet'));
		register_callback(array($this, 'panes'),'sho_bookmarklet');
		$this->register();
	}

	/**
	 * Registers the tabs
	 */

	public function register() {
		
		global $plugin_areas;
		
		@$rs = 
			safe_rows(
				'tabgroup, page, label',
				'sho_bookmarklet',
				'1=1 ORDER BY position asc'
			);
		
		if(!$rs) {
			return;
		}
		
		$this->plugin_areas = $plugin_areas;
		$unset = array();
		
		foreach($rs as $a) {
			
			foreach($plugin_areas as $area => $items) {
				foreach($items as $title => $event) {
					if($a['page'] === $event && !in_array($event, $unset)) {
						unset($plugin_areas[$area][$title]);
						$unset[] = $event;
					}
				}
			}
			
			register_tab($a['tabgroup'], $a['page'], gTxt($a['label']));
		}
	}
	public function panes() {
		require_privs('sho_bookmarklet');

		global $step;
		
		$step = 'pane';
		
		$this->$step();
	}


	 /**
	 * Outputs the pane HTML markup and sets page title.
	 * @param mixed $out Pane markup. Accepts arrays and strings.
	 * @param string $pagetop Page title.
	 * @param string $message Message shown in the header.
	 */

	private function pane() {
		
		global $event;
		
		// $adminURL should perhaps be a function of its own?!
		$adminURL = 'http';
 		if (isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on") {$pageURL .= "s";}
 					$adminURL .= "://";
 		if ($_SERVER["SERVER_PORT"] != "80") {
			 		 $adminURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 		} else {
			 		 $adminURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 		}
 		$adminURL = preg_replace("#(http://.*)index\.php.*#", "$1", $adminURL);
		pagetop(gTxt('Welcome'), 'msg');
				
		echo 
			n.
			'<h1 class="txp-heading">'.gTxt('sho_bookmarklet').'</h1>'.n.
			'<div class="txp-container">'.n.
			'	<p class="txp-buttons">'.
				'<a href="javascript:(function(){var%20form=document.createElement(%22form%22);form.setAttribute(%22method%22,%22POST%22);form.setAttribute(%22target%22,%22txplinkframe%22);form.setAttribute(%22action%22,%22'.$adminURL.'%3Fevent=link%26step=link_edit%22);var%20hiddenURLField=document.createElement(%22input%22);var%20hiddenNameField=document.createElement(%22input%22);form.appendChild(hiddenURLField);form.appendChild(hiddenNameField);hiddenURLField.setAttribute(%22type%22,%22hidden%22);hiddenURLField.setAttribute(%22name%22,%22url%22);hiddenURLField.setAttribute(%22value%22,document.URL);hiddenNameField.setAttribute(%22type%22,%22hidden%22);hiddenNameField.setAttribute(%22name%22,%22linkname%22);hiddenNameField.setAttribute(%22value%22,document.title);document.body.appendChild(form);form.submit();})();">Add to links</a>'.
			'</p>'.n.
			'<p>Drag the box above to your bookmarks bar.</p>
			<p>When logged into the Textpattern admin section you will be able to add any site to your own links by simply clicking on the <em>Add to links</em> link.</p>'.n.$adminURL.
			'</div>'.n;
	}
	
	/**
	 * Redirect to the admin-side interface
	 */

	public function prefs() {
		header('Location: ?event=sho_bookmarklet');
		echo 
			'<p>'.n.
			'	<a href="?event=sho_bookmarklet">'.gTxt('continue').'</a>'.n.
			'</p>';
	}

}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
<h1>sho_bookmarklet</h1>
<p><strong>Important:</strong> You need to be logged in to your Textpattern instance for this bookmarklet to work.</p>
# --- END PLUGIN HELP ---
-->
<?php
}
?>