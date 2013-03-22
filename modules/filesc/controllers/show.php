<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Show extends Public_Controller{
	private	$_path = '';
	
	public function __construct()
	{
	
		parent::__construct();
		
		$this->config->load('files/files');
		$this->load->library('files/files');

		$this->_path = FCPATH.rtrim($this->config->item('files:path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
	}
	
	public function thumb($w=0, $h=0, $x = 0, $x2=0, $y = 0, $y2=0,$id = 0)
	{
		// is it a 15 char hash with no file extension or is it an old style numeric id with no file extension?
		if ((strlen($id) === 15 and strpos($id, '.') === false) or (is_numeric($id) and strpos($id, '.') === false))
		{
			$file = $this->file_m->get($id);
		}
		
		// it's neither a legacy numeric id nor a new hash id so they've passed the filename itself
		else
		{
			$data = getimagesize($this->_path.$id) OR show_404();
			
			$ext = '.'.end(explode('.', $id));
			
			$file->width 		= $data[0];
			$file->height 		= $data[1];
			$file->filename 	= $id;
			$file->extension 	= $ext;
			$file->mimetype 	= $data['mime'];
		}
	
		if ( ! $file)
		{
			set_status_header(404);
			exit;
		}

		$cache_dir = $this->config->item('cache_dir') . 'image_files/';

		is_dir($cache_dir) or mkdir($cache_dir, 0777, true);


		// Path to image thumbnail
		$thumb_filename = $cache_dir . 'crop';
		$thumb_filename .= '_' . $w.'_' . $h.'_' . $x. '_' .$x2 .'_' . $y. '_'. $y2;
		$thumb_filename .= '_' . md5($file->filename) . $file->extension;

		$expire = 60 * Settings::get('files_cache');
		if ($expire)
		{
			header("Pragma: public");
			header("Cache-Control: public");
			header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expire) . ' GMT');
		}

		$source_modified = filemtime($this->_path . $file->filename);
		$thumb_modified = filemtime($thumb_filename);


		if ( ! file_exists($thumb_filename) OR ($thumb_modified < $source_modified))
		{
			// load lib
			$this->load->library('image_lib');

			// crop
			$config['image_library']    = 'gd2';
			$config['source_image']     = $this->_path.$file->filename;
			$config['new_image']        = $thumb_filename;
			$config['maintain_ratio']   = FALSE;
			$config['width']			= $x2-$x;
			$config['height']			= $y2-$y;
			$config['x_axis']			= $x;
			$config['y_axis']			= $y;

			$this->image_lib->initialize($config);
			$this->image_lib->crop();
			$this->image_lib->clear();
			
			// resize
			$config['image_library']    = 'gd2';
			$config['source_image']     = $thumb_filename;
			$config['new_image']        = $thumb_filename;
			$config['maintain_ratio']   = true;
			$config['height']           = $h;
			$config['width']            = $w;
			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			$this->image_lib->clear();
			

		} else if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
			(strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $thumb_modified) && $expire )
		{
			// Send 304 back to browser if file has not beeb changed
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $time).' GMT', true, 304);
			exit;
		}

		header('Content-type: ' . $file->mimetype);
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($thumb_filename)) . ' GMT');
		ob_clean();
		readfile($thumb_filename);
	}


}

