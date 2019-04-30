<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');
    require_once 'functions.php';

    // POST
    if (isset($_POST["action"]) && !empty($_POST["action"])){
        if ($_POST["action"] == "post"){
            $respuesta=addPlayer($_POST["alias"], strval($_POST["ip"]));
            echo json_encode(array("respuesta"=>$respuesta));           
        }
        else{
            echo json_encode(array("respuesta"=>"La solicitud no es correcta"));
            }
        }
    else{
        // GET
        $idplayer=isset($_GET["id"])?$_GET["id"]:-1;
        if ($idplayer == -1){
            getPlayers();
        }
        else{
            getPlayer($idplayer);
        }
    }

?>