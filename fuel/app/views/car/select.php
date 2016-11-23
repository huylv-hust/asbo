	<tr>
		<th class="text-right">車種</th>
		<td>
			<?php echo  htmlspecialchars_decode($car_maker_code)?>
			<select class="form-control" id="car_model_code" name="car_model_code">
				<option value="0">モデルを選択して下さい</option>
				<?php echo htmlspecialchars_decode($car_model_code) ?>
			</select>

			<span class="text-info"><input type="checkbox" value="1" id="check_model_code" name="check_model_code" <?php echo $check_model_code;?> />左記プルダウン以外のモデル</span>

		</td>
	</tr>
	<tr>
		<th class="text-right">初度登録年月・型式・グレード</th>
		<td>
			<select class="form-control" id="car_year" name="car_year">
				<option value="0">初度登録年を選択して下さい</option>
				<?php echo htmlspecialchars_decode($car_year) ?>
			</select>
			<?php echo htmlspecialchars_decode($car_month) ?>
			<br/><br/>
			<select class="form-control" name="car_type_code" id="car_type_code">
				<option value="0">型式を選択して下さい</option>
				<?php echo htmlspecialchars_decode($car_type_code) ?>
			</select>
			<select class="form-control" name="car_grade_code" id="car_grade_code">
				<option value="0">グレードを選択して下さい</option>
				<?php echo  htmlspecialchars_decode($car_grade_code)?>
			</select>
			<br/>

		</td>
	</tr>
	<script>
		$( "#form_car_maker_code" ).change(function() {

		if($( "#form_car_maker_code option:selected").val()=='0' || $( "#form_car_maker_code option:selected").val()=='')
		{
			$("#car_model_code").html('<option value="">モデルを選択して下さい</option>');
			$("#car_year").html('<option value="">初度登録年を選択して下さい</option>');
			$("#car_type_code").html('<option value="">型式を選択して下さい</option>');
			$("#car_grade_code").html('<option value="">グレードを選択して下さい</option>');
			return;
		}
		document.getElementById("check_model_code").disabled = false;
		$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/car',
				{
					'car_maker_code':$( "#form_car_maker_code option:selected").val(),
					'level':'1'
				},
				function(data){

					$("#car_model_code").html(data);
					$("#car_year").html('<option value=""></option>');
					$("#car_type_code").html('<option value=""></option>');
					$("#car_grade_code").html('<option value=""></option>');
				}
			);
	});
	$( "#car_model_code" ).change(function() {
		if($( "#car_model_code option:selected").val()=='' || $( "#car_model_code option:selected").val()=='0')
		{
			$("#car_year").html('<option value=""></option>');
			$("#car_type_code").html('<option value=""></option>');
			$("#car_grade_code").html('<option value=""></option>');
			document.getElementById("check_model_code").disabled = false;
			return;
		}
		document.getElementById("check_model_code").disabled = true;
		$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/car',
				{
					'car_maker_code':$( "#form_car_maker_code option:selected").val(),
					'car_model_code':$( "#car_model_code option:selected").val(),
					'level':'2'
				},
				function(data){

					$("#car_year").html(data);
					$("#car_type_code").html('<option value=""></option>');
					$("#car_grade_code").html('<option value=""></option>');
				}
			);
	});
	$( "#car_year" ).change(function() {
		if($( "#car_year option:selected").val()=='')
		{
			$("#car_grade_code").html('<option value=""></option>');
			$("#car_type_code").html('<option value=""></option>');
			return ;
		}
		$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/car',
				{
					'car_year':$( "#car_year option:selected").val(),
					'car_maker_code':$( "#form_car_maker_code option:selected").val(),
					'car_model_code':$( "#car_model_code option:selected").val(),
					'level':'3'
				},
				function(data){

					$("#car_type_code").html(data);
					$("#car_grade_code").html('<option value=""></option>');
				}
			);
	});
	$( "#car_type_code" ).change(function() {
		if($( "#car_type_code option:selected").val() =='')
		{
			$("#car_grade_code").html('<option value=""></option>');
			return;
		}
		$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/car',
				{
					'car_type_code':$( "#car_type_code option:selected").val(),
					'car_year':$( "#car_year option:selected").val(),
					'car_maker_code':$( "#form_car_maker_code option:selected").val(),
					'car_model_code':$( "#car_model_code option:selected").val(),
					'level':'4'
				},
				function(data){

					//alert(data);
					$("#car_grade_code").html(data);

				}
			);
	});
</script>