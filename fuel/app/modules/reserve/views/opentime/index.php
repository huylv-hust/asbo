<style type="text/css">
	.show_err,.error{color: #F00}
</style>
<script type="text/javascript">
	baseUrl = "<?php echo Uri::base(); ?>";
</script>
<div class="container">
	<h3>受付時間枠切替</h3>
	<?php
	if(!isset($opentimer_info)){
		$opentimer_info = array('open_timer_id' => null,'sscode' => null,'menu_code' => null,'is_holiday' => null,'start_date'=>null,'end_date'=>null);
	}
	$minute_arr = array(0 => '00', 30 => '30');
	if(isset($_GET['menu_code']))
	{
		$opentimer_info['menu_code'] = $_GET['menu_code'];
	}
	if(isset($_GET['is_holiday']))
	{
		$opentimer_info['is_holiday'] = $_GET['is_holiday'];
	}
	?>
	<?php echo Form::open(array('method' => 'post', 'class' => 'form-inline', 'id'=>'form-open')); ?>
		<p class="text-right">
			<?php if($open_timer_id){ ?>
			<button type="button" id="delete" class="btn btn-danger btn-sm" data-id="<?php echo $open_timer_id; ?>">
				<i class="glyphicon glyphicon-trash icon-white"></i>
				削除
			</button>
			<?php } ?>
			<input type="hidden" name="open_timer_id" value="<?php echo $open_timer_id; ?>" />
		</p>
		<?php $menu_code = array('inspection'=>'車検', 'coating'=>'コーティング', 'wash'=>'洗車', 'oil'=>'オイル', 'tire'=>'タイヤ'); ?>
		<?php $is_holiday = array('0'=>'平日', '1'=>'土日祝祭日'); ?>
		<table class="table table-striped">
			<tbody><tr>
					<th class="text-right">メニュー</th>
					<td>
						<?php echo Form::select('menu_code', Input::post('menu_code', isset($post) ? $post->menu_code : $opentimer_info['menu_code']), $menu_code, array('class'=>'form-control', 'disabled'=>'disabled'));?>
						<?php echo Form::select('menu_code', Input::post('menu_code', isset($post) ? $post->menu_code : $opentimer_info['menu_code']), $menu_code, array('class'=>'form-control', 'style'=>'display:none'));?>
					</td>
				</tr>
				<tr>
					<th class="text-right">平日区分</th>
					<td>
						<?php echo Form::select('is_holiday', Input::post('is_holiday', isset($post) ? $post->is_holiday : $opentimer_info['is_holiday']), $is_holiday, array('class'=>'form-control'));?>
					</td>
				</tr>
				<tr>
					<th class="text-right">切替期間</th>
					<td>
						<?php echo Form::input('start_date', Input::post('start_date', isset($post) ? $post->start_date : $opentimer_info['start_date']), array('class'=>'form-control dateform', 'size'=>'12')); ?>
						～
						<?php echo Form::input('end_date', Input::post('end_date', isset($post) ? $post->end_date : $opentimer_info['end_date']), array('class'=>'form-control dateform', 'size'=>'12')); ?>
						<span class="text-info error time-overlap"></span>
						<span class="text-info">※開始日は必須、終了日を指定しない場合永久に有効</span>
					</td>
				</tr>
				<tr>
					<th class="text-right">
						受付時間枠
						<button type="button" class="btn btn-success btn-sm append-multi">
							<i class="glyphicon glyphicon-plus icon-white"></i>
						</button>
					</th>
					<td class="is_holiday_app">
						<div class="append time-ranges" style="float: left">
						<?php if(isset($open_timer_detail) && $open_timer_detail != null){ ?>
						<?php foreach ($open_timer_detail as $items){ ?>
						<?php
							$hour_start = Utility::string_to_time($items['start_time']);
							$minute_start = Utility::string_to_time($items['start_time'], true);
							$hour_end = Utility::string_to_time($items['end_time']);
							$minute_end = Utility::string_to_time($items['end_time'], true);
						?>
						<p>
							<select class="form-control" name="hoursstart[]">
								<?php for($i=0;$i<=23;++$i){ ?>
								<?php $j = $i; if($i < 10) { $j = '0'.$i; } ?>
								<option value="<?php echo $i; ?>" <?php if($hour_start == $i){ echo "selected = 'selected'";} ?>><?php echo $j; ?></option>
								<?php } ?>
							</select>
							:
							<select class="form-control" name="minutestart[]">
								<?php foreach($minute_arr as $key => $value){ ?>
								<option value="<?php echo $key; ?>" <?php if($minute_start == $key){ echo "selected = 'selected'";} ?>><?php echo $value; ?></option>
								<?php } ?>
							</select>
							～
							<select class="form-control" name="hoursend[]">
								<?php for($i=0;$i<=23;++$i){ ?>
								<?php $j = $i; if($i < 10) { $j = '0'.$i; } ?>
								<option value="<?php echo $i; ?>" <?php if($hour_end == $i){ echo "selected = 'selected'";} ?>><?php echo $j; ?></option>
								<?php } ?>
							</select>
							:
							<select class="form-control" name="minutesend[]">
								<?php foreach($minute_arr as $key => $value){ ?>
								<option value="<?php echo $key; ?>" <?php if($minute_end == $key){ echo "selected = 'selected'";} ?>><?php echo $value; ?></option>
								<?php } ?>
							</select>
							<button type="button" class="btn btn-danger btn-sm appended">
								<i class="glyphicon glyphicon-trash icon-white"></i>
							</button>
						</p>
						<?php } } ?>
						</div>
						<div class="show_err" style="float:left;padding:7px 0px 0px 7px;color:#F00"></div>
					</td>
				</tr>

			</tbody></table>

		<div class="text-center">
			<button type="button" class="btn btn-warning btn-sm" name="back-btn">
				<i class="glyphicon glyphicon-step-backward icon-white"></i>
				戻る
			</button>
			<button type="submit" class="btn btn-primary btn-sm">
				<i class="glyphicon glyphicon-pencil icon-white"></i>
				保存
			</button>
		</div>

	<?php echo Form::close(); ?>

	<div id="findcardform" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
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

</div>
<script type="text/javascript">
	$(function (e)
		{
			$('.dateform').datepicker();

			$('button[name=back-btn]').on('click', function()
			{
				history.back();
			});
		}
	);
</script>
<?php echo Asset::js('jquery.validate.js'); ?>
<?php echo Asset::js('validate/open-time.js'); ?>
