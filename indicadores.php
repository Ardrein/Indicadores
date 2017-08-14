<?php
include 'lecturaDocumento.php';


$lecturaDocumento =  new lecturaDocumento();

$postdata = json_decode(file_get_contents("php://input"),true);
$request = $postdata;
@$indicadores = $request['indicadores'];
@$variables = $request['variables'];


$lecturaDocumento->setVariables($variables);
$lecturaDocumento->setIndicadores($indicadores);

$indicadores=$lecturaDocumento->asignarVariables();


for($i = 0; $i<count($indicadores); $i++){
	foreach($variables as $variable){
		$indicadores[$i]['Formula'] = preg_replace('/\b'.$variable['Name'].'\b/u',$variable['Value'],$indicadores[$i]['Formula']);
		
	}
	$indicadores[$i]['Formula'] = '=('.$indicadores[$i]['Formula'].')';
	$indicadores[$i]['Formula']= $lecturaDocumento->calcularFormula($indicadores[$i]['Formula'] );
}




echo json_encode($indicadores);
?>
