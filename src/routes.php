<?php

use Slim\Http\Request;
use Slim\Http\Response;
//
//use \ZKLib\ZKLib;
//
use Lib\Reloj;
use Lib\Data;
// Routes

/*$app->get("/crear", function(){
	$reloj = new Reloj("192.168.0.250");
	echo $reloj->setUser(5001, 5001, "MAS");
	echo "crear";
});

$app->get("/test", function(){
    $st = "000001003433303439353830000000000000000000000000000000000189b5bb24000000000000";
	echo intval( str_replace("\0", '', hex2bin( substr($st, 6, 22) ) ) );
});*/

$app->group("/reloj/v1", function(\Slim\App $app){

    $app->get('/get', function(Request $request, Response $response, $args) {
        $return = array("success" => true, "message" => null);

        $reloj = null;
        try{
            $reloj = new Reloj("192.168.1.201"); // 201-PRIMAVERA 10.10.10.250-ANGAMOS
            $rows = $reloj->get();
        
            //$return["data"] = $rows;

            $data = new Data($this->db, $this->logger);
            foreach($rows AS $row){
                if(!$data->existeRegistro($row["codigo"], $row["reloj_serie"], $row["fecha_hora"])){
                    $data->insertarRegistro($row["codigo"], $row["reloj_serie"], $row["fecha_hora"]);
                }
            }
            $reloj->close();

        }catch(Exception $e){
            $return["message"] = $e->getMessage();
        }finally{
            if($reloj != null){
                $reloj->close();
            }
        }
        return $response->withJson($return);
    });

    $app->get('/sync', function(Request $request, Response $response, $args) {
        $result = array("success" => true, "message" => null);

        $body = array("success" => true, "data" => null);
        $data = new Data($this->db, $this->logger);
        $rows = $data->getData();
        $count = count($rows);
        if($count > 0){
            $body["data"] = base64_encode($rows);

            $url = "http://localhost:8181/reloj/v1/server";
            $res = \Httpful\Request::post($url)
                ->expectsJson()
                ->sendsJson()
                ->body($body)
                ->send();

            if(!$res->body->error){
                $data->setEnviado();
                $result["message"] = "Sincronizado correctamente $count registros";
            } else {
                $result["message"] = $res->body->message;
            }
        }else{
            $result["message"] = "No hay informacion para sincronizar";
        }

        return $response->withJson($result);
    });

    /*$app->post("/create", function(Request $request, Response $response, $args){
        $result = array("success" => true);

        $body = $request->getParsedBody();

        $reloj = new Reloj("10.10.10.250");
        //$reloj->setUser(69, 96, "jc");
        $reloj->setUser($body["id"], $body["codigo"], $body["nombre"]);
        
        return $response->withJson($result);
    });*/
});
