<?php
define(DS,DIRECTORY_SEPARATOR,1);

class page{
	public $sub;
	public $theme_path;
	public $page;
	public $scripts;
	public $variable;
	public $variable;
	public $variable;
	public $variable;
	public $variable;
	
	
	public function init(){
		if ($this->domain_verify()===FALSE) {
			exit('invalid domain')
		}
		if($this->is_subdomain()){
			$this->subdomain($this->sub)
		}
		if(ERROR_PAGE) {
			// Load and run error handler
			exit;
		}
		$this->setinfo();
		// build the page
		return $this->build();
		
	}
	protected function setinfo(){
		// set vars
		
		$this->get_page();
		
		// return boolean
	}
	private function is_subdomain(){
		// IF TRUE set $sub to $string ELSE set to NULL
		
		
		// return boolean
	}
	public function subdomain($name){
		// Add the subdoamin specific elements to the loading script
		
		// return boolean
	}
	protected function domain_verify(){
		// verify that the page being requested is actually on our domain.
		
		// return boolean
	}
	public function get_page(){
		
		
		// return string
	}
	protected function load_scripts(){
		$scripts = $this->scripts;
		// load the scripts listed in the $scripts array
		
		// return boolean
	}
	public function load_script(){
		// dynamically load a script
		
		// return boolean
	}
	
	
	private function build(){
		// load required items
		$this->load_scripts();
		
		// just put them together in order for every page
		if (file_exists($this->theme_path.DS.'prepend.php')) {
			include $this->theme_path.DS.'prepend.php';
		}
		if (file_exists($this->theme_path.DS.'header.php')) {
			include $this->theme_path.DS.'header.php';
		}
		if (file_exists($this->theme_path.DS.'pages'.DS.$this->page.'.php')) {
			include $this->theme_path.DS.'center.php';
		}
		if (file_exists($this->theme_path.DS.'footer.php')) {
			include $this->theme_path.DS.'footer.php';
		}
		if (file_exists($this->theme_path.DS.'postpend.php')) {
			include $this->theme_path.DS.'postpend.php';
		}
		
		// return boolean
	}
	
}