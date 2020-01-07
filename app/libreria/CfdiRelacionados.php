<?php
namespace Libreria;
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2017 PLAN B
 * @version 1.0.2 (26/12/2017)
 */

/**
 * Nodo opcional para precisar la información de los comprobantes relacionados.
 */
class CfdiRelacionados extends CfdiBase {
	/**
	 * @var catCFDI:c_TipoRelacion Atributo requerido para indicar la clave de la relación que existe entre éste que se esta generando y el o los CFDI previos.
	 */
	public $TipoRelacion;

	/**
	 * @var CfdiRelacionado[] Nodo requerido para precisar la información de los comprobantes relacionados.
	 */
	public $CfdiRelacionado;



	public static function init($TipoRelacion, $CfdiRelacionado=null) {
		$clss = new self;
		$clss->TipoRelacion = $TipoRelacion;

		if(is_array($CfdiRelacionado)) {
			$clss->CfdiRelacionado = $CfdiRelacionado;
		}
		return $clss;
	}

	public function agregarUUID($uuid){
		if(!is_array($this->CfdiRelacionado))
			$this->CfdiRelacionado = array();
		$this->CfdiRelacionado[] = $uuid;
	}

	public function setElement($document, $parent=null, $params=null) {
		$data = array();
		if(!self::valueIsEmpty($this->TipoRelacion))
			$data['TipoRelacion'] = $this->TipoRelacion;

		$relacionados = $document->addElement('cfdi:CfdiRelacionados', $parent, $data);

		if(!self::valueIsEmpty($this->CfdiRelacionado)){
			foreach ($this->CfdiRelacionado as $uuid) {
				$document->addElement('cfdi:CfdiRelacionado', $relacionados, array('UUID'=>$uuid));
			}
		}

		return $relacionados;
	}
}