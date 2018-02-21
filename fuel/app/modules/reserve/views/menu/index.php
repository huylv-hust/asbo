<style type="text/css">
	.errors{color: red; padding:7px 0px 0px 5px}
	.floatleft{float: left}
	.minmax{min-width: 351px; min-height: 1px}
	.cls{clear:left}
</style>
<script type="text/javascript">
	baseUrl = "<?php echo Uri::base(); ?>reserve/menu";
	baseUrlMain = "<?php echo Uri::base(); ?>";
</script>
<?php echo Asset::js('validate/menu-validate.js'); ?>
<?php
//set array menu
$listMenu = array(1=>'oil','tire','inspection','wash','coating');
?>
<div class="container">
	<h3>作業メニュー設定</h3>
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		<?php foreach($listMenu as $key => $value){ ?>
		<div class="panel panel-success menubar-<?php echo $value; ?>">
			<div class="panel-heading" role="tab">
				<h4 class="panel-title">
					<a class="collapsed" data-toggle="collapse" href="#menu0<?php echo $key; ?>" aria-expanded="false">
						<?php echo Constants::$pit_work[$value]; ?>
					</a>
				</h4>
			</div>
			<div id="menu0<?php echo $key; ?>" class="panel-collapse collapse" role="tabpanel">
				<div class="panel-body">

					<form class="form-inline menu-<?php echo $value; ?>" method="post" id="menu-form<?php echo $key; ?>">
						<input type="hidden" name="menu_id" value="<?php echo $key; ?>" />
						<input type="hidden" name="menu_code" value="<?php echo $value; ?>" />
						<table class="table table-striped">
							<tr>
								<th class="text-right" style="white-space:nowrap">同時実施可能数</th>
								<td class="hideerr">
									<div style="float: left">
										<input type="text" class="form-control" name="max_parallel_count" size="5">
									</div>
									<?php if ($key === 1 or $key === 2 or $key === 3) { ?>
									<span class="text-info">※入力しない場合対応ピット数と同じになります</span>
									<?php } else { ?>
									<span class="text-info">※必須</span>
									<?php } ?>
									<div class="show-error errors max_parallel_count floatleft"></div>
								</td>
							</tr>
							<?php if($key === 5){ ?>
							<tr>
								<th class="text-right" style="white-space:nowrap">取扱コーティング</th>
								<td>
									<div class="floatleft">
										<label class="checkbox-inline">
											<input type="checkbox" name="coating_code[]" class="coating_code" value="crystal">クリスタルキーパー
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="coating_code[]" class="coating_code" value="diamond">ダイヤモンドキーパー
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="coating_code[]" class="coating_code" value="double">ダブルダイヤキーパー
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="coating_code[]" class="coating_code" value="pure">ピュアキーパー
										</label>
									</div>
									<div class="floatleft errors coating_code" style="padding:0px 0px 0px 10px"></div>
								</td>
							</tr>
							<?php } ?>
							<tr>
								<th class="text-right" style="white-space:nowrap">
									受付時間枠(平日)
									<button type="button" class="btn btn-success btn-sm append-multi" name="week" data-code="<?php echo $value; ?>">
										<i class="glyphicon glyphicon-plus icon-white"></i>
									</button>
									<div style="padding: 5px"></div>
									<p>
									<button type="button" class="btn btn-warning btn-sm" name="reserve-btn" data-type="0" data-code="<?php echo $value; ?>">
										受付時間枠切替
									</button>
									<button type="button" class="btn btn-danger btn-sm" name="delete-all-btn">
										<i class="glyphicon glyphicon-trash icon-white"></i>
										一括削除
									</button>
									</p>
									<div class="in_future0"></div>
									<p></p>
								</th>
								<td class="hideerr week time-ranges">
									<div class="floatleft week_div_apd minmax">
									</div>
									<div class="floatleft errors dayinweek"></div>
									<div class="cls"></div>
									<p></p>
									<div class="show_div_apd"></div>
								</td>
							</tr>
							<tr>
								<th class="text-right" style="white-space:nowrap">
									受付時間枠(土日祝祭日)
									<button type="button" class="btn btn-success btn-sm append-multi" name="holiday" data-code="<?php echo $value; ?>">
										<i class="glyphicon glyphicon-plus icon-white"></i>
									</button>
									<div style="padding: 5px"></div>
									<p>
										<button type="button" class="btn btn-warning btn-sm" name="reserve-btn" data-type="1" data-code="<?php echo $value; ?>">
											受付時間枠切替
										</button>
										<button type="button" class="btn btn-danger btn-sm" name="delete-all-btn">
											<i class="glyphicon glyphicon-trash icon-white"></i>
											一括削除
										</button>
									</p>
									<div class="in_future1"></div>
									<p></p>
								</th>
								<td class="hideerr holiday time-ranges">
									<div class="floatleft holiday_div_apd minmax">
									</div>
									<div class="floatleft errors holidays"></div>
									<div class="cls"></div>
									<p></p>
									<div class="show_div_apd"></div>
								</td>
							</tr>
							<tr>
								<th class="text-right" style="white-space:nowrap">
									予約受付中止日
									<button type="button" class="btn btn-success btn-sm append-one" data-code="<?php echo $value; ?>">
										<i class="glyphicon glyphicon-plus icon-white"></i>
									</button>
								</th>
								<td class="hideerr">
									<div class="holiday_divapd">
										<!--input type="text" class="form-control dateform first" name="is_holiday[]" size="12" value="<?php echo date('Y-m-d'); ?>">
										<button type="button" class="btn btn-danger btn-sm first appended-one" data-code="<?php echo $value; ?>">
											<i class="glyphicon glyphicon-trash icon-white"></i>
										</button-->
									</div>
									<div class="holiday_divapd_err errors"></div>
									<p class="text-info">※過去日のデータは表示されません</p>
								</td>
							</tr>
						</table>

						<button type="button" class="btn btn-primary btn-sm center-block single" data-id="<?php echo $key; ?>">
							<i class="glyphicon glyphicon-pencil icon-white"></i>
							保存
						</button>

					</form>

				</div>
			</div>
		</div>
		<?php } ?>
	</div>

</div>

<script type="text/javascript">
	$(function()
	{
		$('.dateform').datepicker();

		$('button[name=reserve-btn]').click(function(){
			var is_holiday = $(this).attr('data-type');
			var menu_code = $(this).attr('data-code');
			if(is_holiday != '' && menu_code != null){
				window.location.href = baseUrlMain+'reserve/opentime?menu_code='+menu_code+'&is_holiday='+is_holiday;
			}
		});

		$('button[name=delete-all-btn]').on('click', function()
		{
			$(this).parents('tr:first').find('div.week_div_apd > div, div.holiday_div_apd > div').remove();
		});
	});
</script>