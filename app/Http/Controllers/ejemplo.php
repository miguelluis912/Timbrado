<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\libreria\Emisor;
use App\libreria\Receptor;
use App\libreria\ComplementoNomina;
use App\libreria\Comprobante;
use App\libreria\Concepto;
use App\libreria\UtilCertificado;
use App\libreria\autoload;

class ejemplo extends Controller
{

	public function ejemplo(){
		 // Agregar emisor
    // 
$cfdi = new Comprobante();

// Requerido para nomina12
$cfdi->customAttrs['xmlns:nomina12'] = 'http://www.sat.gob.mx/nomina12';
$cfdi->customAttrs['xsi:schemaLocation'] .= ' http://www.sat.gob.mx/nomina12 http://www.sat.gob.mx/sitio_internet/cfd/nomina/nomina12.xsd';

// Preparar valores
$moneda = 'MXN';
$fecha  = time();
$tipoCambio = 1.0;
$folio = '21';
$serie = 'A';
$lugarExpedicion = '12345';
$formaPago = '99';
$metodoPago = 'NA';

// Establecer valores generales
$cfdi->TipoDeComprobante = 'N';
$cfdi->LugarExpedicion   = $lugarExpedicion;
$cfdi->Folio             = $folio;
$cfdi->Serie             = $serie;
$cfdi->TipoCambio        = $tipoCambio;
$cfdi->Moneda            = $moneda;
$cfdi->FormaPago = $formaPago;
$cfdi->MetodoPago = $metodoPago;
$cfdi->setFecha($fecha);

// Agregar emisor
$cfdi->Emisor = Emisor::init(
    'LAN7008173R5',                    // RFC
    '622',                             // Régimen Fiscal
    'Emisor Ejemplo'                   // Nombre (opcional)
);

// Agregar receptor
$cfdi->Receptor = Receptor::init(
    'XAXX010101000',                   // RFC
    'P01',                             // Uso del CFDI
    'Receptor Ejemplo'                 // Nombre (opcional)
);

$complemento = new ComplementoNomina();
$complemento->setNomina(
    'O', // tipo de nómina
    "2018-05-31", // FechaPago
    "2018-05-16", // FechaInicialPago
    "2018-05-31", // FechaFinalPago
    16
);

$complemento->setNominaEmisor(
    null, // curp
    'PROED3', // registro patronal
    null // rfc patron origen
);

// $complemento->setNominaEmisorEntidadSNCF(
//     $c_origen_recurso,
//     $monto_recurso_propio
// );

$complemento->setNominaReceptor(
    "RASG920318HJCMLB07", // Curp
    "12312312333", // NumSeguridadSocial
    "2006-01-04", // FechaInicioRelLaboral
    "P647W", // Antigüedad
    "01", // TipoContrato
    "No", // Sindicalizado
    "01", // TipoJornada
    "02", // TipoRegimen
    "123", // NumEmpleado
    null, // Departamento
    null, // Puesto
    "2", // RiesgoPuesto
    "04", // PeriodicidadPago
    null, // Banco
    null, // CuentaBancaria
    "100", // SalarioBaseCotApor
    "95.68", // SalarioDiarioIntegrado
    "JAL" // ClaveEntFed
);

// Percepciones
$percepcion = $complemento->addNominaPercepcion(
    0, // TipoPercepcion
    "001", // Clave
    "Sueldos, Salarios  Rayas y Jornales", // Concepto
    "1530.88", // ImporteGravado
    "0.00" // ImporteExento
);
// $complemento->addPercepcionHorasExtras(
//     $percepcion,
//     // ...
// );

// Deducciones
$complemento->addNominaDeduccion(
    0, // TipoDeduccion
    "001", // Clave
    "Seguridad social", // Concepto
    "38" // Importe
);

// Otros pagos
$complemento->addOtroPago(
    1, // TipoOtroPago
    "002", // Clave
    "Subsidio para el empleo", // Concepto
    115.44, // Importe
    200.63 // SubsidioCausado
);

// Incapacidades
// $complemento->addIncapacidad();

$concepto = Concepto::init(
    '84111505',
    1,
    'ACT',
    'Pago de nómina',
    $complemento->getSubtotal(),
    $complemento->getSubtotal()
);
$concepto->Descuento = $complemento->getDescuento();
$cfdi->agregarConcepto($concepto);

// obtener totales de los calculos en el complemento
$cfdi->setSubTotal($complemento->getSubtotal());
$cfdi->setTotal($complemento->getTotal());
$cfdi->setDescuento($concepto->Descuento);

$cfdi->Complemento[] = $complemento;

// Mostrar XML del CFDI generado hasta el momento (antes de sellar)
// header('Content-type: application/xml; charset=UTF-8');
// echo $cfdi->obtenerXml();
// die;

// Cargar certificado que se utilizará para generar el sello del CFDI
$cert = new UtilCertificado();
$ok = $cert->loadFiles(
    dirname(__FILE__).DIRECTORY_SEPARATOR.'LAN7008173R5.cer',
    dirname(__FILE__).DIRECTORY_SEPARATOR.'LAN7008173R5.key',
    '12345678a'
);
if(!$ok) {
    die('Ha ocurrido un error al cargar el certificado.');
}

$ok = $cfdi->sellar($cert);
if(!$ok) {
    die('Ha ocurrido un error al sellar el CFDI.');
}

// Mostrar XML del CFDI con el sello
header('Content-type: application/xml; charset=UTF-8');
echo $cfdi->obtenerXml();
die;


	dd($cfdi->Emisor,$cfdi->Receptor, $complemento);


	}
   }
