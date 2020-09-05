<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {
	
	/* Debido al desmadre con desampa, cuando se facturo o hace otra cosa, todos los documentos de desampa se hacen
	con los docs de garotas*/
	public $truequeHabilitado = true;
	public $truequeAplicado = false; 
	public $sucursales_trueque = array(7=>2);
	
	public function esUsadaComoSucursaldeRespaldo($sucursal){
		foreach($this->sucursales_trueque as $key => $content){
			if($content == $sucursal){
				return true;
			}
		}
		return false;
	}
	
	public function getSucursalesTruequeFromSucursalResponde($sucursalResponde){
		$sucursales = array();
		foreach($this->sucursales_trueque as $key => $content){
			if($content == $sucursalResponde){
				array_push($sucursales, $key);
			}
		}
		return $sucursales;
	}

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		log_message('debug', "Model Class Initialized");
	}

	/**
	 * __get
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string
	 * @access private
	 */
	function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}
}
// END Model Class

/* End of file Model.php */
/* Location: ./system/core/Model.php */