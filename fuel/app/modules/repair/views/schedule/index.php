<style type="text/css">
	label.error{color:red}
</style>
<div class="container">

	<h3>
		技術者予定登録
	</h3>
	<?php if(!isset($err_message)){ ?>
	<?php echo Form::open(array('action' =>\Uri::base(true).'repair/schedule/save_data', 'method' => 'post','id'=>'validation','class' => 'form-inline'));?>

		<?php if(isset($repair_schedule_id)) { ?>
		<p class="text-right">
			<button type="button" class="btn btn-danger btn-sm" name="delete-schedule">
				<i class="glyphicon glyphicon-trash icon-white"></i>
				削除
			</button>
		</p>
		<?php } ?>
		<?php
			$start_d='';
			$start_mm='';
			$start_hh='';
			$end_d='';
			$end_mm='';
			$end_hh='';
			$date_s = explode(' ',$start_time);
			$start_d = $date_s['0'];
			if(isset($date_s['1']))
			{
				$date_s_h_m  = explode(':',$date_s['1']);
				$start_hh = $date_s_h_m['0'];
				$start_mm = $date_s_h_m['1'];
			}
			$date_e = explode(' ',$end_time);
			$end_d = $date_e['0'];
			if(isset($date_e['1']))
			{
				$date_e_h_m  = explode(':',$date_e['1']);
				$end_hh = $date_e_h_m['0'];
				$end_mm = $date_e_h_m['1'];
			}
		?>
		<table class="table table-striped">
			<tr>
				<th class="text-right">技術者</th>
				<td>
					<input type="text" class="form-control" size="50"  readonly="" id="staff" value="<?php echo $name  ?>">
					<input type="hidden" class="form-control" size="50" id="staff_id" name="staff_id" readonly="" id="staff" value="<?php if(isset($staff_id)) echo $staff_id  ?>">
					<input type="hidden" class="form-control" size="50" id="repair_schedule_id" name="repair_schedule_id" readonly="" id="staff" value="<?php if(isset($repair_schedule_id)) echo $repair_schedule_id  ?>">

					<button type="button" class="btn btn-success btn-sm" name="findstaff-btn">
						<i class="glyphicon glyphicon-search icon-white"></i>
					</button>
					<span class="text-info">※必須</span>
				</td>
			</tr>
			<tr>
				<th class="text-right">配置先SSコード</th>
				<td>
					<input type="text" class="form-control" size="6" id="sscode" name="sscode" value="<?php if(isset($sscode)) echo $sscode  ?>">
					<button type="button" class="btn btn-success btn-sm" name="findss-btn">
						<i class="glyphicon glyphicon-search icon-white"></i>
					</button>
					<span class="text-info">※必須</span>
				</td>
			</tr>
			<tr>
				<th class="text-right">配置期間</th>
				<td>
							<input type="text" class="form-control dateform from_date" name="from_date_re" size="12" value="<?php if(isset($start_d)) echo $start_d  ?>">
							<input type="text" onchange="Util.zen2han(this)" class="form-control from_date_hh" size="2" maxlength="2" name="from_date_hh_re" placeholder="HH"  value="<?php if(isset($start_hh)) echo $start_hh ?>">
							:
							<input type="text" onchange="Util.zen2han(this)" class="form-control from_date_mm" size="2" maxlength="2" name="from_date_mm_re"  placeholder="MM" value="<?php if(isset($start_mm)) echo $start_mm ?>">
							～
							<input type="text" class="form-control dateform to_date" name="to_date_re" size="12" value="<?php if(isset($end_d)) echo $end_d?>">
							<input type="text" onchange="Util.zen2han(this)" class="form-control to_date_hh" size="2" maxlength="2" name="to_date_hh_re" placeholder="HH" value="<?php if(isset($end_hh)) echo $end_hh ?>">
							:
							<input type="text" onchange="Util.zen2han(this)" class="form-control to_date_mm" size="2" maxlength="2" name="to_date_mm_re" placeholder="MM" value="<?php if(isset($end_mm)) echo $end_mm ?>">
					<span class="text-info">※必須</span>
				</td>
			</tr>
		</table>

		<div class="text-center">
			<button type="submit" class="btn btn-primary btn-sm save-schedule">
				<i class="glyphicon glyphicon-pencil icon-white"></i>
				保存
			</button>
		</div>

	</form>

	<div id="findcardform" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="gridSystemModalLabel">
						カード番号から情報を呼び出すためにはカード番号とお客様の生年月日を入力してください
					</h4>
				</div>
				<div class="modal-body">
					<form mehod="post" class="form-horizontal">
						<div class="form-group">
							<label class="col-md-4 control-label">カード番号</label>
							<div class="col-md-4">
								<input type="text" class="form-control" placeholder="" size="16">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label">生年月日(YYYYMMDD)</label>
							<div class="col-md-4">
								<input type="text" class="form-control" placeholder="" size="8">
							</div>
							<div class="col-md-4">
								<button type="submit" class="btn btn-primary btn-sm">
									<i class="glyphicon glyphicon-pencil icon-white"></i>
									呼び出し
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php } else { ?>
		<div class="alert alert-danger" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?php echo $err_message ?>
		</div>
	<?php } ?>
</div>

<?php echo $ssfinder; ?>
<?php echo $stafffinder; ?>

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

		$('button[name=findcard-btn]').on('click', function () {
			$('#findcardform').modal();
			return false;
		});

		$('button[name=findstaff-btn]').on('click', function () {
			$('#stafffinder').modal();
			return false;
		});
		<?php if(isset($repair_schedule_id)) { ?>
		$('button[name=delete-schedule]').on('click', function ()
			{
				if(confirm('削除します、よろしいですか？')){
				$.post("<?php echo Fuel\Core\Uri::base()?>repair/schedule/delete/",{'repair_schedule_id': '<?php echo $repair_schedule_id ?>'},
				function(data){
					window.location.href='<?php echo Fuel\Core\Uri::base().'repair/staffschedule/'?>'
				}
				);
		}
			}
		);
		<?php } ?>
		$( "#sscode" ).keyup(function() {
			$('.save-schedule').attr('disabled','disabled');
			var check_sscode = false;
			if($( "#sscode").val()=='' || $("#sscode").val().length !='6'){
				$("#pit_no").html('');
				return false;
			}
			$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/ss_search/',
					{
						'sscode':$( "#sscode").val(),
					},
					function(data){
						$('.save-schedule').removeAttr('disabled');
						var check_sscode = false;
						if(data =='1')
						{
							check_sscode = true;
						}
						getlistpit(check_sscode);

					}
			);

		});


	});
	function getlistpit(check_sscode){
		if(check_sscode ===false)
		{
			alert("正しくありません");
			$("#sscode").val('');
			$("#pit_no").html('');
			return false;
		}
		$.post('<?php echo Fuel\Core\Uri::base()?>reserve/reserve/get_pit/',
				{
					'sscode':$( "#sscode").val(),
				},
				function(data){
					$("#pit_no").html(data);

				}
			);


	}
</script>
<?php echo Asset::js('jquery.validate.js'); ?>
<?php echo Asset::js('validate/reserve-reserve.js'); ?>