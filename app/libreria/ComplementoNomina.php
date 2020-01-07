<?php
namespace App\libreria;
use App\libreria\CfdiBase;
use App\libreria\UtilDocumento;

class ComplementoNomina extends CfdiBase {
	public $document;
	public $nomina;
	private $nomina_emisor;
	private $nomina_emisor_sncf;
	private $nomina_receptor;
	private $nomina_receptor_subcont;
	private $nomina_percepciones;
	private $nomina_deducciones;
	private $nomina_otrosPagos;
	private $nomina_incapacidades;

	private $totalPercepciones = 0.0;
	private $totalPercepcionesSueldos = 0.0;
	private $totalPercepcionesIndemnizacion = 0.0;
	private $totalPercepcionesJubilacion = 0.0;
	private $totalDeduccionesOtras = 0.0;
	private $totalOtrosPagos = 0.0;
	private $totalDeduccionesImpuestosRetenidos = 0.0;
	private $totalDeducciones = 0.0;


	public function __construct(){
		parent::__construct();
		$this->document = new UtilDocumento();
	}

	public function getDescuento() {
		return $this->totalDeducciones;
	}
	public function getSubtotal() {
		return $this->totalPercepciones + $this->totalOtrosPagos;
	}
	public function getTotal() {
		return $this->totalPercepciones + $this->totalOtrosPagos - $this->totalDeducciones;
	}

	public function setElement($document, $parent=null, $params=null) {
		$node = $document->importNode(
			$this->document->documentElement,
			true
		);
		return $parent->appendChild($node);
	}

	public function setNomina($TipoNomina, $FechaPago, $FechaInicialPago, $FechaFinalPago, $NumDiasPagados){
		$data = array();

		if(is_int($FechaPago)) $FechaPago = date('Y-m-d', $FechaPago);
		if(is_int($FechaInicialPago)) $FechaInicialPago = date('Y-m-d', $FechaInicialPago);
		if(is_int($FechaFinalPago)) $FechaFinalPago = date('Y-m-d', $FechaFinalPago);

		$data['Version'] = '1.2';
		$data['TipoNomina'] = $TipoNomina;
		$data['FechaPago'] = $FechaPago;
		$data['FechaInicialPago'] = $FechaInicialPago;
		$data['FechaFinalPago'] = $FechaFinalPago;
		$data['NumDiasPagados'] = (float)$NumDiasPagados;

		$this->nomina = $this->document->addElement(
			'nomina12:Nomina',
			null,
			$data
		);
	}

	public function setNominaEmisor($Curp=null, $RegistroPatronal=null, $RfcPatronOrigen=null){
		$data = array();
		if(!empty($Curp)) $data['Curp'] = $Curp;
		if(!empty($RegistroPatronal)) $data['RegistroPatronal'] = $RegistroPatronal;
		if(!empty($RfcPatronOrigen)) $data['RfcPatronOrigen'] = $RfcPatronOrigen;

		$this->nomina_emisor = $this->document->addElement(
			'nomina12:Emisor',
			$this->nomina,
			$data
		);
	}

	public function setNominaEmisorEntidadSNCF($OrigenRecurso, $MontoRecursoPropio=null){
		$data = array();
		$data['OrigenRecurso'] = $OrigenRecurso;
		if(!empty($MontoRecursoPropio)) $data['MontoRecursoPropio'] = $MontoRecursoPropio;

		$this->nomina_emisor_sncf = $this->document->addElement(
			'nomina12:EntidadSNCF',
			$this->nomina_emisor,
			$data
		);
	}

	public function setNominaReceptor($Curp, $NumSeguridadSocial=null, $FechaInicioRelLaboral=null, $Antiguedad=null, $TipoContrato, $Sindicalizado=null, $TipoJornada=null, $TipoRegimen, $NumEmpleado, $Departamento=null, $Puesto=null, $RiesgoPuesto=null, $PeriodicidadPago, $Banco=null, $CuentaBancaria=null, $SalarioBaseCotApor=null, $SalarioDiarioIntegrado=null, $ClaveEntFed){
		$data = array();

		$data['Curp'] = $Curp;
		if(!empty($NumSeguridadSocial)) $data['NumSeguridadSocial'] = $NumSeguridadSocial;
		if(!empty($FechaInicioRelLaboral)) $data['FechaInicioRelLaboral'] = $FechaInicioRelLaboral;
		if(!empty($Antiguedad)) $data['Antigüedad'] = $Antiguedad;
		$data['TipoContrato'] = $TipoContrato;
		if(!empty($Sindicalizado)) $data['Sindicalizado'] = $Sindicalizado;
		if(!empty($TipoJornada)) $data['TipoJornada'] = $TipoJornada;
		$data['TipoRegimen'] = $TipoRegimen;
		$data['NumEmpleado'] = $NumEmpleado;
		if(!empty($Departamento)) $data['Departamento'] = $Departamento;
		if(!empty($Puesto)) $data['Puesto'] = $Puesto;
		if(!empty($RiesgoPuesto)) $data['RiesgoPuesto'] = $RiesgoPuesto;
		$data['PeriodicidadPago'] = $PeriodicidadPago;
		if(!empty($CuentaBancaria)){
			$data['CuentaBancaria'] = $CuentaBancaria;
		}

		// debe incluirse "Banco" solo cuando la CuentaBancaria es una CLABE
		if(strlen($CuentaBancaria) != 18 && !empty($Banco)){
			$data['Banco'] = $Banco;
		}

		if(!empty($SalarioBaseCotApor)) $data['SalarioBaseCotApor'] = $SalarioBaseCotApor;
		if(!empty($SalarioDiarioIntegrado)) $data['SalarioDiarioIntegrado'] = $SalarioDiarioIntegrado;
		$data['ClaveEntFed'] = $ClaveEntFed;

		$this->nomina_receptor = $this->document->addElement(
			'nomina12:Receptor',
			$this->nomina,
			$data
		);
	}

