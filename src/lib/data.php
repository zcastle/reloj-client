<?PHP

namespace Lib;

class Data {

  private $db;
  private $logger;

  public function __construct($db, $logger){
    $this->db = $db;
    $this->logger = $logger;
  }

  public function existeRegistro($row){
    return $this->db->table('marcacion')->select("*")
                ->where("codigo", $row["codigo"])
                ->where("reloj_serie", $row["reloj_serie"])
                ->where("estado", $row["estado"])
                ->where("fecha_hora", $row["fecha_hora"])
                ->count() > 0;
  }

  public function insertarRegistro($row){
    $this->db->table('marcacion')->insert([
      "codigo" => $row["codigo"],
      "reloj_serie" => $row["reloj_serie"],
      "estado" => $row["estado"],
      "fecha_hora" => $row["fecha_hora"]
    ]);
  }

  public function getData(){
    return $this->db->table('marcacion')->select("codigo", "reloj_serie", "estado", "fecha_hora")
                    ->where("fecha_enviado", null)->skip(0)->take(100)->get();
  }

  public function setEnviado($row){
    $this->db->table('marcacion')
          ->where("codigo", $row["codigo"])
          ->where("reloj_serie", $row["reloj_serie"])
          ->where("estado", $row["estado"])
          ->where("fecha_hora", $row["fecha_hora"])
          ->where("fecha_enviado", null)
          ->update(['fecha_enviado' => date("Y-m-d H:i:s")]);
  }
}

?>
