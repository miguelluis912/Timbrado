<?php
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2017 PLAN B
 * @version 1.0.2 (29/09/2017)
 */

/**
 * Nodo opcional para asentar el número de cuenta predial con el que fue registrado el inmueble, en el sistema catastral de la entidad federativa de que trate, o bien para incorporar los datos de identificación del certificado de participación inmobiliaria no amortizable.
 */
class CuentaPredial extends CfdiBase {
	/**
	 * @var string Atributo requerido para precisar el número de la cuenta predial del inmueble cubierto por el presente concepto, o bien para incorporar los datos de identificación del certificado de participación inmobiliaria no amortizable, tratándose de arrendamiento.
	 */
	public $Numero;


	public function setElement($document, $parent=null, $params=null) {
		$data = array();
		if(!self::valueIsEmpty($this->Numero))
			$data['Numero'] = $this->Numero;

		return $document->addElement('cfdi:CuentaPredial', $parent, $data);
	}
}