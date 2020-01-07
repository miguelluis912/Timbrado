<?php
namespace App\libreria;
use DOMDocument;
/**
 * Clase para trabajar con DOMDocument
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2017 PLAN B
 * @version 1.0.3 (19/12/2017)
 */

class UtilDocumento extends DOMDocument {
	public function __construct($version='1.0', $encoding='UTF-8') {
		return parent::__construct($version, $encoding);
	}

	public function addElement($name, $parent=null, $attrs=null){
		$element = $this->createElement($name);
		if(is_array($attrs)){
			foreach ($attrs as $key => $value) {
				$element->setAttribute($key, $value);
			}
		}
		if($parent) {
			$parent->appendChild($element);
		} else {
			$this->appendChild($element);
		}
		return $element;
	}
}