<?php
namespace App\libreria;
use App\libreria\CfdiBase;
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2017 PLAN B
 * @version 1.0.4 (30/12/2017)
 */

/**
 * Nodo requerido para registrar la información detallada de un bien o servicio amparado en el comprobante.
 */
class Concepto extends CfdiBase {
	/**
	 * @var catCFDI:c_ClaveProdServ Atributo requerido para expresar la clave del producto o del servicio amparado por el presente concepto. Es requerido y deben utilizar las claves del catálogo de productos y servicios, cuando los conceptos que registren por sus actividades correspondan con dichos conceptos.
	 */
	public $ClaveProdServ;
	
	/**
	 * @var string Atributo opcional para expresar el número de parte, identificador del producto o del servicio, la clave de producto o servicio, SKU o equivalente, propia de la operación del emisor, amparado por el presente concepto. Opcionalmente se puede utilizar claves del estándar GTIN.
	 */
	public $NoIdentificacion;
	
	/**
	 * @var xs:decimal Atributo requerido para precisar la cantidad de bienes o servicios del tipo particular definido por el presente concepto.
	 */
	public $Cantidad;
	
	/**
	 * @var catCFDI:c_ClaveUnidad Atributo requerido para precisar la clave de unidad de medida estandarizada aplicable para la cantidad expresada en el concepto. La unidad debe corresponder con la descripción del concepto.
	 */
	public $ClaveUnidad;
	
	/**
	 * @var xs:string Atributo opcional para precisar la unidad de medida propia de la operación del emisor, aplicable para la cantidad expresada en el concepto. La unidad debe corresponder con la descripción del concepto.
	 */
	public $Unidad;
	
	/**
	 * @var xs:string Atributo requerido para precisar la descripción del bien o servicio cubierto por el presente concepto.
	 */
	public $Descripcion;
	
	/**
	 * @var tdCFDI:t_Importe Atributo requerido para precisar el valor o precio unitario del bien o servicio cubierto por el presente concepto.
	 */
	public $ValorUnitario;
	
	/**
	 * @var tdCFDI:t_Importe Atributo requerido para precisar el importe total de los bienes o servicios del presente concepto. Debe ser equivalente al resultado de multiplicar la cantidad por el valor unitario expresado en el concepto. No se permiten valores negativos.
	 */
	public $Importe;
	
	/**
	 * @var tdCFDI:t_Importe Atributo opcional para representar el importe de los descuentos aplicables al concepto. No se permiten valores negativos.
	 */
	public $Descuento;

	/**
	 * @var [...] Nodo opcional para capturar los impuestos aplicables al presente concepto. Cuando un concepto no registra un impuesto, implica que no es objeto del mismo. Impuestos (0, 1)
	 */
	public $Impuestos;

	/**
	 * @var InformacionAduanera Nodo opcional para introducir la información aduanera aplicable cuando se trate de ventas de primera mano de mercancías importadas o se trate de operaciones de comercio exterior con bienes o servicios. Secuencia (0, Ilimitado)
	 */
	public $InformacionAduanera;

	/**
	 * @var CuentaPredial Nodo opcional para asentar el número de cuenta predial con el que fue registrado el inmueble, en el sistema catastral de la entidad federativa de que trate, o bien para incorporar los datos de identificación del certificado de participación inmobiliaria no amortizable. Secuencia (0, 1)
	 */
	public $CuentaPredial;

	/**
	 * @var ComplementoConcepto Nodo opcional donde se incluyen los nodos complementarios de extensión al concepto definidos por el SAT, de acuerdo con las disposiciones particulares para un sector o actividad específica. Secuencia (0, 1)
	 */
	public $ComplementoConcepto;

	/**
	 * @var Parte[] Nodo opcional para expresar las partes o componentes que integran la totalidad del concepto expresado en el comprobante fiscal digital por Internet. Secuencia (0, Ilimitado)
	 */
	public $Parte;


	public static function init($ClaveProdServ, $Cantidad, $ClaveUnidad, $Descripcion, $ValorUnitario, $Importe) {
		$clss = new self;
		$clss->ClaveProdServ = $ClaveProdServ;
		$clss->Cantidad = $Cantidad;
		$clss->ClaveUnidad = $ClaveUnidad;
		$clss->Descripcion = $Descripcion;
		$clss->ValorUnitario = $ValorUnitario;
		$clss->Importe = $Importe;
		return $clss;
	}

	public function agregarImpuesto($item) {
		if(!is_array($this->Impuestos)) {
			$this->Impuestos = array();
		}
		$clss = get_class($item);
		if($clss == 'ConceptoTraslado'){
			$this->Impuestos['Traslados'][] = $item;
		}elseif($clss == 'ConceptoRetencion'){
			$this->Impuestos['Retenciones'][] = $item;
		}
	}

