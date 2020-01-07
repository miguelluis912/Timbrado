<?php
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2018 PLAN B
 * @version 1.0.1
 */

/**
 * Nodo requerido para la información detallada de un traslado de impuesto específico.
 */
class ComprobanteTraslado extends CfdiBase {
	/**
	 * @var catCFDI:c_Impuesto Atributo requerido para señalar la clave del tipo de impuesto trasladado.
	 */
	public $Impuesto;
	
	/**
	 * @var catCFDI:c_TipoFactor Atributo requerido para señalar la clave del tipo de factor que se aplica a la base del impuesto.
	 */
	public $TipoFactor;
	
	/**
	 * @var catCFDI:c_TasaOCuota Atributo requerido para señalar el valor de la tasa o cuota del impuesto que se traslada por los conceptos amparados en el comprobante.
	 */
	public $TasaOCuota;
	
	/**
	 * @var tdCFDI:t_Importe Atributo requerido para señalar la suma del importe del impuesto trasladado, agrupado por impuesto, TipoFactor y TasaOCuota. No se permiten valores negativos.
	 */
	public $Importe;


	public function setElement($document, $parent=null, $params=null) {
		$data = array();
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