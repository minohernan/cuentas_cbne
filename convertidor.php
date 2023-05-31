<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');

function convertir($str, $c) {
    $dict = array(
        "Á"=>"A", "É"=>"E", "Í"=>"I", "Ó"=>"O", "Ú"=>"U"
    );
    $mayusculas = strtoupper($str);
    $acentos = str_replace(array_keys($dict), array_values($dict), $mayusculas);
    $long = (strpos($acentos, "Ñ") !== False) ? $c+1 : $c;
    $formato = "%-{$long}s";
    return sprintf($formato, $acentos);
}


if(isset($_FILES['archivo'])) {

    $dias = "Domingo Lunes Martes Miercoles Jueves Viernes Sabado";
    $dias = explode(" ", $dias);

    $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

    $fecha = array(
        $dias[date('w')],
        date('d'),
        $meses[date('n')-1],
        date("H-i")
    );

    $filename = "{$fecha[0]}_{$fecha[1]}_de_{$fecha[2]}_{$fecha[3]}.txt";
    $salida = "C:/xampp/htdocs/cuentas_bne/salida/$filename";

    $archivo = file_get_contents($_FILES['archivo']['tmp_name']);
    $lista_cuentas = explode(chr(10), $archivo);
    array_shift($lista_cuentas);
    array_pop($lista_cuentas);

    $hoy = date("dmY");
    //$encabezado = "AMS".$hoy."AM02007250200ADICIONAL POLICIA";
    //$encabezado = sprintf("%-52s", $encabezado);
    //$encabezado .="\n";

   // file_put_contents($salida, $encabezado);

    foreach($lista_cuentas as $fila) {
        $cols = explode(";", $fila);

        //$secuencial = sprintf("%'06d", $cols[0]);
        $sucursal = sprintf("%'03d", $cols[3]);
        $apellidos = convertir($cols[2], 32);
        $nombres = convertir($cols[3], 32);
        $fecha = sprintf("%'10d", $cols[4]);
        /*$sexo = convertir($cols[5], 1);
        $estado = convertir($cols[6], 1);
        $tipoDoc = sprintf("%'03d", $cols[7]);
        $documento = sprintf("%-13s", $cols[8]);
        $tipodgi = sprintf("%'02d", $cols[9]);
        $tipoNac = sprintf("%'02d", '80');
        $cuil = sprintf("%'013d", $cols[10]);
        $cP = sprintf("%'04d", $cols[11]);
        $provincia = sprintf("%'02d", $cols[12]);
        $localidad = sprintf("%-30s", $cols[13]);
        $calle = sprintf("%-100s", $cols[14]);
        $numero = sprintf("%'05d", $cols[15]);
        $piso = sprintf("%'02d", $cols[16]);
        $depto = sprintf("%'02d", $cols[17]);
        $tel = "           ";
        $legajo = sprintf("%'010d", $cols[19]);
        $sueldoB = sprintf("%'010d", 1);*/

        $elementos_lineas = array($sucursal, $apellidos, $nombres,
            $fecha/*, $sexo, $estado, $tipoDoc, $documento, $tipodgi, $cuil, $tipoNac, $cP,
            $provincia, $localidad, $calle, $numero, $piso, $depto, $tel, $legajo,
    $sueldoB*/ );

        $linea = implode("", $elementos_lineas) . "\n";

        file_put_contents($salida, $linea, FILE_APPEND);
    }

    header('Content-type: text/plain');
    header("Content-Disposition: attachment; filename='$filename'");
    readfile($salida);
}else {
    print "No Se subio el archivo";
}
?>
