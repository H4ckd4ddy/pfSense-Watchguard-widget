<?php

require_once("guiconfig.inc");

$wg_path = "/conf/";

if(isset($_POST["request"])){
	
	$request = $_POST['request'];
	
	$request = json_decode($request, true);
	
	$action = $request['action'];
	
	$parameters = $request['parameters'];
	
	$response = array();
	
	$temp = explode(':',shell_exec($wg_path."WGXepc -t"))[1];
	$fan_speed = round((hexdec(explode('is ',shell_exec($wg_path."WGXepc -f"))[1])/255)*100);
	
	if($action == "get_data"){
		$response["status"] = "OK";
		$response["temp"] = $temp;
		$response["fan_speed"] = $fan_speed;
	}
	
	echo json_encode($response);
	
}elseif(isset($_POST["WG_AUTO"])){
	$settings = "";
	foreach($_POST as $name => $value) {
		if(substr($name, 0, 3) == "WG_"){
			$settings .= substr($name, 3).":".$value."\n";
		}
	}
	file_put_contents($wg_path."watchguard-settings.txt", $settings);
	shell_exec($wg_path."watchguard.sh");
	echo "<script>history.back();</script>";
}else{
	?>
	<script type="text/javascript">
		function request(action,parameters){
			
			var data = {};
			
			data["action"] = action;
			if(parameters){
				data["parameters"] = parameters;
			}
			
			var xhr = null; 
			if(window.XMLHttpRequest){ // Firefox et autres
				xhr = new XMLHttpRequest(); 
			}else if(window.ActiveXObject){ // Internet Explorer 
				try {
					xhr = new ActiveXObject('Msxml2.XMLHTTP');
				} catch (e) {
					xhr = new ActiveXObject('Microsoft.XMLHTTP');
				}
			}else{
				alert('Votre navigateur ne supporte pas les objets XMLHTTPRequest...'); 
				xhr = false; 
			}
			
			xhr.onreadystatechange = function(){
					
				if( xhr.readyState < 4 ){
					
					//loading
					
				}else if(xhr.readyState == 4 && xhr.status == 200){
					
					var response = JSON.parse(xhr.responseText);
					
					if(response["status"] == "ERROR"){
						alert(response["error"]);
					}else{
						window[action+"_done"](response);
					}
					
				}
				
			}
			
			xhr.open('POST', '/widgets/widgets/watchguard.widget.php', true);
			xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			xhr.send("request="+JSON.stringify(data));
		}
		
		function get_data(){
			request("get_data");
		}
		
		function get_data_done(response){
			document.getElementById("watchguard_temp").innerHTML = response["temp"];
			document.getElementById("watchguard_speed").innerHTML = response["fan_speed"];
		}
		
		setInterval(function(){
			get_data();
		}, 1000);
	</script>
	<style>
	.watchguard td {
		padding: 5px;
	}
	
	.watchguard .left {
		text-align: left;
	}
	
	.watchguard .center {
		text-align: center;
	}
	
	.watchguard .right {
		text-align: right;
	}
	</style>
	<div style="padding: 5px">
		<div id="watchguardContainer" class="listr">
			<center>
				<div id="watchguard_data">
					Temperature : <span id="watchguard_temp"></span>°C
					<br/>
					Speed : <span id="watchguard_speed"></span>%
				</div>
			</center>
		</div>
	</div>
	</div>
	
	<div id="widget-<?=$widgetname?>_panel-footer" class="widgetconfigdiv panel-footer collapse watchguard" >
		<center>
		<form action="/widgets/widgets/watchguard.widget.php" method="POST">
			<table>
                <input type="hidden" name="WG_AUTO" value="1"/>
				<!--<tr>
					<td class="center" colspan="3">
						Mode : 
						<select name="WG_AUTO">
							<option value="1" >Auto</option>
							<option value="0" <?php echo (explode(':',shell_exec("grep AUTO ".$wg_path."watchguard-settings.txt"))[1] != 1)?"selected":""; ?>>Manual</option>
						</select>
					</td>
				</tr>-->
				<?php
				for($wg_i = 1;$wg_i <= 3;$wg_i++){
					echo '<tr>
						<td class="right">
							Level '.$wg_i.'
						</td>
						<td>
							<input name="WG_TEMP_MAX_'.$wg_i.'" type="text" maxlength="2" size="2" value="'.explode(':',shell_exec("grep TEMP_MAX_".$wg_i." ".$wg_path."watchguard-settings.txt"))[1].'" />°C
						</td>
						<td class="right">
							<input name="WG_FAN_'.$wg_i.'" type="text" maxlength="3" size="3" value="'.explode(':',shell_exec("grep FAN_".$wg_i." ".$wg_path."watchguard-settings.txt"))[1].'" />%
						</td>
					</tr>';
				}
				echo "<input type='hidden' name='WG_FAN_SPEED' value='".round((hexdec(explode('is ',shell_exec("/conf/WGXepc -f"))[1])/255)*100)."' />";
				?>
				<tr>
					<td class="center" colspan="3">
						<button type="submit" id="watchguard_widget_submit" name="watchguard_widget_submit" class="btn btn-primary btn-sm" value="Save">
							<i class="fa fa-save icon-embed-btn"></i>
							Save
						</button>
					</td>
				</tr>
			</table>
		</form>
		</center>
	<?php
}
?>