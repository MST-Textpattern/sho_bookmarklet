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