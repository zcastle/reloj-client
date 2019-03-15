<?PHP

namespace Lib;

class Data {

  private $db;
  private $logger;

  public function __construct($db, $logger){
    $this->db = $db;
    $this->logger = $logger;
  }

  public function existeRegistro($codigo, $reloj_serie, $fecha_hora){
    return $this->db->table('marcacion')->select("*")->where("codigo", $codigo)->where("reloj_serie", $reloj_serie)->where("fecha_hora", $fecha_hora)->count() > 0;
  }

  public function insertarRegistro($codigo, $reloj_serie, $fecha_hora){
    $this->db->table('marcacion')->insert([
      "codigo" => $codigo,
      "reloj_serie" => $reloj_serie,
      "fecha_hora" => $fecha_hora
    ]);
  }

  public function getData(){
    return $this->db->table('marcacion')->select("codigo","reloj_serie","fecha_hora")->where("fecha_enviado", null)->skip(0)->take(100)->get();
  }

  public function setEnviado(){
    $this->db->table('marcacion')->where("fecha_enviado", null)->update(['fecha_enviado' => date("Y-m-d H:i:s")]);
  }
}

?>