	public function addNominaPercepcion($TipoPercepcion, $Clave, $Concepto, $ImporteGravado, $ImporteExento){
		if(empty($this->nomina_percepciones)){
			$this->setNominaPercepciones();
		}

		$data = array();

		$data['TipoPercepcion'] = $TipoPercepcion;
		$data['Clave'] = $Clave;
		$data['Concepto'] = $Concepto;
		$data['ImporteGravado'] = $ImporteGravado;
		$data['ImporteExento'] = $ImporteExento;

		$item = $this->document->addElement(
			'nomina12:Percepcion',
			$this->nomina_percepciones,
			$data
		);

		if((float)$ImporteGravado > 0){
			$total = (float)$this->nomina_percepciones->getAttribute('TotalGravado')
				+ $ImporteGravado;
			$this->nomina_percepciones->setAttribute(
				'TotalGravado',
				UtilCfdi::valorImporte($total, 'MXN')
			);
		}

		if((float)$ImporteExento > 0){
			$total = (float)$this->nomina_percepciones->getAttribute('TotalExento')
				+ $ImporteExento;
			$this->nomina_percepciones->setAttribute(
				'TotalExento',
				UtilCfdi::valorImporte($total, 'MXN')
			);
		}
		
		if(!in_array($TipoPercepcion, array('022','023','025','039','044'))){
			$total = $this->totalPercepcionesSueldos + $ImporteGravado + $ImporteExento;
			$this->nomina_percepciones->setAttribute(
				'TotalSueldos',
				UtilCfdi::valorImporte($total, 'MXN')
			);
			$this->totalPercepcionesSueldos = $total;
		}

		if(in_array($TipoPercepcion, array('022','023','025'))){
			$total = $this->totalPercepcionesIndemnizacion + $ImporteGravado + $ImporteExento;
			$this->nomina_percepciones->setAttribute(
				'TotalSeparacionIndemnizacion',
				UtilCfdi::valorImporte($total, 'MXN')
			);
			$this->totalPercepcionesIndemnizacion = $total;
		}

		if(in_array($TipoPercepcion, array('039','044'))){
			$total = $this->totalPercepcionesJubilacion + $ImporteGravado + $ImporteExento;
			$this->nomina_percepciones->setAttribute(
				'TotalJubilacionPensionRetiro',
				UtilCfdi::valorImporte($total, 'MXN')
			);
			$this->totalPercepcionesJubilacion = $total;
		}

		$this->totalPercepciones = $this->totalPercepcionesSueldos
			+ $this->totalPercepcionesIndemnizacion
			+ $this->totalPercepcionesJubilacion;

		$this->nomina->setAttribute(
			'TotalPercepciones',
			UtilCfdi::valorImporte($this->totalPercepciones, 'MXN')
		);

		return $item;
	}

	public function addPercepcionHorasExtras($item, $Dias, $TipoHoras, $HorasExtra, $ImportePagado){
		$this->document->addElement(
			'nomina12:HorasExtra',
			$item,
			[
				'Dias'=>$Dias,
				'TipoHoras'=>$TipoHoras,
				'HorasExtra'=>$HorasExtra,
				'ImportePagado'=>$ImportePagado
			]
		);
	}

	public function addNominaDeduccion($TipoDeduccion, $Clave, $Concepto, $Importe){
		if(empty($this->nomina_deducciones)){
			$this->setNominaDeducciones();
		}

		$data = array();

		$data['TipoDeduccion'] = $TipoDeduccion;
		$data['Clave'] = $Clave;
		$data['Concepto'] = $Concepto;
		$data['Importe'] = $Importe;

		$this->document->addElement(
			'nomina12:Deduccion',
			$this->nomina_deducciones,
			$data
		);
		
		if($TipoDeduccion === '002'){
			$total = $this->totalDeduccionesImpuestosRetenidos + $Importe;
			$this->nomina_deducciones->setAttribute(
				'TotalImpuestosRetenidos',
				UtilCfdi::valorImporte($total, 'MXN')
			);
			$this->totalDeduccionesImpuestosRetenidos = $total;
		}else{
			$total = $this->totalDeduccionesOtras + $Importe;
			$this->nomina_deducciones->setAttribute(
				'TotalOtrasDeducciones',
				UtilCfdi::valorImporte($total, 'MXN')
			);
			$this->totalDeduccionesOtras = $total;
		}

		$this->totalDeducciones = $this->totalDeduccionesImpuestosRetenidos
			+ $this->totalDeduccionesOtras;

		$this->nomina->setAttribute(
			'TotalDeducciones',
			UtilCfdi::valorImporte($this->totalDeducciones, 'MXN')
		);
	}

