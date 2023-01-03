<?php
date_default_timezone_set('PRC');#设置时区

//写入变量
$id = $_POST['id'];

include_once $_SERVER['DOCUMENT_ROOT']."/yzwk_conn.php";//数据库连接

//写入数据
$sql = "UPDATE apply
SET examine_state=2
WHERE id = $id
";
$res = $conn->query($sql);
if($res){
  $out = array ('resultcode'=>200);
} else {
  $out = array ('resultcode'=>500,'msg'=>'写入失败，请重试！');
}

echo json_encode($out);