<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Filesc extends Module {

	public $version = '1.0';

	public function info()
	{
		$info= array(
			'name' => array("en"=>'Files - cropping',"hu"=>'Fájlok - képvágó'),
			'description' => array("en"=>'Tiny files module extension for imagecrop field type'),
			'frontend' => FALSE,
			'backend' => TRUE,
			
		);
			
		return $info;	
	} 

	public function install()
	{
		return true;
	}

	public function uninstall()
	{
		return TRUE;
	}

	public function upgrade($old_version)
	{
		return TRUE;
	}

	public function help()
	{
		return "This module has no inline docs as it does not have a back-end.";
	}
}
/* End of file details.php */
 