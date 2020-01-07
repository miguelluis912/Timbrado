<?php
namespace App\libreria;
use App\libreria\CfdiBase;
use DOMDocument;
use  XSLTProcessor ;
/**
 * 
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2018 PLAN B
 * @version 1.0.5 (11/01/2018)
 */

class Comprobante extends CfdiBase {
	/**
	 * @var string Atributo requerido con valor prefijado a 3.3 que indica la versión del estándar bajo el que se encuentra expresado el comprobante.
	 */
	protected $Version = '3.3';

	/**
	 * @var string Atributo opcional para precisar la serie para control interno del contribuyente. Este atributo acepta una cadena de caracteres.
	 */
	public $Serie;

	/**
	 * @var string Atributo opcional para control interno del contribuyente que expresa el folio del comprobante, acepta una cadena de caracteres.
	 */
	public $Folio;

	/**
	 * @var string Atributo requerido para la expresión de la fecha y hora de expedición del Comprobante Fiscal Digital por Internet. Se expresa en la forma AAAAMM-DDThh:mm:ss y debe corresponder con la hora local donde se expide el comprobante.
	 */
	protected $Fecha;

	/**
	 * @var tdCFDI:t_FechaH Atributo requerido para contener el sello digital del comprobante fiscal, al que hacen referencia las reglas de resolución miscelánea vigente. El sello debe ser expresado como una cadena de texto en formato Base 64.
	 */
	protected $Sello;

	/**
	 * @var string Atributo condicional para expresar la clave de la forma de pago de los bienes o servicios amparados por el comprobante. Si no se conoce la forma de pago este atributo se debe omitir.
	 */
	public $FormaPago;

	/**
	 * @var catCFDI:c_FormaPago Atributo requerido para expresar el número de serie del certificado de sello digital que ampara al comprobante, de acuerdo con el acuse correspondiente a 20 posiciones otorgado por el sistema del SAT.
	 */
	protected $NoCertificado;

	/**
	 * @var string Atributo requerido que sirve para incorporar el certificado de sello digital que ampara al comprobante, como texto en formato base 64.
	 */
	protected $Certificado;

	/**
	 * @var string Atributo condicional para expresar las condiciones comerciales aplicables para el pago del comprobante fiscal digital por Internet. Este atributo puede ser condicionado mediante atributos o complementos.
	 */
	public $CondicionesDePago;

	/**
	 * @var tdCFDI:t_Importe Atributo requerido para representar la suma de los importes de los conceptos antes de descuentos e impuesto. No se permiten valores negativos.
	 */
	protected $SubTotal;

	/**
	 * @var tdCFDI:t_Importe Atributo condicional para representar el importe total de los descuentos aplicables antes de impuestos. No se permiten valores negativos. Se debe registrar cuando existan conceptos con descuento.
	 */
	protected $Descuento;

	/**
	 * @var catCFDI:c_Moneda Atributo requerido para identificar la clave de la moneda utilizada para expresar los montos, cuando se usa moneda nacional se registra MXN. Conforme con la especificación ISO 4217.
	 */
	public $Moneda;

	/**
	 * @var xs:decimal Atributo condicional para representar el tipo de cambio conforme con la moneda usada. Es requerido cuando la clave de moneda es distinta de MXN y de XXX. El valor debe reflejar el número de pesos mexicanos que equivalen a una unidad de la divisa señalada en el atributo moneda. Si el valor está fuera del porcentaje aplicable a la moneda tomado del catálogo c_Moneda, el emisor debe obtener del PAC que vaya a timbrar el CFDI, de manera no automática, una clave de confirmación para ratificar que el valor es correcto e integrar dicha clave en el atributo Confirmacion.
	 */
	public $TipoCambio;

	/**
	 * @var tdCFDI:t_Importe Atributo requerido para representar la suma del subtotal, menos los descuentos aplicables, más las contribuciones recibidas (impuestos trasladados - federales o locales, derechos, productos, aprovechamientos, aportaciones de seguridad social, contribuciones de mejoras) menos los impuestos retenidos. Si el valor es superior al límite que establezca el SAT en la Resolución Miscelánea Fiscal vigente, el emisor debe obtener del PAC que vaya a timbrar el CFDI, de manera no automática, una clave de confirmación para ratificar que el valor es correcto e integrar dicha clave en el atributo Confirmacion. No se permiten valores negativos.
	 */
	protected $Total;

	/**
	 * @var catCFDI:c_TipoDeComprobante Atributo requerido para expresar la clave del efecto del comprobante fiscal para el contribuyente emisor.
	 */
	public $TipoDeComprobante;

	/**
	 * @var catCFDI:c_MetodoPago Atributo condicional para precisar la clave del método de pago que aplica para este comprobante fiscal digital por Internet, conforme al Artículo 29-A fracción VII incisos a y b del CFF.
	 */
	public $MetodoPago;

