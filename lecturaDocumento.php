<?php

 //libreria para leer hojas de calculo.
 //La carpeta de la libreria, el documento a leer y este archivo deben estar en la misma carpeta
include('PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');

class lecturaDocumento{

//Variables
	private $indicadores;
	private $variables;
	private $indicadoresHeader;
	private $variablesHeader;


//Propiedades
	function getVariables(){
		return $this->variables;
	}

	function setVariables($variables){
		$this->variables = $variables;
	}

	function getIndicadores(){
		return $this->indicadores;
	}

	function setIndicadores($indicadores){
		$this->indicadores = $indicadores;
	}

//Metodos

	function __construct(){
	}

	public static function conArchivo($nombreArchivo){
		$instance = new self();
		$instance->cargarDocumento($nombreArchivo);
		return $instance;
	}

	function cargarDocumento($nombreArchivo){

		try {
			$inputFileType = PHPExcel_IOFactory::identify($nombreArchivo);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($nombreArchivo);

		} catch (Exception $e) {
			die('Error loading file "' . pathinfo($nombreArchivo, PATHINFO_BASENAME) . '": ' . 
				$e->getMessage());
		}

		$worksheets = $objPHPExcel->getWorksheetIterator();

		//hojas de calculo
		foreach ($worksheets as $worksheet) {
  			//filas y columnas de la hoja
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();

			//cabeceras
			if($worksheet->getTitle() == "variables"){
				$this->variablesHeader =  $worksheet->rangeToArray('A1:' . $highestColumn . '1', 
					null, true, false)[0];
			}else{
				$this->indicadoresHeader =  $worksheet->rangeToArray('A1:' . $highestColumn . '1', 
					null, true, false)[0];
				$this->indicadoresHeader[] = "variables";
			}

			//recorrido por las filas de la hoja
			for ($row = 2; $row <= $highestRow; $row++) { 
				$rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, 
					null, true, false)[0];


				if($worksheet->getTitle() == "variables"){
					$variable = array();
					for ($i=0; $i <count($this->variablesHeader) ; $i++) { 
						$variable[$this->variablesHeader[$i]] = $rowData[$i];
					}
					if(count($variable)<4){
						$variable['Value'] = '';
					}

					$this->variables[] = $variable;
				}else{
					$indicador = array();
					$rowData[] = array();
					for ($i=0; $i <count($this->indicadoresHeader) ; $i++) { 
						$indicador[$this->indicadoresHeader[$i]] = $rowData[$i];
					}
					$indicador['Formula2'] = $indicador['Formula'] ;

					$this->indicadores[] = $indicador;
				}

			}
		}

	}

	function asignarVariables(){
		for($i = 0; $i<count($this->indicadores); $i++){
			foreach($this->variables as $variable){
				//if(preg_match('/^'.$variable['Name'].'$|^'.$variable['Name'].'(\W)|(\W)'.
					//$variable['Name'].'(\W)|(\W)'.$variable['Name'].'$/',$this->indicadores[$i]['Formula'])){
				if(preg_match('/\b'.$variable['Name'].'\b/u',$this->indicadores[$i]['Formula'])){
					$this->indicadores[$i]['variables'][] = $variable;
					$this->indicadores[$i]['labels'][] = $variable['Name'];
					$this->indicadores[$i]['values'][] = $variable['Value'];
				}
			}
		}

		return $this->indicadores;
	}

	function imprimirIndicadores(){
		echo '<pre>';
		echo print_r($this->indicadores);
		echo '</pre>';
	}

	function calcularFormula($formula){
		$objPHPExcel = new PHPExcel(); 
		$resultado = '';
		try{
			$resultado = PHPExcel_Calculation::getInstance($objPHPExcel)->calculateFormula($formula, 'A1', $objPHPExcel->getActiveSheet()->getCell('A1'));
		}catch(Exception $e){
			echo $e->getMessage();
		}
		

		return $resultado;
	}

}

?>