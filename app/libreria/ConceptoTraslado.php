<?php
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2018 PLAN B
 * @version 1.0.1
 */

/**
 * Nodo requerido para asentar la información detallada de un traslado de impuestos aplicable al presente concepto.
 */
class ConceptoTraslado extends CfdiBase {
	/**
	 * @var tdCFDI:t_Importe Atributo requerido para señalar la base para el cálculo del impuesto, la determinación de la base se realiza de acuerdo con las disposiciones fiscales vigentes. No se permiten valores negativos.
	 */
	public $Base;
	
	/**
	 * @var catCFDI:c_Impuesto Atributo requerido para señalar la clave del tipo de impuesto trasladado aplicable al concepto.
	 */
	public $Impuesto;
	
	/**
	 * @var catCFDI:c_TipoFactor Atributo requerido para señalar la clave del tipo de factor que se aplica a la base del impuesto.
	 */
	public $TipoFactor;
	
	/**
	 * @var catCFDI:c_TasaOCuota Atributo condicional para señalar el valor de la tasa o cuota del impuesto que se traslada para el presente concepto. Es requerido cuando el atributo TipoFactor tenga un valor que corresponda a Tasa o Cuota.
	 */
	public $TasaOCuota;
	
	/**
	 * @var tdCFDI:t_Importe Atributo condicional para señalar el importe del impuesto trasladado que aplica al concepto. No se permiten valores negativos. Es requerido cuando TipoFactor sea Tasa o Cuota.
	 */
	public $Importe;


	public function setElement($document, $parent=null, $params=null) {
		$data = array();
		if(!self::valueIsEmpty($this->Base))
			$data['Base'] = UtilCfdi::valorImporte($this->Base, $params['Moneda']);
		if(!self::valueIsEmpty($this->Impuesto))
			$data['Impuesto'] = $this->Impuesto;
		if(!self::valueIsEmpty($this->TipoFactor))
			$data['TipoFactor'] = $this->TipoFactor;
		if(!self::valueIsEmpty($this->TasaOCuota))
			$data['TasaOCuota'] = UtilCfdi::valorTasaOCuota($this->TasaOCuota);
		if(!self::valueIsEmpty($this->Importe))
			$data['Importe'] = UtilCfdi::valorImporte($this->Importe, $params['Moneda']);

		return $document->addElement('cfdi:Traslado', $parent, $data);
	}
}