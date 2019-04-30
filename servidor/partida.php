<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');
    require_once 'functions.php';

    if (isset($_POST["action"]) && !empty($_POST["action"])){
        if ($_POST["action"] == "post"){
            //addPartida($idJugador, $idMensaje, $tiempo, $puntos)
            $respuesta=addPartida($_POST["huella"],$_POST["idJugador"], $_POST["idMensaje"],$_POST["tiempo"],$_POST["puntos"]);
            echo json_encode(array("respuesta"=>$respuesta));           
        }
        else{
            echo json_encode(array("respuesta"=>"La solicitud no es correcta"));
            }
        }
    else{
        echo json_encode(array("respuesta"=>"error en la formación del post"));
    }

?>