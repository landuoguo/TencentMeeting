<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="UTF-8">
		<title>特殊情况申请表</title>
		<?php include_once $_SERVER['DOCUMENT_ROOT']."/public/js.html"; ?>
	</head>
	<body>

		<div style="width: 95%;max-width: 600px;margin:auto;">
			<p style="width: 100%;text-align: center;font-size: 22px;margin-top: 30px;">晚自习特殊情况申请表</p>
			<form id="sub" onsubmit="return false" method="post" style="margin-left: 10px;">
				<p style="color:orange;font-size:14px;">有特殊情况要<span style="color:red">及时</span>填写本表上报，超过事发时间5分钟以上不予受理！</p>
				<p>
					<label>座号</label>
					<input type="number" name="uid" size="2" class="form-control" />
				</p>
				<p>
					<label>申请原因</label>&nbsp;&nbsp;&nbsp;
					<input type="text" name="reason" size="15" class="form-control" />
					<span style="color:red;font-size:14px;">*此处原因请简要填写，如：请假（要和班主任先申请）/上厕所/吃饭/传作业/看题目，如有详细的解释，请在下方的备注中说明！</span>
				</p>
				<p>
					<label>备注</label>
					<input type="text" name="notes" size="200" class="form-control" />
				</p>
                <p>
					<label>开始时间</label>
					<input type="datetime-local" name="start_time" class="form-control" />
					<span style="color:red;font-size:14px;">*开始时间与申请表提交时间之间时差不得超过5分钟</span>
				</p>
				<p>
					<label>持续时间（单位：分钟）</label>
					<input type="number" name="duration" size="4" class="form-control" />
					<span style="color:red;font-size:14px;">*上厕所不得超过6min，传作业不得超过8min，吃饭不得超过20min，看题目不得超过15min，如超过上限，可能会被驳回，其它事由会酌情处理</span>
				</p>
				<p style="color:orange;font-size:14px;">请不要重复提交申请表，否则将按恶意提交处理</p>
				<button type="submit" class="btn btn-primary" onclick="sub()">提交</button>
			</form>
			<img style="width:100%;height:auto;margin-top:40px;" src="/img/ddxz.jpg"/>
			<!--提示信息框-->
			<div style="margin-left: 30px;margin-top: 20px;">
				<div class='alert alert-danger' style="display:none;" role='alert' id='msg'></div>
			</div>
		</div>
	</body>


<script type="text/javascript">
		function sub() {
			$.ajax({
				type: "POST",
				dataType: "json",//预期服务器返回的数据类型
				url: "compo/apply.php", 
				data: $('#sub').serialize(),
				success: function(result) {
					console.log(result);
					if (result.resultcode == 200) {
						window.location.href="show_rank.php";
					};
					if (result.resultcode != 200) {
						$("#msg").attr("style","display:block;")
						$("#msg").text("Err"+ result.resultcode + result.msg)
					};
				},
				error: function(err) {
					$("#msg").attr("style","display:block")
					$("#msg").text("Err 服务器异常")
					console.log(err)
				}
			});
		}
	</script>
</html>
