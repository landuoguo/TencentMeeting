<?php
date_default_timezone_set('PRC');#设置时区
include_once $_SERVER['DOCUMENT_ROOT']."/yzwk_conn.php";//数据库连接

$sql1 = "SELECT user.uid,user.name,count(*) AS count
FROM capture_data,user
WHERE capture_data.is_vaild=1
AND user.uid = capture_data.uid
GROUP BY capture_data.uid
ORDER BY count(*) DESC
LIMIT 30
";
$result = $conn->query($sql1);
$base_list = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $base_list[] = $row;
    }
}

foreach($base_list as $i=>$it){
    $now_uid = $it['uid'];
$sql2="SELECT count(*) AS apply_times
FROM capture_data,apply
WHERE capture_data.uid = $now_uid
AND capture_data.uid = apply.uid
AND capture_data.is_vaild=1
AND apply.examine_state = 1
AND (capture_data.capture_time>=apply.start_time AND capture_data.capture_time<=apply.end_time)
";
    $res2 = $conn->query($sql2);
    if ($res2->num_rows > 0) {
        while($row = $res2->fetch_assoc()) {
            $base_list[$i]['apply_times'] = $row['apply_times'];
        }
    }else{
        echo "err";
    }

}

foreach($base_list as $i=>$it){
    $base_list[$i]['estimate_time'] =round(($it['count']-$it['apply_times'])*9.7/60,1);
}

array_multisort(array_column($base_list,'estimate_time'),SORT_DESC,$base_list);

//echo json_encode($base_list);
$now_time = new DateTime('now');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>xxx排行榜</title>
        <?php include_once $_SERVER['DOCUMENT_ROOT']."/public/js.html"; ?>
    </head>
    <body>
        <div style="max-width: 800px;margin:auto auto">
        <h3 style="text-align:center;margin-top:30px">摄像头实时“排行榜”</h3>
        <p style="color:orange;font-size:14px;">生成时间：<?php echo $now_time->format('Y-m-d H:i:s');?></p>
        <table class="table" id="idx1-1" >
            <tbody>
                <tr>
                    <td>排名</td>
                    <td>座号</td>
                    <td>姓名</td>
                    <td>被检测次数</td>
                    <td>核销次数</td>
                    <td>预估没开摄像头时间（min）</td>
                </tr>
                <tr v-for="item,index in items">
                    <td style="color:orange">{{index}}</td>
                    <td style="color:green">{{item.uid}}</td>
                    <td style="color:brown">{{item.name}}</td>
                    <td style="color:brown">{{item.count}}</td>
                    <td style="color:green">{{item.apply_times}}</td>
                    <td style="color:red">{{item.estimate_time}}</td>
                </tr>
            </tbody>
        </table>
        <p style="color:grey;font-size:14px;">*以上数据仅供参考，如有异议请及时联系网管核实</p>
        <svg class="icon" style="width:70px;height:auto;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M512 85.33312C323.499138 85.33312 170.66752 238.164738 170.66752 426.6656s152.831618 341.33248 341.33248 341.33248 341.33248-152.831618 341.33248-341.33248S700.500862 85.33312 512 85.33312zM85.3344 426.6656C85.3344 191.032411 276.366811 0 512 0c235.661633 0 426.6656 191.032411 426.6656 426.6656 0 235.661633-191.003967 426.6656-426.6656 426.6656-235.633189 0-426.6656-191.003967-426.6656-426.6656z m127.99968 554.66528A42.66656 42.66656 0 0 1 256.00064 938.66432h511.99872a42.66656 42.66656 0 1 1 0 85.33312H256.00064a42.66656 42.66656 0 0 1-42.66656-42.66656z"></path><path d="M459.776131 775.166062a42.239894 42.239894 0 0 1 19.114618 56.888747l-85.560675 169.642242a42.951004 42.951004 0 0 1-56.774969 17.806178 42.239894 42.239894 0 0 1-19.71195-55.722527l85.560675-169.642243a42.951004 42.951004 0 0 1 57.372301-18.972397z m132.892112 0a42.239894 42.239894 0 0 0-19.114619 56.888747l85.560675 169.642242a42.951004 42.951004 0 0 0 56.774969 17.806178 42.239894 42.239894 0 0 0 19.711951-55.722527l-85.560675-169.642243a42.951004 42.951004 0 0 0-57.372301-18.972397zM526.222187 321.136975c-61.439846 0-111.2175 49.350988-111.2175 110.250391 0 60.927848 49.777653 110.278835 111.2175 110.278835s111.2175-49.350988 111.217499-110.278835c0-60.870959-49.777653-110.250391-111.217499-110.250391z m-196.806619 110.250391c0-107.747286 88.120669-195.099957 196.806619-195.099957 108.685951 0 196.806619 87.352671 196.806619 195.128401 0 107.747286-88.120669 195.099957-196.806619 195.099957-108.685951 0-196.806619-87.352671-196.806619-195.128401zM526.222187 28.444373c23.60883 0 42.780337 19.000841 42.780337 42.410561v63.630063c0 23.409719-19.143063 42.382116-42.780337 42.382116-23.60883 0-42.780337-18.972397-42.780338-42.382116V70.82649c0-23.409719 19.143063-42.410561 42.780338-42.410561z"></path></svg>
        <!--<img style="width:30%;height:auto;margin:auto" src="/img/rabbit.svg"/>-->
        </div>

        <script>
			
			var vm = new Vue({
				el: '#idx1-1',
				data: {
					items: <?php echo json_encode($base_list) ?>
				},
				methods: {
					gourl: function(item){
						window.location="collect.php?collectid=" + item.collectid
					},
                }
			});    
        </script>
    </body>
</html>