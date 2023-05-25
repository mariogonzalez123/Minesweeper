<?php
function crearCampoMina(&$campoMinas, &$casillaCubierta) {
     $campoMinas[] = array();
    for ($i = 0; $i < BOARD; $i++) {
        for ($e = 0; $e < BOARD; $e++) {
            $campoMinas[$i][$e] = 0;
        }
    }
    ponerMinas($campoMinas);
           $casillaCubierta[] = array();
    for ($i = 0; $i < BOARD; $i++) {
        for ($e = 0; $e < BOARD; $e++) {
           $casillaCubierta[$i][$e] = true;
        }
    }
}

function ponerMinas(&$campoMinas) {
    for ($i = 0; $i < MINE; $i++) {
        $encontrado = false;
        while (!$encontrado) {
            $X = rand(0, BOARD - 1);
            $Y = rand(0, BOARD - 1);
            if ($campoMinas[$X][$Y] != MINE_TOKEN) {
                $campoMinas[$X][$Y] = MINE_TOKEN;
                $encontrado = true;
                celdasAdyacentesMina($campoMinas, $X, $Y);
            }
        }
    }
}


function celdasAdyacentesMina(&$campoMinas, $x, $y) {
    $filPrev = max(0, $x - 1);
    $filPost = min(BOARD - 1, $x + 1);
    $colPrev = max(0, $y - 1);
    $colPost = min(BOARD- 1, $y + 1);
    for ($i = $filPrev; $i <= $filPost; $i++) {
        for ($e = $colPrev; $e <= $colPost; $e++) {
            if ($campoMinas[$i][$e] != $campoMinas[$x][$y] && $campoMinas[$i][$e] != MINE_TOKEN) {
                $campoMinas[$i][$e]++;
            }
        }
    }
}


function comprobarMinasAdyac($campoMinas, &$casillaCubierta, $x, $y) {
    if ($casillaCubierta[$x][$y]) {
        $casillaCubierta[$x][$y] = false;
        if ($campoMinas[$x][$y] == 0) {
            $filPrev = max(0, $x - 1);
            $filPost = min(BOARD - 1, $x + 1);
            $colPrev = max(0, $y - 1);
            $colPost = min(BOARD - 1, $y + 1);
             for ($i = $filPrev; $i <= $filPost; $i++) {
                for ($e = $colPrev; $e <= $colPost; $e++) {
                    if ($campoMinas[$i][$e] != MINE_TOKEN) {
                        comprobarMinasAdyac($campoMinas, $casillaCubierta, $i, $e);
                    }
                }
            }
        }
    }
}


function descubrirMinas($campoMinas, &$casillaCubierta) {
    for ($i = 0; $i < BOARD; $i++) {
        for ($e = 0; $e < BOARD; $e++) {
            if ($campoMinas[$i][$e] == MINE_TOKEN) {
                $casillaCubierta[$i][$e] = false;
            }
        }
    }
}


function descubrirCeldas($casillaCubierta) {
    $valores = array_merge(...$casillaCubierta);
    $Descubrir = array_filter($valores , function ($celda){
        return !$celda;
    });
    return count($Descubrir);
}

 
function comprobarResultado($campoMinas, &$casillaCubierta, $x, $y) {
    if ($campoMinas[$x][$y] == MINE_TOKEN) {
        descubrirMinas($campoMinas, $casillaCubierta);
        return -1;
    } else {
        if (descubrirCeldas($casillaCubierta) == pow(BOARD, 2) - MINE) {
            descubrirMinas($campoMinas, $casillaCubierta);
            return 1;
        }
    }
}

define("PATH_PIC", "\public\assets\img\bomb.png");
define("BOARD", 8);
define("MINE", 10);
define("MINE_TOKEN",9);

require "vendor/autoload.php";

use eftec\bladeone\bladeone;

$Views = __DIR__ . '\Views';
$Cache = __DIR__ . '\Cache';

$Blade = new BladeOne($Views, $Cache);

session_start();


if (empty($_POST)) {
    
    $campoMinas[] = array();
    $casillaCubierta[] = array();
    crearCampoMina($campoMinas, $casillaCubierta);
    $_SESSION['campoMinas'] = $campoMinas;
    $_SESSION['casillaCubierta'] = $casillaCubierta;
    
    echo $Blade->run('board');
    
} else {

   $campoMinas = $_SESSION['campoMinas'];
    $casillaCubierta = $_SESSION['casillaCubierta'];
    $result["descubrir"] = array();
    $X = filter_input(INPUT_POST, 'x');
    $Y = filter_input(INPUT_POST, 'y');
    comprobarMinasAdyac($campoMinas, $casillaCubierta, $X, $Y);
    $resultado = comprobarResultado($campoMinas, $casillaCubierta, $X, $Y);

       if ($resultado != null) {
        $result["gameRes"] = $resultado;
    }
    for ($i = 0; $i < BOARD; $i++) {
        for ($e = 0; $e < BOARD; $e++) {
            if (!$casillaCubierta[$i][$e]) {
                $result["descubrir"][] = [$i, $e, $campoMinas[$i][$e]];
            }
        }
    }
    
    $_SESSION['casillaCubierta'] = $casillaCubierta;
    

    header('Content-type: application/json');
    echo json_encode($result);
}
