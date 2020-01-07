<?php

class ComplementoComercioExterior extends CfdiBase {
	public $document;
	public $comercioExterior;
	public $ceMercancias;
	public $ceEmisor;
	public $ceReceptor;


	public function __construct(){
		parent::__construct();
		$this->document = new UtilDocumento();
	}

	public function setElement($document, $parent=null, $params=null) {
		$node = $document->importNode(
			$this->document->documentElement,
			true
		);
		return $parent->appendChild($node);
	}

	public function setComercioExterior($TipoOperacion,
		$MotivoTraslado=null, $ClaveDePedimento=null, $CertificadoOrigen=null, $NumCertificadoOrigen=null, $NumeroExportadorConfiable=null, $Incoterm=null, $Subdivision=null, $Observaciones=null, $TipoCambioUSD=null, $TotalUSD=null
	){
		$data = array();
		$data['Version'] = '1.1';
		if(!$this->valueIsEmpty($MotivoTraslado)) $data['MotivoTraslado'] = $MotivoTraslado;
		$data['TipoOperacion'] = $TipoOperacion;
		if(!$this->valueIsEmpty($ClaveDePedimento)) $data['ClaveDePedimento'] = $ClaveDePedimento;
		if(!$this->valueIsEmpty($CertificadoOrigen)) $data['CertificadoOrigen'] = $CertificadoOrigen;
		if(!$this->valueIsEmpty($NumCertificadoOrigen)) $data['NumCertificadoOrigen'] = $NumCertificadoOrigen;
		if(!$this->valueIsEmpty($NumeroExportadorConfiable)) $data['NumeroExportadorConfiable'] = $NumeroExportadorConfiable;
		if(!$this->valueIsEmpty($Incoterm)) $data['Incoterm'] = $Incoterm;
		if(!$this->valueIsEmpty($Subdivision)) $data['Subdivision'] = $Subdivision;
		if(!$this->valueIsEmpty($Observaciones)) $data['Observaciones'] = $Observaciones;
		if(!$this->valueIsEmpty($TipoCambioUSD)) $data['TipoCambioUSD'] = UtilCfdi::valorTipoCambio($TipoCambioUSD);
		if(!$this->valueIsEmpty($TotalUSD)) $data['TotalUSD'] = UtilCfdi::valorImporte($TotalUSD, 'USD');

		$this->comercioExterior = $this->document->addElement(
			'cce11:ComercioExterior',
			null,
			$data
		);
	}

	public function setEmisor($Calle, $Estado, $Pais, $CodigoPostal,
		$NumeroExterior=null, $NumeroInterior=null, $Colonia=null, $Localidad=null, $Referencia=null, $Municipio=null,
		$Curp
	){
		$data = array();
		if(!$this->valueIsEmpty($Curp)) {
			$data['Curp'] = $Curp;
		}

		$this->ceEmisor = $this->document->addElement(
			'cce11:Emisor',
			$this->comercioExterior,
			$data
		);

		$data = array();
		$data['Calle'] = $Calle;
		if(!$this->valueIsEmpty($NumeroExterior)) $data['NumeroExterior'] = $NumeroExterior;
		if(!$this->valueIsEmpty($NumeroInterior)) $data['NumeroInterior'] = $NumeroInterior;
		if(!$this->valueIsEmpty($Colonia)) $data['Colonia'] = $Colonia;
		if(!$this->valueIsEmpty($Localidad)) $data['Localidad'] = $Localidad;
		if(!$this->valueIsEmpty($Referencia)) $data['Referencia'] = $Referencia;
		if(!$this->valueIsEmpty($Municipio)) $data['Municipio'] = $Municipio;
		$data['Estado'] = $Estado;
		$data['Pais'] = $Pais;
		$data['CodigoPostal'] = $CodigoPostal;

		$this->document->addElement(
			'cce11:Domicilio',
			$this->ceEmisor,
			$data
		);
	}

