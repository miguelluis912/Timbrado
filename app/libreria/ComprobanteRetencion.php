<?php
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2018 PLAN B
 * @version 1.0.1
 */

/**
 * Nodo requerido para la información detallada de una retención de impuesto específico.
 */
class ComprobanteRetencion extends CfdiBase {
	/**
	 * @var catCFDI:c_Impuesto Atributo requerido para señalar la clave del tipo de impuesto retenido
	 */
	public $Impuesto;

	/**
	 * @var tdCFDI:t_Importe Atributo requerido para señalar el monto del impuesto retenido. No se permiten valores negativos.
	 */
	public $Importe;


	public function setElement($document, $parent=null, $params=null) {
		$data = array();
		if(!self::valueIsEmpty($this->Impuesto))
			$data['Impuesto'] = $this->Impuesto;
		if(!self::valueIsEmpty($this->Importe))
			$data['Importe'] = UtilCfdi::valorImporte($this->Importe, $params['Moneda']);

		return $document->addElement('cfdi:Retencion', $parent, $data);
	}
}