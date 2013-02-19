<?php
define(DS,DIRECTORY_SEPARATOR,1);
define(EOL,PHP_EOL,1);

class page{
	public $sub;
	public $theme_path;
	public $page;
	public $scripts;
	public $theme_pages_path;
	protected $loaded_scripts;
	public $scripts_path;
	public $sub_lib_path;
	public $theme_lib_path;
	public $variable;
	public $variable;
	public $variable;
	public $variable;
	public $variable;
	public $variable;
	public $variable;
	public $variable;
	
	
	public function init(){
		if ($this->domain_verify()===FALSE) {
			exit('invalid domain');
		}
		if($this->is_subdomain()){
			$this->subdomain($this->sub)
		}
		if(ERROR_PAGE) {
			// send to error page with error #
		}
		$this->setinfo();
		// build the page
		return $this->build();
		
	}
	protected function setinfo(){
		// set vars
		
		// scripts
		$this->scripts_path = __DIR__.DS.'script-lib';
		
		// get the theme dir's
		$this->page = $this->get_page();
		$this->theme_path = __DIR__.DS.'theme';
		$this->theme_lib_path = $this->theme_path.DS.'lib';
		$this->theme_pages_path = $this->theme_path.DS.'pages';
		
		// if we are in a subdomain
		if ($this->is_subdomain()) {
			$this->theme_pages_path = $this->theme_pages_path.DS.'sub'.DS.$this->sub;
		}
		
		
		
		
		
		
		
		return TRUE
	}
	private function is_subdomain(){
		static $has_run;
		static $return;
		if($has_run){
			return $return;
		}
		// IF TRUE set $sub to $string ELSE set to NULL
		if(
			array_shift(explode(".",$_SERVER['HTTP_HOST']))=='projectcleverweb'
			){
			$this->sub = array_shift(explode(".",$_SERVER['HTTP_HOST']));
			$return = TRUE;
		}
		$return = FALSE;
		return $return;
	}
	public function subdomain($name = NULL){
		// Add the subdoamin specific elements to the loading script
		
		// return boolean
		return TRUE;
	}
	protected function domain_verify(){
		// verify that the page being requested is actually on our domain.
		
		// return boolean
		// for now just act as if everything is ok
		return TRUE;
	}
	public function get_page(){
		// if page does not exist send to error page
		
		// return string (rel path)
	}
	protected function load_scripts(){
		static $has_run;
		if($has_run){
			return FALSE;
		}
		if (empty($this->scripts)) {
			return TRUE; // nothing to load but no errors
		}
		
		$scripts = $this->scripts;
		if (empty(($this->loaded_scripts)) {
			$this->loaded_scripts = array();
		}
		foreach ($scripts as $key => $value) {
			if(!empty($this->loaded_scripts[$key])) {
				exit('Double script load error with key: '.$key)
			}
			if(!file_exists($this->scripts_path.DS.$value)){
				trigger_error('Requested script "'.$value.'" does not exist! Key: '.$key.EOL,E_USER_WARNING);
			}
			else{
				include_once $this->scripts_path.DS.$value;
			}
		}
		
		$has_run++;
		// return boolean
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
		$this->scripts[$key] = $path;
	}
	
	private function build(){
		// load required items
		if(!$this->subdomain()){ // add the scripts for this subdomain to queue (if they exist)
			exit('Error loading subdomain scripts')
		}
		if(!$this->load_scripts()){
			exit('Error loading main script set')
		}
		
		// Get the theme config file
		if (file_exists($this->theme_path.DS.'prepend.php')) {
			include $this->theme_path.DS.'prepend.php';
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