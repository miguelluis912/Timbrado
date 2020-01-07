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
 * Nodo requerido para precisar la información del contribuyente receptor del comprobante.
 */
class Receptor extends CfdiBase {
	/**
	 * @var tdCFDI:t_RFC Atributo requerido para precisar la Clave del Registro Federal de Contribuyentes correspondiente al contribuyente receptor del comprobante.
	 */
	public $Rfc;

	/**
	 * @var string Atributo opcional para precisar el nombre, denominación o razón social del contribuyente receptor del comprobante.
	 */
	public $Nombre;

	/**
	 * @var catCFDI:c_Pais Atributo condicional para registrar la clave del país de residencia para efectos fiscales del receptor del comprobante, cuando se trate de un extranjero, y que es conforme con la especificación ISO 3166-1 alpha-3. Es requerido cuando se incluya el complemento de comercio exterior o se registre el atributo NumRegIdTrib.
	 */
	public $ResidenciaFiscal;

	/**
	 * @var string Atributo condicional para expresar el número de registro de identidad fiscal del receptor cuando sea residente en el extranjero. Es requerido cuando se incluya el complemento de comercio exterior.
	 */
	public $NumRegIdTrib;
	
	/**
	 * @var catCFDI:c_UsoCFDI Atributo requerido para expresar la clave del uso que dará a esta factura el receptor del CFDI.
	 */
	public $UsoCFDI;


	public static function init($Rfc, $UsoCFDI, $Nombre=null) {
		$clss = new self;
		$clss->Rfc = $Rfc;
		$clss->UsoCFDI = $UsoCFDI;
		$clss->Nombre = $Nombre;
		return $clss;
	}

	public function setElement($document, $parent=null, $params=null) {
		$data = array();
		if(!self::valueIsEmpty($this->Rfc))
			$data['Rfc'] = $this->Rfc;
		if(!self::valueIsEmpty($this->Nombre))
			$data['Nombre'] = $this->Nombre;
		if(!self::valueIsEmpty($this->ResidenciaFiscal))
			$data['ResidenciaFiscal'] = $this->ResidenciaFiscal;
		if(!self::valueIsEmpty($this->NumRegIdTrib))
			$data['NumRegIdTrib'] = $this->NumRegIdTrib;
		if(!self::valueIsEmpty($this->UsoCFDI)) $data['UsoCFDI'] = $this->UsoCFDI;

		return $document->addElement('cfdi:Receptor', $parent, $data);
	}
}