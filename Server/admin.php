<?php
include_once $_SERVER['DOCUMENT_ROOT']."/yzwk_conn.php";//数据库连接

if($_GET['pass']!='xxxxxxxx'){#自己设个密码吧
    exit;
}

$sql1 = "SELECT *
FROM apply,user
WHERE user.uid = apply.uid
ORDER BY apply.start_time DESC
";
$result = $conn->query($sql1);
$base_list = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $base_list[] = $row;
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>xxxx管理后台</title>
        <?php include_once $_SERVER['DOCUMENT_ROOT']."/public/js.html"; ?>
    </head>
    <body>
        <div style="max-width: 1400px;margin:auto auto">
        <h3 style="text-align:center;margin-top:30px">管理员后台</h3>
        <table class="table" id="idx1-1" >
            <tbody>
                <tr>
                    <td>ID</td>
                    <td>座号</td>
                    <td>姓名</td>
                    <td>原因</td>
                    <td>开始</td>
                    <td>结束</td>
                    <td>持续时长</td>
                    <td>申请时间</td>
                    <td>备注</td>
                    <td>操作</td>
                </tr>
                <tr v-for="item,index in items">
                    <td style="color:green">{{item.id}}</td>
                    <td style="color:green">{{item.uid}}</td>
                    <td style="color:brown">{{item.name}}</td>
                    <td style="color:brown">{{item.reason}}</td>
                    <td style="color:red">{{item.start_time}}</td>
                    <td style="color:red">{{item.end_time}}</td>
                    <td style="color:red">{{item.duration}} min</td>
                    <td style="color:red">{{item.apply_time}}</td> 
                    <td style="color:grey">{{item.notes}}</td>
                    <td>
                        <button v-if="item.examine_state==1" type="button" class="btn btn-primary btn-sm" v-on:click="bh(item)">驳回</button>
                        <button v-if="item.examine_state==2" type="button" class="btn btn-danger btn-sm">被驳回</button>
                    </td>
                </tr>
            </tbody>
        </table>
        
        </div>

        <script>
			
			var vm = new Vue({
				el: '#idx1-1',
				data: {
					items: <?php echo json_encode($base_list) ?>
				},
				methods: {
					bh: function(item){
                        $.ajax({
                            type: "POST",
                            dataType: "json",//预期服务器返回的数据类型
                            url: "compo/dh.php", 
                            data: {
                                id:item.id
                            },
                            success: function(result) {
                                console.log(result);
                                if (result.resultcode == 200) {
                                    location.reload()
                                };
                                if (result.resultcode != 200) {
                                    window.alert("Err"+ result.resultcode + result.msg)
                                };
                            },
                            error: function(err) {
                                window.alert("Err 服务器异常")
                                console.log(err)
                            }
                        });
						
					},
                }
			});    
        </script>
    </body>
</html>