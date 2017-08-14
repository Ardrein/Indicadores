<?php

include 'lecturaDocumento.php';
$lectura = lecturaDocumento::conArchivo('Indicators.xlsx');
?>


<div ng-init='initLists(<?php echo json_encode($lectura->getVariables());?>, <?php echo json_encode($lectura->getIndicadores());?>)' class="contenido">

	<div ng-repeat="variable in varsc.variables" class="variable" lista>

		<div class="var-cabecera">
			<span>
				<b>Variable: </b>{{variable.Name}}
				<br>
				<b>Type: </b>{{variable['Data type']}}
			</span>
		</div>
		<div class="var-descripcion">
			<b>Description: </b>{{variable['Long name']}}
		</div>
		<div class="var-valor">
			<b>Value: </b>
			<input type="number" step="any" ng-model="variable.Value">
		</div>

	</div>
</div>

<div class="footer">
	<button type="button" ng-click="asignarVariables()">SUBMIT</button>
</div>
