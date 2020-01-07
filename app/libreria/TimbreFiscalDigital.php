<?php
/**
 * Clase para generación de CFDI v3.3
 *
 * @author Noel Miranda <noelmrnd@gmail.com>
 * @copyright 2017 PLAN B
 * @version 1.0.2 (29/09/2017)
 */

class TimbreFiscalDigital extends CfdiBase {
	public $Version;
	public $SelloCFD;
	public $NoCertificadoSAT;
	public $RfcProvCertif;
	public $Leyenda;
	public $UUID;
	public $FechaTimbrado;
	public $SelloSAT;


	public function setElement($document, $parent=null, $params=null) {
		$data = array();

		$data['xmlns:tfd'] = 'http://www.sat.gob.mx/TimbreFiscalDigital';
		$data['xsi:schemaLocation'] = 'http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd';

		if(!empty('Version'))
			$data['Version'] = $this->Version;
		if(!empty('SelloCFD'))
			$data['SelloCFD'] = $this->SelloCFD;
		if(!empty('NoCertificadoSAT'))
			$data['NoCertificadoSAT'] = $this->NoCertificadoSAT;
		if(!empty('RfcProvCertif'))
			$data['RfcProvCertif'] = $this->RfcProvCertif;
		if(!empty('Leyenda'))
			$data['Leyenda'] = $this->Leyenda;
		if(!empty('UUID'))
			$data['UUID'] = $this->UUID;
		if(!empty('FechaTimbrado'))
			$data['FechaTimbrado'] = $this->FechaTimbrado;
		if(!empty('SelloSAT'))
			$data['SelloSAT'] = $this->SelloSAT;
		
		return $document->addElement('tfd:TimbreFiscalDigital', $parent, $data);
	}
}