	public static function cargar($xmlObject) {
		$clss = parent::cargar($xmlObject);

		foreach ($xmlObject->children as $child) {
			switch ($child->name) {
				case 'cfdi:InformacionAduanera':
				case 'cfdi:CuentaPredial':
				case 'cfdi:ComplementoConcepto':
				case 'cfdi:Parte':
					throw new Exception('Concepto -> '.$chidl->name.'. No implementado', 2);
					break;
				case 'cfdi:Impuestos':
					$clss->Impuestos = array();
					foreach ($child->children as $child) {
						switch ($child->name) {
							case 'cfdi:Traslados':
								$clss->Impuestos['Traslados'] = array();
								foreach ($child->children as $child) {
									if($child->name == 'cfdi:Traslado') {
										$clss->Impuestos['Traslados'][] = ConceptoTraslado::cargar($child);
									}
								}
								break;
							case 'cfdi:Retenciones':
								$clss->Impuestos['Retenciones'] = array();
								foreach ($child->children as $child) {
									if($child->name == 'cfdi:Retencion') {
										$clss->Impuestos['Retenciones'][] = ConceptoRetencion::cargar($child);
									}
								}
								break;
						}
					}
					break;
			}
		}

		return $clss;
	}

	

	public function setElement($document, $parent=null, $params=null) {
		$data = array();

		if(!self::valueIsEmpty($this->ClaveProdServ))
			$data['ClaveProdServ'] = $this->ClaveProdServ;
		if(!self::valueIsEmpty($this->NoIdentificacion))
			$data['NoIdentificacion'] = $this->NoIdentificacion;

		if($params['TipoDeComprobante'] == 'N' || $params['TipoDeComprobante'] == 'P') {
			$data['Cantidad'] = '1';
		}else{
			$data['Cantidad'] = UtilCfdi::valorCantidad($this->Cantidad);
		}

		if(!self::valueIsEmpty($this->ClaveUnidad))
			$data['ClaveUnidad'] = $this->ClaveUnidad;
		if(!self::valueIsEmpty($this->Unidad))
			$data['Unidad'] = $this->Unidad;
		if(!self::valueIsEmpty($this->Descripcion))
			$data['Descripcion'] = $this->Descripcion;

		if($params['TipoDeComprobante'] == 'P') {
			$data['ValorUnitario'] = '0';
		}elseif(!self::valueIsEmpty($this->ValorUnitario)) {
			// ...el SAT [...] ha definido en su "Matriz de errores" que los proveedores autorizados de certificación (PAC) "no deben considerar esta validación"; es decir, el SAT, considerando esta discrepancia de manejo de decimales, indica que el PAC no debe validar que el número de decimales en este campo tengan como máximo los decimales que permite la moneda, sino que podrá contener hasta 6 decimales, sin que sea motivo de error.
			$data['ValorUnitario'] = UtilCfdi::valorImporte($this->ValorUnitario, 6);
			// $data['ValorUnitario'] = UtilCfdi::valorImporte($this->ValorUnitario, $params['Moneda']);
		}

		if(!self::valueIsEmpty($this->Importe))
			$data['Importe'] = UtilCfdi::valorImporte($this->Importe, $params['Moneda']);
		if(!self::valueIsEmpty($this->Descuento) && (float)$this->Descuento > 0.0)
			$data['Descuento'] = UtilCfdi::valorImporte($this->Descuento, $params['Moneda']);

		$conceptoE = $document->addElement('cfdi:Concepto', $parent, $data);
		
		if(!self::valueIsEmpty($this->Impuestos)){
			$impuestosE = $document->addElement('cfdi:Impuestos', $conceptoE);
			if(!empty($this->Impuestos['Traslados'])){
				$trasladosE = $document->addElement('cfdi:Traslados', $impuestosE);
				foreach ($this->Impuestos['Traslados'] as $item) {
					$item->setElement($document, $trasladosE, $params);
				}
			}
			if(!empty($this->Impuestos['Retenciones'])){
				$retencionesE = $document->addElement('cfdi:Retenciones', $impuestosE);
				foreach ($this->Impuestos['Retenciones'] as $item) {
					$item->setElement($document, $retencionesE, $params);
				}
			}
		}

		if(!self::valueIsEmpty($this->InformacionAduanera)){
			$this->InformacionAduanera->setElement($document, $conceptoE);
		}

		if(!self::valueIsEmpty($this->CuentaPredial)){
			$this->CuentaPredial->setElement($document, $conceptoE);
		}

		if(!self::valueIsEmpty($this->ComplementoConcepto)){
			throw new Exception('Concepto -> ComplementoConcepto. No implementado', 3);
		}

		if(!self::valueIsEmpty($this->Parte)){
			throw new Exception('Concepto -> Parte. No implementado', 3);
		}

		return $conceptoE;
	}
}