	/**
	 * @var catCFDI:c_CodigoPostal Atributo requerido para incorporar el código postal del lugar de expedición del comprobante (domicilio de la matriz o de la sucursal).
	 */
	public $LugarExpedicion;

	/**
	 * @var string Atributo condicional para registrar la clave de confirmación que entregue el PAC para expedir el comprobante con importes grandes, con un tipo de cambio fuera del rango establecido o con ambos casos. Es requerido cuando se registra un tipo de cambio o un total fuera del rango establecido.
	 */
	public $Confirmacion;
	
	/**
	 * @var CfdiRelacionados Nodo opcional para precisar la información de los comprobantes relacionados. Secuencia (0, 1)
	 */
	public $CfdiRelacionados;

	/**
	 * @var Emisor Nodo requerido para expresar la información del contribuyente emisor del comprobante. Secuencia (1, 1)
	 */
	public $Emisor;

	/**
	 * @var Receptor Nodo requerido para precisar la información del contribuyente receptor del comprobante. Secuencia (1, 1)
	 */
	public $Receptor;

	/**
	 * @var Concepto[] Nodo requerido para listar los conceptos cubiertos por el comprobante. Secuencia (1, 1)
	 */
	public $Conceptos;

	/**
	 * @var ComprobanteImpuestos Nodo condicional para expresar el resumen de los impuestos aplicables. Secuencia (0, 1)
	 */
	public $Impuestos;

	/**
	 * @var Complemento Secuencia (0, 1)
	 */
	public $Complemento;

	/**
	 * @var Addenda Secuencia (0, 1)
	 */
	public $Addenda;

	public $customAttrs = array(
		'xmlns:cfdi' => 'http://www.sat.gob.mx/cfd/3',
		'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
		'xsi:schemaLocation' => 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd',
	);


	public function setSubTotal($SubTotal) {
		$this->SubTotal = $SubTotal;
	}

	public function setTotal($Total) {
		$this->Total = $Total;
	}

	public function setDescuento($Descuento) {
		$this->Descuento = $Descuento;
	}

	public function setFecha($Fecha) {
		if(is_int($Fecha)) $Fecha = date('Y-m-d\TH:i:s', $Fecha);
		$this->Fecha = $Fecha;
	}

	public function agregarConcepto($item) {
		if(!is_array($this->Conceptos)) {
			$this->Conceptos = array();
		}

		if(!empty($item->Impuestos)) {
			if(!$this->Impuestos) {
				$this->Impuestos = new ComprobanteImpuestos;
			}

			if(!empty($item->Impuestos['Traslados'])) {
				foreach ($item->Impuestos['Traslados'] as $impuesto) {
					$ret = new ComprobanteTraslado();
					$ret->Impuesto = $impuesto->Impuesto;
					$ret->TipoFactor = $impuesto->TipoFactor;
					$ret->TasaOCuota = $impuesto->TasaOCuota;
					$ret->Importe = $impuesto->Importe;
					$this->Impuestos->agregarImpuesto($ret);
				}
			}

			if(!empty($item->Impuestos['Retenciones'])) {
				foreach ($item->Impuestos['Retenciones'] as $impuesto) {
					$ret = new ComprobanteRetencion();
					$ret->Impuesto = $impuesto->Impuesto;
					$ret->Importe = $impuesto->Importe;
					$this->Impuestos->agregarImpuesto($ret);
				}
			}
		}

		$this->Conceptos[] = $item;
	}

	public static function cargar($xmlObject) {
		$clss = parent::cargar($xmlObject);

		if($clss->Version !== '3.3') {
			throw new Exception('Versión de Comprobante incorrecta', 1);
		}

		foreach($xmlObject->children as $child) {
			switch ($child->name) {
				case 'cfdi:Receptor':
					$clss->Receptor = Receptor::cargar($child);
					break;
				case 'cfdi:Emisor':
					$clss->Emisor = Emisor::cargar($child);
					break;
				case 'cfdi:Impuestos':
					$clss->Impuestos = ComprobanteImpuestos::cargar($child);
					break;
				case 'cfdi:Complemento':
					$clss->Complemento = array();
					foreach ($child->children as $child) {
						if($child->name == 'tfd:TimbreFiscalDigital') {
							$clss->Complemento[] = TimbreFiscalDigital::cargar($child);
						}
					}
					break;
				case 'cfdi:Conceptos':
					$clss->Conceptos = array();
					foreach ($child->children as $child) {
						$clss->Conceptos[] = Concepto::cargar($child);
					}
					break;
			}
		}

		return $clss;
	}

