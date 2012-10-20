<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * SEO controller
 *
 * @package		Nova
 * @category	Controller
 * @author		Moriel Schottlender
 * @copyright	2012 Moriel Schottlender GNU
 */

require_once MODPATH.'core/controllers/nova_admin.php';

class Awesimreport extends Nova_admin {

	public function __construct()
	{
		parent::__construct();
	}

	public function index($action = false) {
		
		$data['header'] = 'aweSimReport Lite for Nova 2';


		$this->_regions['content'] = Location::view('awesimreport_index', $this->skin, 'admin', $data);
		$this->_regions['javascript'] = Location::js('awesimreport_index_js', $this->skin, 'admin');
		$this->_regions['title'].= $data['header'];
		
		Template::assign($this->_regions);
		
		Template::render();
		

	}
	
}
