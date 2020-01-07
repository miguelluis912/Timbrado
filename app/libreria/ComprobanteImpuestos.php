<?php
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2017 PLAN B
 * @version 1.0.2 (29/09/2017)
 */

/**
 * Nodo condicional para expresar el resumen de los impuestos aplicables.
 */
class ComprobanteImpuestos extends CfdiBase {
	/**
	 * @var tdCFDI:t_Importe Atributo condicional para expresar el total de los impuestos retenidos que se desprenden de los conceptos expresados en el comprobante fiscal digital por Internet. No se permiten valores negativos. Es requerido cuando en los conceptos se registren impuestos retenidos
	 */
	public $TotalImpuestosRetenidos;

	
	/**
	 * @var tdCFDI:t_Importe Atributo condicional para expresar el total de los impuestos trasladados que se desprenden de los conceptos expresados en el comprobante fiscal digital por Internet. No se permiten valores negativos. Es requerido cuando en los conceptos se registren impuestos trasladados.
	 */
	public $TotalImpuestosTrasladados;

	/**
	 * @var ComprobanteRetencion[] Nodo condicional para capturar los impuestos retenidos aplicables. Es requerido cuando en los conceptos se registre algún impuesto retenido.
	 */
	public $Retenciones;
	
	/**
	 * @var ComprobanteTraslado[] Nodo condicional para capturar los impuestos trasladados aplicables. Es requerido cuando en los conceptos se registre un impuesto trasladado.
	 */
	public $Traslados;


	public function agregarImpuesto($item) {
		$clss = get_class($item);
		if($clss == 'ComprobanteTraslado'){

			if($item->TipoFactor == 'Exento') {
				// no se deben incluir estos impuestos
				return;
			}

			if(!is_array($this->Traslados))
				$this->Traslados = array();
			if(empty($this->TotalImpuestosTrasladados))
				$this->TotalImpuestosTrasladados = 0.0;

			$combinacionIdx = -1;
			foreach ($this->Traslados as $i => $impuesto) {
				if($impuesto->Impuesto == $item->Impuesto
					&& $impuesto->TipoFactor == $item->TipoFactor
					&& $impuesto->TasaOCuota == $item->TasaOCuota
				) {
					$combinacionIdx = $i;
					break;
				}
			}

			if($combinacionIdx < 0) {
				$this->Traslados[] = $item;
			}else{
				$this->Traslados[$combinacionIdx]->Importe += (float)$item->Importe;
			}

			$this->TotalImpuestosTrasladados += (float)$item->Importe;
		}elseif($clss == 'ComprobanteRetencion'){

			if(!is_array($this->Retenciones))
				$this->Retenciones = array();
			if(empty($this->TotalImpuestosRetenidos))
				$this->TotalImpuestosRetenidos = 0.0;

			$combinacionIdx = -1;
			foreach ($this->Retenciones as $i => $impuesto) {
				if($impuesto->Impuesto == $item->Impuesto) {
					$combinacionIdx = $i;
					break;
				}
			}

			if($combinacionIdx < 0) {
				$this->Retenciones[] = $item;
			}else{
				$this->Retenciones[$combinacionIdx]->Importe += (float)$item->Importe;
			}

			$this->TotalImpuestosRetenidos += (float)$item->Importe;
		}
	}

	public function setElement($document, $parent=null, $params=null) {
		$data = array();
		if(!self::valueIsEmpty($this->TotalImpuestosRetenidos))
			$data['TotalImpuestosRetenidos'] = UtilCfdi::valorImporte($this->TotalImpuestosRetenidos, $params['Moneda']);
		if(!self::valueIsEmpty($this->TotalImpuestosTrasladados))
			$data['TotalImpuestosTrasladados'] = UtilCfdi::valorImporte($this->TotalImpuestosTrasladados, $params['Moneda']);

		$impuestosE = $document->addElement('cfdi:Impuestos', $parent, $data);

		if(is_array($this->Retenciones)) {
			$retencionesE = $document->addElement('cfdi:Retenciones', $impuestosE);
			foreach ($this->Retenciones as $item) {
				$item->setElement($document, $retencionesE, $params);
			}
		}

		if(is_array($this->Traslados)) {
			$trasladadosE = $document->addElement('cfdi:Traslados', $impuestosE);
			foreach ($this->Traslados as $item) {
				$item->setElement($document, $trasladadosE, $params);
			}
		}

		return $impuestosE;
	}

	public static function cargar($xmlObject) {
		$clss = parent::cargar($xmlObject);

		foreach ($xmlObject->children as $child) {
			switch ($child->name) {
				case 'cfdi:Retenciones':
					$clss->Retenciones = array();
					foreach ($child->children as $child) {
						if($child->name == 'cfdi:Retencion') {
							$clss->Retenciones[] = ComprobanteRetencion::cargar($child);
						}
					}
					break;
				case 'cfdi:Traslados':
					$clss->Traslados = array();
					foreach ($child->children as $child) {
						if($child->name == 'cfdi:Traslado') {
							$clss->Traslados[] = ComprobanteTraslado::cargar($child);
						}
					}
					break;
			}
		}

		return $clss;
	}
}