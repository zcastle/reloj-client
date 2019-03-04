<?php

use Slim\Http\Request;
use Slim\Http\Response;
//
//use \ZKLib\ZKLib;
//
use Lib\Reloj;
use Lib\Data;
// Routes

$app->get("/crear", function(){
	$reloj = new Reloj("10.10.10.250");
	echo $reloj->setUser(69, 96, "jc");
	echo "crear";
});

$app->group("/reloj/v1", function(\Slim\App $app){

    $app->get('/get', function(Request $request, Response $response, $args) {
        //$this->logger->info("Slim-Skeleton '/' route");
        $return = array("success" => true, "message" => null);

        $rows = array();
        if(true){
            try{
                $reloj = new Reloj("10.10.10.250");
                $rows = $reloj->get();
            }catch(Exception $e){
                $return["message"] = "Error desconocido";
            }
        }else{
            for ($i=1; $i <= 5; $i++) { 
                array_push($rows, array(
                    "codigo" => $i,
                    "reloj_serie" => "serialNumber",
                    "fecha_hora" => date("Y-m-d H:i:s")
                ));
            }
        }

	//$return["data"] = $rows;

        $data = new Data($this->db, $this->logger);
        foreach($rows AS $row){
            if(!$data->existeRegistro($row["codigo"], $row["reloj_serie"], $row["fecha_hora"])){
                $data->insertarRegistro($row["codigo"], $row["reloj_serie"], $row["fecha_hora"]);
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
});
