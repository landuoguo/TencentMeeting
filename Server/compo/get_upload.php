<?php
$p = $_GET['d'];
$p = json_decode($p,true);

if($p['s']!="*******"){#改为你设置的密钥
    echo "err";
    exit;
}
$jsq = $p['j'];
$xt = $p['xtime'];
$uid = $p['uid'];


include_once $_SERVER['DOCUMENT_ROOT']."/yzwk_conn.php";//数据库连接

//写入数据
$sql = "INSERT INTO capture_data (uid,capture_time,is_vaild)
VALUES ";
for($i=0;$i<$jsq;$i++){
    $sql .= "(".$uid[$i].",'".$xt[$i]."',1)";
    if($i!=($jsq-1)){
        $sql .= ",";
    }
}

$res = $conn->query($sql);
if($res){
  $out = array ('resultcode'=>200);
} else {
  $out = array ('resultcode'=>500,'msg'=>'写入失败，请重试！');
}

echo json_encode($out);