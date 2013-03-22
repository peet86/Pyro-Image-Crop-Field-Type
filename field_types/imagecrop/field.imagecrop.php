<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Image Crop Field Type for PyroStreams
 *
 * @package		PyroStreams
 * @author		Peter Varga (http://www.vargapeter.com)
 * @license		http://www.apache.org/licenses/LICENSE-2.0.html (Apache 2)
 * @copyright	Copyright (c) 2013, Peter Varga
 */
class Field_imagecrop
{
	public $field_type_slug			= 'imagecrop';

	public $db_col_type				= 'text';

	public $custom_parameters		= array('folder', 'crop_width', 'crop_height', 'allowed_types');

	public $version					= '1.0.1';

	public $author					= array('name' => 'Peter Varga', 'url' => 'http://www.vargapeter.com');

	public $input_is_file			= true;

	// --------------------------------------------------------------------------

	public function __construct()
	{
		get_instance()->load->library('image_lib');
	}

	// --------------------------------------------------------------------------


	public function form_output($params)
	{
	
		$this->CI->load->config('files/files');
		$out = '';

		if ($params['value'])
		{		
			$value=unserialize($params['value']);
	
			// form
			$out .= '<div class="crop_field"><span class="crop_image_remove" data-remove="'.$params['form_slug'].'">X</span><div id="'.$params['form_slug'].'_crop_pool" class="crop_pool"><img id="'.$params['form_slug'].'_crop_img" class="crop_img" data-name="'.$params['form_slug'].'" src="'.site_url('files/large/'.$value["id"]).'" /></div></div><br />'.form_hidden($params['form_slug'], $value["id"])
			.form_hidden($params['form_slug']."_x", $value["x"])
		    .form_hidden($params['form_slug']."_y", $value["y"])
			.form_hidden($params['form_slug']."_x2", $value["x2"])
			.form_hidden($params['form_slug']."_y2", $value["y2"]);
		}
		else
		{
			$out .= form_hidden($params['form_slug'], '');
		}

		$options['name'] 	= $params['form_slug'];
		$options['name'] 	= $params['form_slug'].'_file';
		$this->CI->type->add_js('imagecrop', 'imagecrop.js');
		$this->CI->type->add_js('imagecrop', 'jquery.Jcrop.min.js');
		$this->CI->type->add_css('imagecrop', 'imagecrop.css');
		$this->CI->type->add_css('imagecrop', 'jquery.Jcrop.min.css');
		return $out .= form_upload($options);
	}

	
//	--------------------------------------------------------------------------


	public function pre_save($input, $field, $stream, $row_id, $form_data)
	{

								
		if ( ! isset($_FILES[$field->field_slug.'_file']['name']) or ! $_FILES[$field->field_slug.'_file']['name'])
		{
			if (isset($form_data[$field->field_slug]) and $form_data[$field->field_slug])
			{
				$save["id"]=$form_data[$field->field_slug];
	
				$save["x"]=$form_data[$field->field_slug."_x"];
				$save["y"]=$form_data[$field->field_slug."_y"];
				$save["x2"]=$form_data[$field->field_slug."_x2"];
				$save["y2"]=$form_data[$field->field_slug."_y2"];
							
				return serialize($save);

			}
			else
			{
				return "";
			}
		}

		$this->CI->load->library('files/files');

		$allowed_types 	= (isset($field->field_data['allowed_types'])) ? $field->field_data['allowed_types'] : '*';

		$return = Files::upload($field->field_data['folder'], null, $field->field_slug.'_file', null, null, null, $allowed_types);

		if ( ! $return['status'])
		{
			$this->CI->session->set_flashdata('notice', $return['message']);
			return null;
		}
		else
		{
			// Return the ID of the file DB entry
			$save["id"]=$return['data']['id'];
			
			// Set max. crop (default crop)
			$cw=$field->field_data['crop_width'];
			$ch=$field->field_data['crop_height'];
			$w=$return["data"]["width"];
			$h=$return["data"]["height"];	
			
			
			$cnx = round($w / 2);
			$cny = round($h / 2);
			
			
			$cwh  = round($cw / 2); 
			$chh = round($ch / 2);
			
			$save["x"] = max(0, $cnx - $cwh);
			$save["y"] = max(0, $cny - $chh);
			
			$save["x2"] = min($w, $cnx + $cwh);
			$save["y2"] = min($h, $cny + $chh);
				
			return serialize($save);
		}
	}

	// --------------------------------------------------------------------------

	public function pre_output($input, $params)
	{
		$data=unserialize($input);
		if ( ! $input or $input == 'dummy' ) return null;

		// Get image data
		$image = $this->CI->db->select('filename, alt_attribute, description, name')->where('id', $data["id"])->get('files')->row();

		if ( ! $image) return null;
		


		//http://domain.com/filesc/thumb/300/200/100/400/88/288/9eb65ef6c1100f0/image.jpg

		return '<img src="/filesc/thumb/'.$params["crop_width"].'/'.$params["crop_height"].'/'.$data["x"].'/'.$data["x2"].'/'.$data["y"].'/'.$data["y2"].'/'.$data["id"].'/'.$this->obvious_alt($image).'" alt="'.$this->obvious_alt($image).'" />';
	}

	// --------------------------------------------------------------------------

	public function pre_output_plugin($data, $params)
	{
		if ( ! $data or $data == 'dummy' ) return null;

		$this->CI->load->library('files/files');
		$input=unserialize($data);
		
		$file = Files::get_file($input["id"]);

		if ($file['status'])
		{
			$image = $file['data'];


			if ( ! $image->path)
			{
				$image_data['image'] = base_url($this->CI->config->item('files:path').$image->filename);
			}
			else
			{
				$image_data['image'] = str_replace('{{ url:site }}', base_url(), $image->path);
			}


			$alt = $this->obvious_alt($image);
			
			$url_start=BASE_URL.'filesc/thumb/';
			$url_end='/'.$input["x"].'/'.$input["x2"].'/'.$input["y"].'/'.$input["y2"].'/'.$input["id"].'/'.$image->filename;


			$image_data['filename']			= $image->filename;
			$image_data['name']				= $image->name;
			$image_data['alt']				= $image->alt_attribute;
			$image_data['description']		= $image->description;
			$image_data['img']				= img(array('alt' => $alt, 'src' => $image_data['image']));
			$image_data['ext']				= $image->extension;
			$image_data['mimetype']			= $image->mimetype;
			$image_data['width']			= $image->width;
			$image_data['height']			= $image->height;
			$image_data['id']				= $image->id;
			$image_data['filesize']			= $image->filesize;
			$image_data['download_count']	= $image->download_count;
			$image_data['date_added']		= $image->date_added;
			$image_data['folder_id']		= $image->folder_id;
			$image_data['folder_name']		= $image->folder_name;
			$image_data['folder_slug']		= $image->folder_slug;
			
		
			
			// thumb url
			$image_data['thumb']['image']			= $url_start.$params["crop_width"].'/'.$params["crop_height"].$url_end;
			
			// thumb [IMG]
			$image_data['thumb']['img']		= img(array('alt' => $alt, 'src'=> $url_start.$params["crop_width"].'/'.$params["crop_height"].$url_end));
			
			// zoom images (thumb_zoom.90.img / thumb_zoom.90.url)
			for($i=1;$i<100;$i++){
				$zoom=100-$i;
				$image_data['thumb']["scale"][$zoom]["image"]=$url_start.(round($params["crop_width"]*$zoom/100)).'/'.(round($params["crop_height"]*$zoom/100)).$url_end;
				$image_data['thumb']["scale"][$zoom]["img"]=img(array('alt' => $alt, 'src'=> $url_start.(round($params["crop_width"]*$zoom/100)).'/'.(round($params["crop_height"]*$zoom/100)).$url_end));
			}
			

			return $image_data;
		}
	}

	// --------------------------------------------------------------------------


	public function param_folder($value = null)
	{
		$this->CI->load->model('files/file_folders_m');

		$tree = $this->CI->file_folders_m->get_folders();

		$tree = (array)$tree;

		if ( ! $tree)
		{
			return '<em>'.lang('streams:image.need_folder').'</em>';
		}

		$choices = array();

		foreach ($tree as $tree_item)
		{
			$tree_item = (object)$tree_item;

			$choices[$tree_item->id] = $tree_item->name;
		}

		return form_dropdown('folder', $choices, $value);
	}

	// --------------------------------------------------------------------------


	public function param_crop_width($value = null)
	{
		return form_input('crop_width', $value);
	}

	// --------------------------------------------------------------------------


	public function param_crop_height($value = null)
	{
		return form_input('crop_height', $value);
	}

	// --------------------------------------------------------------------------


	public function param_allowed_types($value = null)
	{
		return array(
				'input'			=> form_input('allowed_types', $value),
				'instructions'	=> lang('streams:image.allowed_types_instr'));
	}

	// --------------------------------------------------------------------------


	private function obvious_alt($image)
	{
		if ($image->alt_attribute) {
			return $image->alt_attribute;
		}
		if ($image->description) {
			return $image->description;
		}
		return $image->name;
	}

}
