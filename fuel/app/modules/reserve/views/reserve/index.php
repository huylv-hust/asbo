<style type="text/css">
	label.error{
		color:red;
	}
	.input-group > label{
		display: table-cell;
		padding: 0 5px;
		vertical-align: middle;
	}
	.col-time > label{
		margin-left: 5px;
	}
	div.repair-image {
		margin-top: 10px;
		margin-bottom: 10px;
	}

	button.remove-btn {
		position: relative;
		left: -20px;
		top: -10px;
		padding: 8px;
		vertical-align: top;
	}
</style>
<?php if(isset($reservation_delete)){ ?>
<script type="text/javascript">
alert('<?php echo $reservation_delete ?>');
var pos = '<?php echo Fuel\Core\Input::get('pos') ?>';
if(pos =='1')
	window.location.href = '<?php echo \Uri::base().'reserve/list'?>';
else
	window.location.href = '<?php echo \Uri::base().'reserve/calendar'?>';
</script>
<?php return ; } ?>
<div class="container">
			<h3>
				作業予約<?php echo \Input::param('reservation_no') ? '詳細' : '登録' ?>
				<?php if(isset($error)) echo $error?>
				<?php
					if(isset($reservation_delete)) echo $reservation_delete
				?>
			</h3>

	<form class="form-inline" method="post" id="validation">
				<p class="text-right">
					<button type="button" class="btn btn-warning btn-sm">
						<i class="glyphicon glyphicon-step-backward icon-white"></i>
						戻る
					</button>
					<?php if(\Input::param('reservation_no') && $check_edit != 1){ ?>
					<button class="btn btn-danger btn-sm" type="button" id="delete" onclick="delete_reserve('<?php echo \Input::param('reservation_no')?>','<?php echo (int)\Input::param('pos')?>')">
						<i class="glyphicon glyphicon-trash icon-white"></i>
						削除
					</button>
					<?php } ?>
				</p>
				<table class="table table-striped">
					<tbody>
					<?php if ($reservation_no) { ?>
					<tr>
						<th class="text-right">予約番号</th>
						<td>
							<?php echo $reservation_no ?><span class="text-success"> (<?php if($save_from == 'ss') echo '管理側登録'; elseif($save_from == 'usappy') echo 'ユーザー登録'; else echo ' 登録経路不明'; ?>)</span>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<th class="text-right">SSコード</th>
						<td>
							<input type="text" size="6" maxlength="6" class="form-control" name="sscode" id="sscode" value="<?php if(isset($sscode)) echo $sscode?>" readonly="readonly">
							<span class="text-inverse" id="sslabel"></span>
							<button name="findss-btn" class="btn btn-success btn-sm" type="button">
								<i class="glyphicon glyphicon-search icon-white"></i>
							</button>
							<span class="text-info">※必須</span>
						</td>
					</tr>
					<tr>
						<th class="text-right">メニュー</th>
						<td>
							<?php if(isset($pit_work))echo  htmlspecialchars_decode($pit_work)?>
							<input type="text" placeholder="「その他」場合必須" size="30" class="form-control" name="menu_name" value="<?php if(isset($menu_name)) echo $menu_name?>">
						</td>
					</tr>
					<tr>
						<th class="text-right">作業ピット</th>
						<td>
							<select class="form-control" name="pit_no" id="pit_no">
								<?php if(isset($pit_no)) echo htmlspecialchars_decode($pit_no);?>
							</select>
							<span class="text-info">※タイヤ交換・オイル交換・車検の場合必須</span>
						</td>
					</tr>
					<tr>
						<th class="text-right">入庫予定時刻</th>
						<td>
							<?php
								$arrival_time_date='';
								$arrival_time_hh='';
								$arrival_time_mm='';
								if(strtotime($arrival_time))
								{
									$arrival_time = explode(' ',$arrival_time);
									$arrival_time_date = $arrival_time['0'];
									if(isset($arrival_time['1']))
									{
										$arrival_time_t  = explode(':',$arrival_time['1']);
										$arrival_time_hh = $arrival_time_t['0'];
										$arrival_time_mm = $arrival_time_t['1'];
									}
								}
							?>
							<?php
								for($i=0;$i<=23;++$i){
									if($i < 10){
										$i = '0'.$i;
									}
									$listHours[$i] = $i;
								}
								$listMinute = array('00' => '00', '30' => '30');
							?>
							<input type="text" size="12&quot;" class="form-control dateform" id="dp1432003172115" name="arrival_time" value="<?php echo $arrival_time_date?>">
							<?php echo Form::select('arrival_time_hh', Input::post('arrival_time_hh', isset($post) ? $post->arrival_time_hh : $arrival_time_hh), $listHours, array('class'=>'form-control')); ?>
							:
							<?php echo Form::select('arrival_time_mm', Input::post('arrival_time_mm', isset($post) ? $post->arrival_time_mm : $arrival_time_mm), $listMinute, array('class'=>'form-control')); ?>
							<span class="text-info">※車検の場合必須</span>
						</td>
					</tr>
					<tr>
						<th class="text-right">作業予定期間</th>
							<td>
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
								<span class="from_date_1">
							<input type="text" class="form-control from_date" name="from_date" size="12" value="<?php echo $start_d  ?>">
								</span>
							<?php echo Form::select('from_date_hh', Input::post('from_date_hh', isset($post) ? $post->from_date_hh : $start_hh), $listHours, array('class'=>'form-control from_date_hh')); ?>
							:
							<?php echo Form::select('from_date_mm', Input::post('from_date_mm', isset($post) ? $post->from_date_mm : $start_mm), $listMinute, array('class'=>'form-control from_date_mm')); ?>
							～
							<span class="to_date_1">
							<input type="text" class="form-control to_date dateform" name="to_date" size="12" value="<?php echo $end_d?>">
							</span>
							<?php echo Form::select('to_date_hh', Input::post('to_date_hh', isset($post) ? $post->to_date_hh : $end_hh), $listHours, array('class'=>'form-control to_date_hh')); ?>
							:
							<?php echo Form::select('to_date_mm', Input::post('to_date_mm', isset($post) ? $post->to_date_mm : $end_mm), $listMinute, array('class'=>'form-control to_date_mm')); ?>

						</td>


					</tr>
					<tr>
						<th class="text-right">Usappy会員ID</th>
						<td>
							<input type="text" value="<?php if(isset($member_id)) echo $member_id?>" maxlength="12" readonly="readonly" size="20" class="form-control" name="usappy_id">
							<span class="text-info">※手入力不可</span>
							<button name="findcard-btn" class="btn btn-success btn-sm ds" type="button">
								<i class="glyphicon glyphicon-search icon-white"></i>
								Usappyカード番号から呼出
							</button>
							<button class="btn btn-danger btn-sm ds" type="button" id="clear_usappy_id" name="clear_usappy_id">
								会員IDクリア
							</button>
						</td>
					</tr>
					<!-- Usppy -->
					<tr>
						<th class="text-right">掛カード番号</th>
						<td>
							<input type="text" class="form-control" onchange="Util.zen2han(this)" size="20" value="<?php if(isset($cs_card_number)) echo $cs_card_number ?>" name="cs_card_number">
						</td>
					</tr>
					<tr>
						<th class="text-right">お客様氏名</th>
						<td>
							<input type="text" class="form-control" size="20" value="<?php if(isset($cs_name)) echo $cs_name ?>" onchange="Util.han2zen(this)" name="cs_name">
							<span class="text-info">※必須</span>

						</td>
					</tr>
					<tr>
						<th class="text-right">お客様氏名(カナ)</th>
						<td>
							<input type="text" class="form-control" onchange="Util.convertKanaToOneByte(this)" size="20" name="cs_name_kana" value="<?php if(isset($cs_name_kana)) echo $cs_name_kana ?>">
							<span class="text-info">※必須</span>
						</td>
					</tr>
					<tr>
						<th class="text-right">お客様電話番号(携帯)</th>
						<td>
							<input type="text" onchange="Util.zen2han(this)" class="form-control" size="20" name="cs_mobile_tel" value="<?php if(isset($cs_mobile_tel)) echo $cs_mobile_tel ?>">
							<span class="text-info">※携帯・自宅のどちらか必須</span>
						</td>
					</tr>
					<tr>
						<th class="text-right">お客様電話番号(自宅)</th>
						<td>
							<input type="text" onchange="Util.zen2han(this)" class="form-control" size="20" name="cs_house_tel" value="<?php if(isset($cs_house_tel)) echo $cs_house_tel ?>">
							<span class="text-info">※携帯・自宅のどちらか必須</span>
						</td>
					</tr>
					<!-- end Usppy -->
					<tr>
						<th class="text-right">車番</th>
					<td>
						<input type="text" class="form-control" onchange="Util.zen2han(this),Util.convertPlateNo(this)" size="4" name="plate_no" value="<?php if(isset($plate_no)) echo $plate_no?>"/>
						<span class="text-info">※必須</span>
					</td>
					</tr>
					<?php echo $carselect;?>
					<tr>
						<th class="text-right">車の色</th>
						<td>
							<?php echo  htmlspecialchars_decode($car_color)?>
						</td>
					</tr>
					<tr>
						<th class="text-right">コーティングの種類</th>
						<td>
							<?php echo htmlspecialchars_decode($coating_code)?>
						</td>
					</tr>
					<tr>
						<th class="text-right">車両サイズ</th>
						<td>
							<?php echo htmlspecialchars_decode($car_size)?>
						</td>
					</tr>
					<tr>
						<th class="text-right">車両重量</th>
						<td>
							<?php echo htmlspecialchars_decode($car_weight)?>
						</td>
					</tr>
					<tr>
						<th class="text-right">車検満了日</th>
						<td>
							<input type="text" size="12" class="form-control dateform" id="dp1432003172118" name="inspection_date" value="<?php  if(strtotime($inspection_date)) echo $inspection_date?>">
						</td>
					</tr>
					<tr>
						<th class="text-right">代車の希望</th>
						<td>
							<input type="hidden" name="savejson" value="0" />
							<?php echo htmlspecialchars_decode($is_car_request) ?>
						</td>
					</tr>
					<tr>
						<th class="text-right">送迎の有無</th>
						<td>
							<?php echo  htmlspecialchars_decode($is_shuttle_request)?>
						</td>
					</tr>
					<tr>
						<th class="text-right">タイヤの有無</th>
						<td>
							<?php echo htmlspecialchars_decode($tire_preparation_code) ?>
						</td>
					</tr>
					<tr>
						<th class="text-right" id="wheel-title">ホイールの準備</th>
						<td>
							<?php echo htmlspecialchars_decode($wheel_preparation_code) ?>
						</td>
					</tr>
					<tr>
						<th class="text-right">タイヤのサイズ</th>
						<td>
							<?php echo htmlspecialchars_decode($tire_size_code)?>
						</td>
					</tr>


					<tr>
						<th class="text-right">ご要望など</th>
						<td>
							<textarea rows="5" cols="80" class="form-control" name="other_request"><?php echo $other_request;?></textarea>						</textarea>


									</td>
					</tr>
<?php if(!\Input::param('reservation_no')){ ?>
					<tr>
						<th class="text-right">プライバシーポリシー</th>
						<td>
							<label class="checkbox-inline"><input type="checkbox" value="1" name="policy">同意する</label>
							(<a target="_blank" href="../privacy.pdf">プライバシーポリシーを表示</a>)
						</td>
					</tr>
<?php } ?>

				</tbody></table>
				<?php if($check_edit != 1 ){?>
				<div class="text-center">
					<button class="btn btn-primary btn-sm" type="submit">
						<i class="glyphicon glyphicon-pencil icon-white"></i>
						保存
					</button>
				</div>
				<?php }?>


			</form>
		</div>
<?php echo $ssfinder; ?>
<!-- Search usappy -->
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
                                <input type="text" class="form-control" placeholder="" size="16" name="card_no">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">生年月日(YYYYMMDD)</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="" size="8" name="birthday">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary btn-sm" id="get_card_info">
                                    <i class="glyphicon glyphicon-pencil icon-white"></i>
                                    呼び出し
                                </button>
								<span id ="report"></span>
								<span id ="error"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<div id="loading" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel">
					処理中です
				</h4>
			</div>
			<div class="modal-body">
				<p>
					<img src="<?php echo \Fuel\Core\Uri::base(); ?>/assets/img/snake.gif">
					しばらくお待ちください
				</p>
			</div>
		</div>
	</div>
</div>
<!-- end -->
<script>
	var check_edit = <?php echo $check_edit;?> ;
	if(check_edit == 1){
		$('#validation').find('input, textarea, select,button.ds').attr('disabled','disabled');
	}

	var setSsName = function(target)
	{
		$('#sslabel').text('');

		var sscode = target.val();
		if (sscode.length != 6)
		{
			return false;
		}

		$.getJSON(
			'<?php echo \Fuel\Core\Uri::base(); ?>/ajax/common/ss',
			{ sscode : target.val() }
		).done(function(response)
		{
			if (response === null)
			{
				return false;
			}
			$('#sslabel').text(response.ss_name);
		});
	};

	setSsName($('input[name=sscode]'));

	$('.dateform').datepicker();
//from_date
	$('.from_date').datepicker( {
		onSelect: function(date) {
		    if($("input[name=to_date]").val() == ''){
				$("input[name=to_date]").val(date);
			}
		},

	});
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
	$('button[name=clear_usappy_id]').on('click', function () {
		$('input[name=usappy_id]').val('');
		$('input[name=cs_card_number]').val('');
		$('input[name=cs_name_kana]').val('');
		$('input[name=cs_name]').val('');
		$('input[name=usappy_id]').val('');
		$('input[name=cs_mobile_tel]').val('');
		$('input[name=cs_house_tel]').val('');
		return false;
	});
	$( "#form_car_size_code" ).change(function() {

		if($( "#form_car_size_code option:selected").val()=='0' || $( "#form_car_size_code option:selected").val()=='')
		{
			$("#form_car_weight_code").html('<option value="0"></option>');
			return;
		}
		$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/weight',
				{
					'weight':$( "#form_car_size_code option:selected").val()
				},
				function(data){

					$("#form_car_weight_code").html(data);
				}
			);
	});

	$("#form_pit_work").change(function() {

		<?php if (\Input::param('reservation_no') == null) { ?>
		if ($('select[name=pit_work]').val() == 'inspection')
		{
			$('input[name=sscode]').prop('readonly', false);
		}
		else
		{
			setSsName(
				$('input[name=sscode]').prop('readonly', true).val('<?php echo Cookie::get('sscode') ?>')
			);
		}
		<?php } ?>

	}).trigger('change');

	<?php if (\Input::param('reservation_no') && Cookie::get('sscode') != $sscode) { ?>
	$('select[name=pit_work] option').each(function()
	{
		if ($(this).prop('selected') === false)
		{
			$(this).prop('disabled', true);
		}
	});
	<?php } ?>

	function delete_reserve(id,pos)
	{
		if(!confirm("削除します、よろしいですか？"))
			return false;
		$.post('<?php echo Fuel\Core\Uri::base()?>reserve/list/delete/',
				{
					'reservation_no':id
				},
				function(data){
					if(data=='1')
					{
						if(pos == '-1')
						{
							window.location.href='<?php  echo Fuel\Core\Uri::base()?>';
						}
						else if(pos=='0')
						{
							window.location.href='<?php  echo Fuel\Core\Uri::base()?>reserve/calendar/';
						}
						else
						{
							window.location.href='<?php if(Session::get('url_redirect')) echo Session::get('url_redirect').'&';else echo Fuel\Core\Uri::base()?>reserve/list/';
						}
					}
					else
					{
						alert('エラー');
					}

				}
			);
	}
	$( "#sscode" ).change(function() {
		var check_sscode = false;
		if($( "#sscode").val()=='' || $("#sscode").val().length !='6'){
			$("#pit_no").html('');
			return false;
		}
		$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/ss_search',
				{
					'sscode':$( "#sscode").val(),
				},
				function(data){
					var check_sscode = false;
					if(data =='1')
					{
						check_sscode = true;
					}
					getlistpit(check_sscode);

				}
		);

		setSsName($(this));
	});
	function getlistpit(check_sscode){
		if(check_sscode ===false)
		{
			alert("正しくありません");
			$("#sscode").val('');
			$("#pit_no").html('');
			return false;
		}
		$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/get_pit/',
				{
					'sscode':$( "#sscode").val(),
				},
				function(data){
					$("#pit_no").html(data);

				}
			);


	}
	$('#get_card_info').on('click', function () {


				if($('input[name=card_no]').val()=='')
				{

					alert('カード番号が正しくありません');
					$('#findcardform').modal('hide');
					return;
				}
				if($('input[name=birthday]').val()=='')
				{
					alert('生年月日が正しくありません');
					$('#findcardform').modal('hide');
					return;
				}
				else
				{
					try {
						var check_format_date = $.datepicker.parseDate('yymmdd', $('input[name=birthday]').val());

					} catch (e)
					{
						alert('生年月日が正しくありません');
						$('#findcardform').modal('hide');
						return;
					}

				}
				$('#findcardform').modal('hide');
				$('#loading').modal();
				$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/getcardinfo/',
				{	'card_no':$('input[name=card_no]').val(),
					'birthday':$('input[name=birthday]').val()
				},
				function(data){
					var rs = jQuery.parseJSON(data);
					$('#loading').modal('hide');
					if(rs['error']=='0')
					{
						//$('input[name=cs_card_number]').val(rs['card_no']);
						$('input[name=cs_name_kana]').val(rs['member_kaiinKana']);
						$('input[name=cs_name]').val(rs['member_kaiinName']);
						$('input[name=usappy_id]').val(rs['member_kaiinCd']);
						$('input[name=cs_mobile_tel]').val(rs['member_telNo1']);
						$('input[name=cs_house_tel]').val(rs['member_telNo2']);
						$('#findcardform').modal('hide');
						$('#loading').modal('hide');

					}
					else
					{
						$('#loading').modal('hide');
						alert(rs['error']);
					}

				}
			);
	});
	$('.btn-warning').click(function(){
		var returnUrl = '<?php echo Cookie::get('reserve_return_url') ?>';
		var pos = '<?php echo (int)\Fuel\Core\Input::get('pos')?>';

		if(pos == '-1')
		{
			window.location.href = '<?php echo Fuel\Core\Uri::base()?>';

		}
		else if(returnUrl != '')
		{
			window.location.href = returnUrl;
		}
		else
		{
			window.location.href = '<?php echo \Uri::base(); ?>reserve/list';
		}
	});


	$('select[name=tire_preparation_code]').on('change', function()
	{
		if ($(this).val() === '1') {
			$('#wheel-title').text('ご購入予定の内容');
			$('select[name=wheel_preparation_code] option[value=1]').text('タイヤのみ購入');
			$('select[name=wheel_preparation_code] option[value=2]').text('タイヤとホイールセット');
		} else if ($(this).val() === '2') {
			$('#wheel-title').text('ホイールの準備');
			$('select[name=wheel_preparation_code] option[value=1]').text('有り');
			$('select[name=wheel_preparation_code] option[value=2]').text('無し');
		}
	}).trigger('change');

	$.fn.autoKana('input[name=cs_name]', 'input[name=cs_name_kana]', { katakana : true });
	$('input[name=cs_name]').on('blur', function(){ $('input[name=cs_name_kana]').trigger('change'); });
</script>
<?php echo Asset::js('jquery.validate.js'); ?>
<?php echo Asset::js('validate/reserve-reserve.js'); ?>