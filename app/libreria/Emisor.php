<?php
namespace App\libreria;
use App\libreria\CfdiBase;
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2018 PLAN B
 * @version 1.0.1
 */

/**
 * Nodo requerido para expresar la información del contribuyente emisor del comprobante.
 */
class Emisor extends CfdiBase {
	/**
	 * @var tdCFDI:t_RFC Atributo requerido para registrar la Clave del Registro Federal de Contribuyentes correspondiente al contribuyente emisor del comprobante.
	 */
	public $Rfc;

	/**
	 * @var string Atributo opcional para registrar el nombre, denominación o razón social del contribuyente emisor del comprobante.
	 */
	public $Nombre;

	/**
	 * @var catCFDI:c_RegimenFiscal Atributo requerido para incorporar la clave del régimen del contribuyente emisor al que aplicará el efecto fiscal de este comprobante.
	 */
	public $RegimenFiscal;

	public static function init($Rfc, $RegimenFiscal, $Nombre=null) {
		$clss = new self;
		$clss->Rfc = $Rfc;
		$clss->RegimenFiscal = $RegimenFiscal;
		$clss->Nombre = $Nombre;
		return $clss;
	}


	public function setElement($document, $parent=null, $params=null) {
		$data = array();
		if(!self::valueIsEmpty($this->Rfc))
			$data['Rfc'] = $this->Rfc;
		if(!self::valueIsEmpty($this->Nombre))
			$data['Nombre'] = $this->Nombre;
		if(!self::valueIsEmpty($this->RegimenFiscal))
			$data['RegimenFiscal'] = $this->RegimenFiscal;
		
		return $document->addElement('cfdi:Emisor', $parent, $data);
	}
}