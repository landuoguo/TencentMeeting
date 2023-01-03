<?php
date_default_timezone_set('PRC');#设置时区

//检查数据是否填写完整
if(empty($_POST['uid']) || empty($_POST['reason']) || empty($_POST['start_time']) || empty($_POST['duration'])){
  $out = array ('resultcode'=>405,'msg'=>'信息填写不完整');
  echo json_encode($out);
  exit;
}

//写入变量
$uid = $_POST['uid'];
$reason = $_POST['reason'];
$start_time = $_POST['start_time'];
$duration = $_POST['duration'];
$notes = $_POST['notes'];

if($duration<=0){
  $out = array ('resultcode'=>405,'msg'=>'持续时间不能小于或等于0');
  echo json_encode($out);
  exit;
}

$time1 = new DateTime('now -5 minutes');
$time2 = new DateTime('now +5 minutes');
$time3 = new DateTime($start_time);
if($time1>$time3 || $time3>$time2){
  $out = array ('resultcode'=>405,'msg'=>'申请的开始时间与申请表提交时间之间时差不得超过5分钟');
  echo json_encode($out);
  exit;
}

$start_time_string = $time3->format('Y-m-d H:i:s');
$end_time = new DateTime($start_time.' +'.$duration.' minutes');
$end_time_string = $end_time->format('Y-m-d H:i:s');

function get_ip(){
  if (getenv("HTTP_CLIENT_IP"))
      $ip = getenv("HTTP_CLIENT_IP");
  else if(getenv("HTTP_X_FORWARDED_FOR"))
      $ip = getenv("HTTP_X_FORWARDED_FOR");
  else if(getenv("REMOTE_ADDR"))
      $ip = getenv("REMOTE_ADDR");
  else $ip = "Unknow";
  if(preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip))
      return $ip;
  else
      return '';
}
$ip=get_ip();

include_once $_SERVER['DOCUMENT_ROOT']."/yzwk_conn.php";//数据库连接

//写入数据
$sql = "INSERT INTO apply (uid,start_time,end_time,duration,reason,notes,examine_state,ip)
VALUES($uid,'$start_time_string','$end_time_string','$duration','$reason','$notes',1,'$ip')
";
$res = $conn->query($sql);
if($res){
  $out = array ('resultcode'=>200);
} else {
  $out = array ('resultcode'=>500,'msg'=>'写入失败，请重试！');
}

echo json_encode($out);