	public function setElement($document, $parent=null, $params=null) {
		$data = $this->customAttrs ?: array();

		$data['Version'] = $this->Version;
		if(!self::valueIsEmpty($this->TipoDeComprobante))
			$data['TipoDeComprobante'] = $this->TipoDeComprobante;
		if(!self::valueIsEmpty($this->Fecha))
			$data['Fecha'] = $this->Fecha;
		if(!self::valueIsEmpty($this->Serie))
			$data['Serie'] = $this->Serie;
		if(!self::valueIsEmpty($this->Folio))
			$data['Folio'] = $this->Folio;
		if(!self::valueIsEmpty($this->LugarExpedicion))
			$data['LugarExpedicion'] = $this->LugarExpedicion;
		if($this->TipoDeComprobante != 'T' && $this->TipoDeComprobante != 'P') {
			// solo incluir si no es tipo Traslado y Pago
			if(!self::valueIsEmpty($this->FormaPago))
				$data['FormaPago'] = $this->FormaPago;
			if(!self::valueIsEmpty($this->MetodoPago))
				$data['MetodoPago'] = $this->MetodoPago;
		}
		if(!self::valueIsEmpty($this->CondicionesDePago))
			$data['CondicionesDePago'] = $this->CondicionesDePago;
		if(!self::valueIsEmpty($this->Moneda))
			$data['Moneda'] = $this->Moneda;
		if($this->Moneda != 'MXN' && $this->Moneda != 'XXX' && !self::valueIsEmpty($this->TipoCambio)) {
			$data['TipoCambio'] = UtilCfdi::valorTipoCambio($this->TipoCambio);
		}
		if(!self::valueIsEmpty($this->Descuento) && (float)$this->Descuento > 0.0)
			$data['Descuento'] = UtilCfdi::valorImporte($this->Descuento, $this->Moneda);
		if(!self::valueIsEmpty($this->SubTotal))
			$data['SubTotal'] = UtilCfdi::valorImporte($this->SubTotal, $this->Moneda);
		if(!self::valueIsEmpty($this->Total))
			$data['Total'] = UtilCfdi::valorImporte($this->Total, $this->Moneda);
		if(!self::valueIsEmpty($this->NoCertificado))
			$data['NoCertificado'] = $this->NoCertificado;
		if(!self::valueIsEmpty($this->Certificado))
			$data['Certificado'] = $this->Certificado;
		if(!self::valueIsEmpty($this->Sello))
			$data['Sello'] = $this->Sello;

		$params = array(
			'TipoDeComprobante'=>$this->TipoDeComprobante,
			'Moneda'=>$this->Moneda
		);

		$comprobanteE = $document->addElement('cfdi:Comprobante', $parent, $data);

		if(!self::valueIsEmpty($this->CfdiRelacionados))
			$this->CfdiRelacionados->setElement($document, $comprobanteE);
		if(!self::valueIsEmpty($this->Emisor))
			$this->Emisor->setElement($document, $comprobanteE);
		if(!self::valueIsEmpty($this->Receptor))
			$this->Receptor->setElement($document, $comprobanteE);
		if(!self::valueIsEmpty($this->Conceptos)){
			$conceptosE = $document->addElement('cfdi:Conceptos', $comprobanteE);
			foreach ($this->Conceptos as $item) {
				$item->setElement($document, $conceptosE, $params);
			}
		}

		if(!self::valueIsEmpty($this->Impuestos))
			$this->Impuestos->setElement($document, $comprobanteE, $params);

		if(!self::valueIsEmpty($this->Complemento)){
			$complementoE = $document->addElement('cfdi:Complemento', $comprobanteE);
			foreach ($this->Complemento as $item) {
				$item->setElement($document, $complementoE);
			}
		}

		if(!self::valueIsEmpty($this->Addenda)){
			$complementoA = $document->addElement('cfdi:Addenda', $comprobanteE);
			foreach ($this->Addenda as $item) {
				$complementoA->appendChild(
					$document->importNode(
						$item->documentElement,
						true
					)
				);
			}
		}

		return $comprobanteE;
	}

	public function obtenerXml() {
		$document = new UtilDocumento();
		$this->setElement($document);
		return $document->saveXML();
	}

	public function sellar($cert) {
		$this->Sello = null;
		$this->Certificado = $cert->toBase64();
		$this->NoCertificado = $cert->getNumeroCertificado();

		$cadenaOriginal = $this->getCadenaOriginal();
		if($cadenaOriginal) {
			$sello = $cert->firmarCadena($cadenaOriginal, OPENSSL_ALGO_SHA256);
			if($sello) {
				$this->Sello = $sello;
				return true;
			}
		}

		return false;
	}

	private function getCadenaOriginal(){
		// desactivar errores
		libxml_use_internal_errors(true);

		// import stylesheet
		$xsl = new DOMDocument();
		$xsl->load(dirname(__FILE__).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cadenaoriginal_3_3.xslt');

		// start xslt
		$proc = new XSLTProcessor();
		$proc->importStyleSheet($xsl);

		// prepare xml
		$docUtil = new UtilDocumento();
		$xml = new DOMDocument();
		$this->setElement($docUtil);
		$ok = $xml->loadXML( $docUtil->saveXml() );

		if($ok) {
			return $proc->transformToXml($xml);
		}

		return false;
	}
}