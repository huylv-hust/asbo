<style type="text/css">
	.append-div-left{float: left}
	.show_error{float: left; padding: 5px 0px 0px 7px}
	.show_error label{color: red}
	label.error,p.error{color:red}
	.input-group > label{display: table-cell; padding: 0 5px; vertical-align: middle;}
</style>
<?php echo Asset::js('jquery.validate.js'); ?>
<script type="text/javascript">
	baseUrl = "<?php echo Uri::base(); ?>repair/staff";
</script>
<div class="container">
	<h3>
		リペア技術者登録
	</h3>
	<?php if(empty($info)) { $info = array('branch_code' => '','staff_name' => '','login_id' => '','password'=>'','piece_count'=>'','pice'=>'');} ?>
	<?php $branch_code = isset($_POST['branch_code']) ? $_POST['branch_code'] : $info['branch_code']; ?>
	<form method="post" class="form-inline" id="staffs-form">
	<?php //echo Form::open(array('method' => 'post', 'class' => 'form-inline', 'id' => 'staffs-form')); ?>
		<table class="table table-striped">
			<tr>
				<th class="text-right">所属支店</th>
				<td>
					<select class="form-control" name="branch_code">
						<option value=""></option>
						<?php foreach(Constants::$branch as $key => $val){ ?>
						<option value="<?php echo $key; ?>" <?php if($key == $branch_code){ echo "selected='selected'";} ?>><?php echo $val; ?></option>
						<?php } ?>
					</select>
					<span class="text-info">※必須</span>
				</td>
			</tr>
			<tr>
				<th class="text-right">氏名</th>
				<td>
					<input type="hidden" name="repair_staff_id" value="<?php echo \Input::param('repair_staff_id'); ?>" />
					<?php echo Form::input('staff_name', Input::post('staff_name', isset($post) ? $post->staff_name : $info['staff_name']), array('class' => 'form-control', 'size' => 50)); ?>
					<span class="text-info">※必須</span>
				</td>
			</tr>
			<tr>
				<th class="text-right">ログインID</th>
				<td>
					<div style="float:left">
					<?php echo Form::input('login_id', Input::post('login_id', isset($post) ? $post->login_id : $info['login_id']), array('class' => 'form-control', 'size' => 50, 'onchange' => 'Util.upercasetolow(this),Util.zen2han(this)')); ?>
					</div>
					<span class="text-info">※必須</span>
					<p class="error unique-err" style="float:left;padding:7px 0px 0px 5px;"><?php if(isset($errors)){ echo $errors; } ?></p>
				</td>
			</tr>
			<tr>
				<th class="text-right">パスワード</th>
				<td>
					<?php echo Form::input('password', Input::post('password', isset($post) ? $post->password : $info['password']), array('class' => 'form-control', 'size' => 50)); ?>
					<span class="text-info">※必須</span>
				</td>
			</tr>
			<tr>
				<th class="text-right">基本対応ピース数(1日あたり)</th>
				<td>
					<div class="input-group">
						<?php echo Form::input('piece_count', Input::post('piece_count', isset($post) ? $post->piece_count : $info['piece_count']), array('class' => 'form-control', 'size' => 5, 'onchange' => 'Util.zen2han(this)')); ?>
						<div class="input-group-addon">個</div>
					</div>
					<span class="text-info">※必須</span>
				</td>
			</tr>

			<tr>
				<th class="text-right">
					月別対応ピース数(1日あたり)
					<button type="button" class="btn btn-success btn-sm append">
						<i class="glyphicon glyphicon-plus icon-white"></i>
					</button>
				</th>
				<td class="append-div">
					<div class="clearfix">
						<div class="append-div-left">
						<?php if(isset($info['pice']) && ! empty($info['pice'])){ ?>
						<?php $i = 1; foreach($info['pice'] as $pice){ ?>
						<div class="div-element">
							<div class="input-group">
								<input type="text" class="form-control" name="pice_year[]" size="4" value="<?php echo $pice['year']; ?>" onchange="Util.zen2han(this)">
								<div class="input-group-addon">年</div>
							</div>
							<div class="input-group">
								<input type="text" class="form-control" name="pice_month[]" data-stt="<?php echo $i; ?>" size="2" value="<?php echo $pice['month']; ?>" onchange="Util.zen2han(this)">
								<div class="input-group-addon">月</div>
							</div>
							<div class="input-group">
								<input type="text" class="form-control" name="pice_counts[]" size="2" value="<?php echo $pice['piece_count']; ?>" onchange="Util.zen2han(this)">
								<div class="input-group-addon">個</div>
							</div>
							<button type="button" class="btn btn-danger btn-sm appended">
								<i class="glyphicon glyphicon-trash icon-white"></i>
							</button>
						</div>
						<?php $i++; } } ?>
						</div>
						<div class="show_error"><label></label></div>
					</div>
					<p class="text-info">※過去日のデータは表示されません</p>
				</td>
			</tr>
		</table>

		<button type="submit" class="btn btn-primary btn-sm center-block">
			<i class="glyphicon glyphicon-pencil icon-white"></i>
			登録
		</button>
	</form>
	<?php //echo Form::close(); ?>

</div>

<!-- ssfinder -->
<?php echo $ssfinder; ?>

<script>
	$(function (e)
	{
		$('.dateform').datepicker();

		$('button[name=findss-btn]').on('click', function () {
			$('#ssfinder').modal();
			return false;
		});

		$('#ssfinder div.list-group a').on('click', function () {
			$('#ssfinder').modal('hide');
			return false;
		});
	});
</script>

<?php echo Asset::js('validate/staffs.js'); ?>