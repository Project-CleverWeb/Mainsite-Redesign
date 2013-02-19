<?php
define('DS',DIRECTORY_SEPARATOR,1);
define('EOL',PHP_EOL,1);

// Exlcudes
define('EXCLUDE_PREPEND',0,1);
define('EXCLUDE_HEADER',0,1);
define('EXCLUDE_CONTENT',0,1);
define('EXCLUDE_FOOTER',0,1);
define('EXCLUDE_POSTPEND',0,1);

class page{
	
	/**
	 * Variables
	 */
	// theme
	public $theme_path;
	public $theme_pages_path;
	public $theme_lib_path;
	public $theme_error_path;
	public $theme_sub_path;
	
	// page
	public $page;
	public $page_abspath;
	public $page_relpath; // Does not start with a directory seperator!
	
	// subdomain
	public $sub;
	public $sub_lib_path;
	
	// misc
	public $scripts;
	public $scripts_path;
	protected $loaded_scripts;
	protected $parsed_url;
	
	
	public function init(){
		if ($this->domain_verify()===FALSE) {
			exit('invalid domain');
		}/*
		if(ERROR_PAGE) {
			// send to error page with error #
		}*/
		$this->setinfo();
		// build the page
		return $this->build();
		
	}
	protected function setinfo(){
		// set vars
		
		// clean request
		$this->clean_request();
		
		// parse the url
		if (empty($_SERVER['REQUEST_SCHEME'])) {
			$_SERVER['REQUEST_SCHEME'] = 'http'; // ensure this gets set
		}
		$this->parsed_url = parse_url($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		if(!empty($this->parsed_url['query'])){
			$this->parsed_url['query'] = parse_str($this->parsed_url['query']);
		}
		$this->parsed_url['path'] = array(
			'full' => $this->parsed_url['path'],
			'parts' => explode('/', $this->parsed_url['path']),
			'basename' => basename($this->parsed_url['path'])
		);
		if(!stripos($this->parsed_url['path']['basename'], '.php')){
			$this->parsed_url['path']['basename'] = 'index.php'; // all index files should be php
			$this->parsed_url['path']['full'] = $this->parsed_url['path']['full'].$this->parsed_url['path']['basename'];
			$this->parsed_url['path']['parts'][] = $this->parsed_url['path']['basename'];
		}
		foreach ($this->parsed_url['path']['parts'] as $key => $value) {
			// remove empty values
			if(empty($value)==FALSE){
				if(!isset($temp)){
					$temp = array();
				}
				$temp[] = $value;
			}
		}
		if(!isset($temp)){
			// reset this array if browsing the top index file for this domain
			unset($this->parsed_url['path']['parts']);
			$this->parsed_url['path']['parts'] = array($this->parsed_url['path']['basename']);
			$this->parsed_url['path']['full'] = DS.$this->parsed_url['path']['basename'];
		}
		else{
			$this->parsed_url['path']['parts'] = $temp;
			unset($temp);
		}
		
		
		// lib paths
		$this->scripts_path = __DIR__.DS.'script-lib';
		$this->sub_lib_path = __DIR__.DS.'sub-lib';
		
		// load the theme config & get the theme dir's
		$this->theme_path = __DIR__.DS.'theme';
		if(file_exists($this->theme_path.DS.'config.php')){
			include_once $this->theme_path.DS.'config.php';
		}
		if(empty($this->theme_lib_path)){
			$this->theme_lib_path   = $this->theme_path.DS.'lib';
		}
		if(empty($this->theme_pages_path)){
			$this->theme_pages_path = $this->theme_path.DS.'pages';
		}
		if(empty($this->theme_error_path)){
			$this->theme_error_path = $this->theme_path.DS.'error.php';
		}
		
		// if we are in a subdomain
		if ($this->is_subdomain()){
			if(empty($this->theme_sub_path)==TRUE){
				$this->theme_pages_path = $this->theme_pages_path.DS.'sub'.DS.$this->sub;
				$this->theme_sub_path = $this->theme_pages_path;
			}
			$this->subdomain($this->sub) or
				exit('Could not properly load the scripts for this subdomain');
		}
		else{
			$this->theme_pages_path = $this->theme_sub_path;
		}
		
		
		$this->page_abspath = $this->get_page_abspath(); // this auto-sets $this->page_relpath
		$this->page = basename($this->page_abspath);
		
		
		
		return TRUE;
	}
	protected function clean_request(){
		
		
		// return?
	}
	public function is_subdomain(){
		// IF TRUE set $sub to $string ELSE set to NULL
		$array = explode(".",$_SERVER['HTTP_HOST']);
		$sub = $array[0];
		if($sub!='projectcleverweb'){
			$this->sub = $sub;
			return TRUE;
		}
		return FALSE;
	}
	public function subdomain($name = NULL){
		// Add the subdoamin specific elements to the loading script
		$name = (empty($name))? $this->sub : $name;
		
		switch ($name) {
			case 'new':
				switch (FALSE) {
					// lib's should load their indvidual dependencies, not this script.
					case $this->add_script($this->sub_lib_path.DS.'docs-lib.php','sub-docs-lib'):
					case $this->add_script($this->scripts_path.DS.'codex-lib.php','script-codex-lib'):
					// case $this->add_script($path[, $key]):
						return FALSE;
					default:
						return TRUE;
				}
			case 'partners':
				switch (FALSE) {
					case $this->add_script($this->sub_lib_path.DS.'partners-lib.php','sub-partners-lib'):
						return FALSE;
					default:
						return TRUE;
				}
			
			// since we are only returning in switch 2 we don't need breaks in either of the switches.
			default: // this subdomain doesn't require and additional scripts
				return TRUE;
		}
	}
	protected function domain_verify(){
		// verify that the page being requested is actually on our domain.
		
		// return boolean
		// for now just act as if everything is ok
		return TRUE;
	}
	public function get_page_abspath(){
		// if page does not exist send to error page
		if(file_exists($this->theme_pages_path.$this->parsed_url['path']['full'])){
			$this->page_relpath = ltrim($this->parsed_url['path']['full'],'/\\');
			return $this->theme_pages_path.$this->parsed_url['path']['full'];
		}
		else{
			if(strpos($this->theme_error_path, $this->theme_path)){
				$this->page_relpath = ltrim(str_ireplace($this->theme_path, '', $this->theme_error_path),'/\\');
			}
			else{
				// when the error path is not in the theme dir leave a message.
				// exit('Something is fishy with the theme error page path: '.$this->theme_error_path); // needs debugging [comeback]
			}
			return $this->theme_error_path;
		}
		
	}
	private function load_scripts(){
		if (empty($this->scripts)) {
			return TRUE; // nothing to load but no errors
		}
		
		$scripts = $this->scripts;
		if(empty($this->loaded_scripts)) {
			$this->loaded_scripts = array();
		}
		$err = 0;
		foreach ($scripts as $key => $value) {
			if(!empty($this->loaded_scripts[$key])) {
				// we may be loading the same script twice OR 2 different scripts with the same key
				// neither are fatal but will mess up loaded scripts array. so it is bad practice
				// to allow it.
				exit('Double script load error with key: '.$key);
			}
			if(!file_exists($value)){
				trigger_error('Requested script "'.$value.'" does not exist! Key: '.$key.EOL,E_USER_WARNING);
				// trigger exit() after final script attempts to load
				$err++;
			}
			else{
				$this->loaded_scripts[$key] = $value;
				include_once $value;
			}
		}
		if($err>0){
			return FALSE;
		}
		
		// clean up and return
		$this->scripts = array();
		return TRUE;
	}
	public function load_script($name){
		// dynamically load a script
		if(!file_exists($this->scripts_path.DS.$name)){
			trigger_error('Requested script "'.$name.'" does not exist!'.EOL,E_USER_WARNING);
			return FALSE;
		}
		else{
			include_once $this->scripts_path.DS.$name;
			return TRUE;
		}
	}
	public function add_script($path,$key=FALSE){
		if(empty($this->scripts)){ $this->scripts = array();}
		if (empty($key)) {
			$this->scripts[] = $path;
		}
		else{
			if(empty($this->scripts[$key])){
				$this->scripts[$key] = $path;
			}
			else{
				trigger_error('key "'.$key.'" is already set in $this->scripts'.EOL,E_USER_WARNING);
				return FALSE;
			}
		}
		return TRUE;
	}
	
	private function build(){
		// load required items
		if(!$this->load_scripts()){
			exit('Error loading main script set');
		}
		
		// just put them together in order for every page
		if (EXCLUDE_PREPEND != TRUE && file_exists($this->theme_path.DS.'prepend.php')) {
			include $this->theme_path.DS.'prepend.php';
		}
		if (EXCLUDE_HEADER != TRUE && file_exists($this->theme_path.DS.'header.php')) {
			include $this->theme_path.DS.'header.php';
		}
		if (EXCLUDE_CONTENT != TRUE && file_exists($this->theme_pages_path.DS.$this->page.'.php')) {
			include $this->theme_pages_path.DS.$this->page.'.php';
		}
		if (EXCLUDE_FOOTER != TRUE && file_exists($this->theme_path.DS.'footer.php')) {
			include $this->theme_path.DS.'footer.php';
		}
		if (EXCLUDE_POSTPEND != TRUE && file_exists($this->theme_path.DS.'postpend.php')) {
			include $this->theme_path.DS.'postpend.php';
		}
		
		// return boolean
	}
	
}