	public function addOtroPago($TipoOtroPago, $Clave, $Concepto, $Importe, $SubsidioAlEmpleo=null, $CompensacionSaldosAFavor=null){
		if(empty($this->nomina_otrosPagos)){
			$this->nomina_otrosPagos = $this->document->addElement(
				'nomina12:OtrosPagos',
				$this->nomina
			);
		}

		$data = array();

		$data['TipoOtroPago'] = $TipoOtroPago;
		$data['Clave'] = $Clave;
		$data['Concepto'] = $Concepto;
		$data['Importe'] = $Importe;

		$otroPago = $this->document->addElement(
			'nomina12:OtroPago',
			$this->nomina_otrosPagos,
			$data
		);

		if($SubsidioAlEmpleo !== null){
			$this->document->addElement(
				'nomina12:SubsidioAlEmpleo',
				$otroPago,
				[
					'SubsidioCausado'=>UtilCfdi::valorImporte($SubsidioAlEmpleo, 'MXN')
				]
			);	
		}

		if(!empty($CompensacionSaldosAFavor)){
			$this->document->addElement(
				'nomina12:CompensacionSaldosAFavor',
				$otroPago,
				[
					'SaldoAFavor'=>UtilCfdi::valorImporte($CompensacionSaldosAFavor['SaldoAFavor'], 'MXN'),
					'Año'=>$CompensacionSaldosAFavor['Ano'],
					'RemanenteSalFav'=>UtilCfdi::valorImporte($CompensacionSaldosAFavor['RemanenteSalFav'], 'MXN')
				]
			);	
		}

		$this->totalOtrosPagos += (float)$Importe;
		
		$this->nomina->setAttribute(
			'TotalOtrosPagos',
			UtilCfdi::valorImporte($this->totalOtrosPagos, 'MXN')
		);
	}

	public function addIncapacidad($TipoIncapacidad, $DiasIncapacidad, $ImporteMonetario=null){
		if(empty($this->nomina_incapacidades)){
			$this->nomina_incapacidades = $this->document->addElement(
				'nomina12:Incapacidades',
				$this->nomina
			);
		}

		$data = array();

		$data['DiasIncapacidad'] = round($DiasIncapacidad);
		$data['TipoIncapacidad'] = $TipoIncapacidad;

		if(!empty($ImporteMonetario)) $data['ImporteMonetario'] = $ImporteMonetario;

		$this->document->addElement(
			'nomina12:Incapacidad',
			$this->nomina_incapacidades,
			$data
		);
	}

	// TODO: Agregar AccionesOTitulos
	// TODO: Agregar JubilacionPensionRetiro
	// TODO: Agregar SeparacionIndemnizacion

	private function setNominaDeducciones($TotalOtrasDeducciones=null, $TotalImpuestosRetenidos=null){
		// Nota: estos valores se calculan automaticamente mediante los hijos

		$data = array();

		if(!empty($TotalOtrasDeducciones)) $data['TotalOtrasDeducciones'] = $TotalOtrasDeducciones;
		if(!empty($TotalImpuestosRetenidos)) $data['TotalImpuestosRetenidos'] = $TotalImpuestosRetenidos;

		$this->nomina_deducciones = $this->document->addElement(
			'nomina12:Deducciones',
			$this->nomina,
			$data
		);
	}

	private function setNominaPercepciones($TotalSueldos=null, $TotalSeparacionIndemnizacion=null, $TotalJubilacionPensionRetiro=null){
		// Nota: estos valores se calculan automaticamente mediante los hijos
		
		$data = array();

		if(!empty($TotalSueldos)){
			$data['TotalSueldos'] = $TotalSueldos;
			$this->totalPercepcionesSueldos = (float)$TotalSueldos;
		}
		if(!empty($TotalSeparacionIndemnizacion)){
			$data['TotalSeparacionIndemnizacion'] = $TotalSeparacionIndemnizacion;
			$this->totalPercepcionesIndemnizacion = (float)$TotalSeparacionIndemnizacion;
		}
		if(!empty($TotalJubilacionPensionRetiro)){
			$data['TotalJubilacionPensionRetiro'] = $TotalJubilacionPensionRetiro;
			$this->totalPercepcionesJubilacion = (float)$TotalJubilacionPensionRetiro;
		}

		$data['TotalGravado'] = '0.00';
		$data['TotalExento'] = '0.00';

		$this->nomina_percepciones = $this->document->addElement(
			'nomina12:Percepciones',
			$this->nomina,
			$data
		);
	}
}