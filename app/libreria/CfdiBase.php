<?php
namespace App\libreria;
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2018 PLAN B
 * @version 1.0.1
 */

class CfdiBase {

	public function __construct($xmlObject=null) {
	}

	public static function cargar($xmlObject) {
		$clss = new static;
		foreach ($xmlObject->attributes as $name => $value) {
			$name = str_replace(':', '_', $name);
			if(property_exists($clss, $name)) {
				$clss->$name = $value;
			}
		}
		return $clss;
	}

	public function setElement($document, $parent=null, $params=null) {
		throw new Exception('Método no implementado', 1);
	}

    public function __set($property, $value) {
        if (!property_exists($this, $property)) {
			throw new Exception('Propiedad '.get_class($this).'::'.$property.' no válida', 1);
        }
    }

    public static function valueIsEmpty($val) {
    	return is_null($val)
    		|| (is_string($val) && strlen($val) == 0)
    	;
    }
}