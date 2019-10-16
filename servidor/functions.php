<?php
require_once 'connect.php';

function conecta(){
    try{
        //cadena de conexión
        $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        //tipos de errores  considerar
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e){
        //echo($e->getMessage());
        //echo($e->getCode());
        die("sin_conexion");
    }
    //devuelve el objeto PDO o el string sin_conexión
}

function getDataIP($ip){
    $key="at_CJIb4Bv48betCL6E6u19u8OnP7dZ3";
    $url='https://geo.ipify.org/api/v1?apiKey='.$key.'&ipAddress='.$ip;
    $valoresDevueltos=file_get_contents($url);
    $ipData=json_decode($valoresDevueltos);
    $country=$ipData->{'location'}->{'country'};
    $city=$ipData->{'location'}->{'city'};
    return array($country, $city);
}


function getPlayers(){
    $pdo = conecta();
    $sql = "SELECT * FROM jugadores";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();


    if ($stmt->rowCount() > 0){
        $players=[];
        while($row = $stmt->fetch()){
            //"id"=>$row['id'],
            $player=array(
                "alias"=>$row["alias"],
                "punt_max"=>$row['punt_max']
            );
            $players[]=$player;
        }
        echo json_encode($players,JSON_NUMERIC_CHECK);
    }
    else{
        echo json_encode(array("error"=>"no hay jugadores"));
    }
    unset($stmt);
    unset($pdo);
}

function generarHuella(){
    $alfabeto = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = "";
    $cantValores = strlen($alfabeto) - 1; //cantidad de valores
    for ($i = 0; $i < 8; $i++) {
        $el = rand(0, $cantValores);
        $pass= $pass.$alfabeto[$el];
    }
    return $pass; 
}

function addPlayer($alias, $ip){
    $pdo = conecta();
    $sql = "INSERT INTO jugadores(alias,punt_max,huella,ip,pais,ciudad) values (?,?,?,?,?,?)";
    $huella= generarHuella();
    
    $dataIP= ($ip!="")?getDataIP($ip):"";
    $paisIP= ($dataIP!="")?$dataIP[0]:"";
    $ciudadIP= ($dataIP!="")?$dataIP[1]:"";

    $stmt = $pdo->prepare($sql);
    $resp=$stmt->execute(array($alias,0,$huella,$ip,$paisIP,$ciudadIP));
    $lastID= $pdo->lastInsertId();    

    unset($stmt);
    unset($pdo);

    $huella= hash('tiger128,3', $huella); # huella como hash con sal
    $ret= $resp?array("id"=>$lastID,"huella"=>$huella):"error";
    return $ret;
}

function addPartida($huella, $idJugador, $idMensaje, $tiempo, $puntos){
    
    $dataPlayer= getDataPlayer($idJugador);
    $huellaOriginal=$dataPlayer["huella"];
    $comparar=compararHuella($huella,$huellaOriginal);
    if ($comparar){
        $pdo= conecta();
        $sql="INSERT INTO partida(idjugador,idmensaje,tiempo,puntos) values (?,?,?,?)";
        $stmt=$pdo->prepare($sql);
        $resp=$stmt->execute(array($idJugador, $idMensaje, $tiempo, $puntos));

        $retorno= $resp?"listo":"errorDB";
        if ($retorno=="listo" && $puntos>$dataPlayer["punt_max"]){
            $sql= "UPDATE jugadores SET punt_max=? WHERE id=?";
            $stmt=$pdo->prepare($sql);
            $resp=$stmt->execute(array($puntos,$idJugador));

            $retorno= $resp?"listo":"errorDoble";
        }

        unset($stmt);
        unset($pdo);       
    }
    else{
        $retorno= "error auth".$comparar;
    }
    return $retorno;
}

function getRandomMensaje(){
    $pdo=conecta();
    $sql="SELECT * FROM mensajes ORDER BY RAND() LIMIT 1";

    $stmt=$pdo->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0){
        $row = $stmt->fetch();
        $mensaje=array(
            "id"=>$row['id'], 
            "descripcion"=>$row['descripcion']
        );
        echo json_encode($mensaje);
    }
    else{
        echo json_encode(array("error"=>"no hay mensajes"));
    }
    unset($stmt);
    unset($pdo);    
}

function getReport(){
    $pdo= conecta();
    $sql= "SELECT pais, ciudad, COUNT(ciudad) as cantidad FROM jugadores GROUP BY ciudad";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0){
        $cities=[];
        while($row = $stmt->fetch()){
            $city=array(
                "pais"=>$row["pais"],
                "ciudad"=>$row["ciudad"],
                "cantidad"=>$row['cantidad']
            );
            $cities[]=$city;
        }
        echo json_encode($cities,JSON_NUMERIC_CHECK);
    }
    unset($stmt);
    unset($pdo);   
}

function getPlayer($id){
    $pdo = conecta();
    $sql = "SELECT * FROM jugadores WHERE id=?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id));


    if ($stmt->rowCount() > 0){
        $players=[];
        while($row = $stmt->fetch()){
            //"id"=>$row['id'],
            $player=array(
                "alias"=>$row["alias"],
                "punt_max"=>$row['punt_max'],
                "last"=>$row['f_update'],
            );
            $players[]=$player;
        }
        echo json_encode($players);
    }
    else{
        echo json_encode(array("error"=>"no hay jugadores"));
    }
    unset($stmt);
    unset($pdo);
}

function getDataPlayer($id){
    $pdo = conecta();
    $sql = "SELECT * FROM jugadores WHERE id=?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($id));


    if ($stmt->rowCount() > 0){
        $row = $stmt->fetch();
        $player=array("huella"=>$row['huella'],"punt_max"=>$row['punt_max'],);
        return $player;
        //$player["huella"];
    }
    else{
        return "error";
    }    
    unset($stmt);
    unset($pdo);    
}


function compararHuella($esperado, $original){
    $huellaOriginal= hash('tiger128,3', $original);
    return hash_equals($esperado, $huellaOriginal);   
}


?>