	public function setPropietario($NumRegIdTrib, $ResidenciaFiscal){
		$this->document->addElement(
			'cce11:Propietario',
			$this->comercioExterior,
			array(
				'NumRegIdTrib' => $NumRegIdTrib,
				'ResidenciaFiscal' => $ResidenciaFiscal
			)
		);
	}

	public function setReceptor($Calle, $Estado, $Pais, $CodigoPostal,
		$NumeroExterior=null, $NumeroInterior=null, $Colonia=null, $Localidad=null, $Referencia=null, $Municipio=null,
		$NumRegIdTrib=null
	){
		$data = array();
		if(!$this->valueIsEmpty($NumRegIdTrib)) {
			$data['NumRegIdTrib'] = $NumRegIdTrib;
		}

		$this->ceReceptor = $this->document->addElement(
			'cce11:Receptor',
			$this->comercioExterior,
			$data
		);

		$data = array();
		$data['Calle'] = $Calle;
		if(!$this->valueIsEmpty($NumeroExterior)) $data['NumeroExterior'] = $NumeroExterior;
		if(!$this->valueIsEmpty($NumeroInterior)) $data['NumeroInterior'] = $NumeroInterior;
		if(!$this->valueIsEmpty($Colonia)) $data['Colonia'] = $Colonia;
		if(!$this->valueIsEmpty($Localidad)) $data['Localidad'] = $Localidad;
		if(!$this->valueIsEmpty($Referencia)) $data['Referencia'] = $Referencia;
		if(!$this->valueIsEmpty($Municipio)) $data['Municipio'] = $Municipio;
		$data['Estado'] = $Estado;
		$data['Pais'] = $Pais;
		$data['CodigoPostal'] = $CodigoPostal;

		$this->document->addElement(
			'cce11:Domicilio',
			$this->ceReceptor,
			$data
		);
	}

	public function setDestinatario($NumRegIdTrib, $Nombre){
		$this->document->addElement(
			'cce11:Destinatario',
			$this->comercioExterior,
			array(
				'NumRegIdTrib' => $NumRegIdTrib,
				'Nombre' => $Nombre
			)
		);
	}

	public function addMercancia($NoIdentificacion, $ValorDolares,
		$FraccionArancelaria=null, $CantidadAduana=null, $UnidadAduana=null, $ValorUnitarioAduana=null
	){
		$data = array();
		$data['NoIdentificacion'] = $NoIdentificacion;
		$data['ValorDolares'] = UtilCfdi::valorImporte($ValorDolares, 'USD');
		if(!$this->valueIsEmpty($FraccionArancelaria)) $data['FraccionArancelaria'] = $FraccionArancelaria;
		if(!$this->valueIsEmpty($CantidadAduana)) $data['CantidadAduana'] = UtilCfdi::valorImporte($CantidadAduana, 3); // [0-9]{1,14}(.([0-9]{1,3}))?
		if(!$this->valueIsEmpty($UnidadAduana)) $data['UnidadAduana'] = $UnidadAduana;
		if(!$this->valueIsEmpty($ValorUnitarioAduana)) $data['ValorUnitarioAduana'] = UtilCfdi::valorImporte($ValorUnitarioAduana, 'USD');

		if(!$this->ceMercancias) {
			$this->ceMercancias = $this->document->addElement(
				'cce11:Mercancias',
				$this->comercioExterior
			);
		}

		return $this->document->addElement(
			'cce11:Mercancia',
			$this->ceMercancias,
			$data
		);
	}

	public function addDescripcionEspecifica($mercancia, $Marca,
		$Modelo=null, $SubModelo=null, $NumeroSerie=null
	){
		$data = array();
		$data['Marca'] = $Marca;
		if(!$this->valueIsEmpty($Modelo)) $data['Modelo'] = $Modelo;
		if(!$this->valueIsEmpty($SubModelo)) $data['SubModelo'] = $SubModelo;
		if(!$this->valueIsEmpty($NumeroSerie)) $data['NumeroSerie'] = $NumeroSerie;

		$this->document->addElement(
			'cce11:DescripcionesEspecificas',
			$mercancia,
			$data
		);
	}
}