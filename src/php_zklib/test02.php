<?php

include("zklib/zklib.php");

$zk = new ZKLib("192.168.2.19", 4370);
$ret = $zk->connect();
sleep(1);
if($ret){
  $zk->disableDevice();
  sleep(1);
  $attendance = $zk->getAttendance();
  sleep(1);
  $data = [];
  while(list($idx, $attendancedata) = each($attendance)){
    /*array_push($data, array(
      "id" => $idx,
      "uid" => $attendancedata[0],
      "id" => $attendancedata[1],
      "status" => ($attendancedata[2] == 14 ? "Check Out" : "Check In"),
      "date" => date("d-m-Y", strtotime($attendancedata[3])),
      "time" => date("H:i:s", strtotime($attendancedata[3]))
    ));*/
    array_push($data, array(
      "id" => $attendancedata[1],
      "date" => date("Y-m-d H:i:s", strtotime($attendancedata[3])),
      "time" => date("Y-m-d", strtotime($attendancedata[3]))
    ));
  }
  $zk->enableDevice();
  sleep(1);
  $zk->disconnect();
  echo json_encode($data);
}
?>
