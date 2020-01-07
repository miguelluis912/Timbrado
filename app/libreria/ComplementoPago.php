<?php

class ComplementoPago extends CfdiBase {
	public $document;
	public $pago;


	public function __construct(){
		parent::__construct();
		$this->document = new UtilDocumento();
		
		$data = array();
		$data['Version'] = '1.0';

		$this->pago = $this->document->addElement(
			'pago10:Pagos',
			null,
			$data
		);
	}

	public function setElement($document, $parent=null, $params=null) {
		$node = $document->importNode(
			$this->document->documentElement,
			true
		);
		return $parent->appendChild($node);
	}

	public function addPago($FechaPago, $FormaDePagoP, $MonedaP, $TipoCambioP, $Monto, $NumOperacion=null, $RfcEmisorCtaOrd=null, $NomBancoOrdExt=null, $CtaOrdenante=null, $RfcEmisorCtaBen=null, $CtaBeneficiario=null, $TipoCadPago=null, $CertPago=null, $CadPago=null, $SelloPago=null){

		if(is_int($FechaPago)) $FechaPago = date('Y-m-d\TH:i:s', $FechaPago);

		$data = array();
		$data['FechaPago'] = $FechaPago;
		$data['FormaDePagoP'] = $FormaDePagoP;
		$data['MonedaP'] = $MonedaP;
		if($MonedaP != 'MXN') {
			// Se debe registrar el tipo de cambio de la moneda a la fecha en que se recibió el pago, cuando el
			// campo MonedaP sea diferente a MXN (Peso Mexicano), en este caso el valor de este campo
			// debe reflejar el número de pesos mexicanos que equivalen a una unidad de la divisa señalada en
			// el campo MonedaP.
			$data['TipoCambioP'] = UtilCfdi::valorTipoCambio($TipoCambioP);
		}
		$data['Monto'] = UtilCfdi::valorImporte($Monto, $MonedaP);

		if(!$this->valueIsEmpty($NumOperacion))
			$data['NumOperacion'] = $NumOperacion;
		if(!$this->valueIsEmpty($RfcEmisorCtaOrd))
			$data['RfcEmisorCtaOrd'] = $RfcEmisorCtaOrd;
		if(!$this->valueIsEmpty($NomBancoOrdExt))
			$data['NomBancoOrdExt'] = $NomBancoOrdExt;
		if(!$this->valueIsEmpty($CtaOrdenante))
			$data['CtaOrdenante'] = $CtaOrdenante;
		if(!$this->valueIsEmpty($RfcEmisorCtaBen))
			$data['RfcEmisorCtaBen'] = $RfcEmisorCtaBen;
		if(!$this->valueIsEmpty($CtaBeneficiario))
			$data['CtaBeneficiario'] = $CtaBeneficiario;
		if(!$this->valueIsEmpty($TipoCadPago))
			$data['TipoCadPago'] = $TipoCadPago;
		if(!$this->valueIsEmpty($CertPago))
			$data['CertPago'] = $CertPago;
		if(!$this->valueIsEmpty($CadPago))
			$data['CadPago'] = $CadPago;
		if(!$this->valueIsEmpty($SelloPago))
			$data['SelloPago'] = $SelloPago;

		return $this->document->addElement(
			'pago10:Pago',
			$this->pago,
			$data
		);
	}

	public function addDoctoRelacionado($item, $IdDocumento, $Serie, $Folio, $MonedaDR, $TipoCambioDR, $MetodoDePagoDR, $NumParcialidad, $ImpSaldoAnt, $ImpPagado, $ImpSaldoInsoluto){
		$data = array();
		$data['IdDocumento'] = $IdDocumento;

		if(!$this->valueIsEmpty($Serie))
			$data['Serie'] = $Serie;
		if(!$this->valueIsEmpty($Folio))
			$data['Folio'] = $Folio;
		if(!$this->valueIsEmpty($MonedaDR))
			$data['MonedaDR'] = $MonedaDR;
		if(!$this->valueIsEmpty($TipoCambioDR))
			$data['TipoCambioDR'] = UtilCfdi::valorTipoCambio($TipoCambioDR);
		if(!$this->valueIsEmpty($MetodoDePagoDR))
			$data['MetodoDePagoDR'] = $MetodoDePagoDR;
		if(!$this->valueIsEmpty($NumParcialidad))
			$data['NumParcialidad'] = $NumParcialidad;
		if(!$this->valueIsEmpty($ImpSaldoAnt))
			$data['ImpSaldoAnt'] = UtilCfdi::valorImporte($ImpSaldoAnt, $MonedaDR);
		if(!$this->valueIsEmpty($ImpPagado))
			$data['ImpPagado'] = UtilCfdi::valorImporte($ImpPagado, $MonedaDR);
		if(!$this->valueIsEmpty($ImpSaldoInsoluto))
			$data['ImpSaldoInsoluto'] = UtilCfdi::valorImporte($ImpSaldoInsoluto, $MonedaDR);

		$this->document->addElement(
			'pago10:DoctoRelacionado',
			$item,
			$data
		);
	}
}