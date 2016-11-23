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
	window.location.href = '<?php echo \Uri::base().'repair/list'?>';
else
	window.location.href = '<?php echo \Uri::base().'repair/calendar'?>';
</script>
<?php return ; } ?>
<div class="container">
			<h3>
				リペア予約登録 <?php if(isset($error)) echo $error ?>
				<?php
					if(isset($reservation_delete)) echo $reservation_delete
				?>
			</h3>
	<form class="form-inline" id="validation" method="POST">
				<p class="text-right">
					<button type="button" class="btn btn-warning btn-sm">
						<i class="glyphicon glyphicon-step-backward icon-white"></i>
						戻る
					</button>
					<?php if(\Input::param('reservation_no') && $check_edit != 1){ ?>
					<button class="btn btn-danger btn-sm" type="button" id="delete" onclick="delete_repair('<?php echo \Input::param('reservation_no')?>',<?php echo (int)\Input::param('pos') ?>)">
						<i class="glyphicon glyphicon-trash icon-white"></i>
						削除
					</button>
					<?php } ?>
				</p>
				<table class="table table-striped">
					<?php if ($reservation_no) { ?>
					<tr>
						<th class="text-right">予約番号</th>
						<td>
							<?php echo $reservation_no ?>
						</td>
					</tr>
					<tr>
						<th class="text-right">登録日時</th>
						<td>
							<?php echo $created_at ?>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<th class="text-right">施工SSコード</th>
						<td>
							<input type="text" class="form-control" size="6" maxlength="6" name="sscode" id="sscode" value="<?php echo $sscode ?>" readonly="readonly">
							<span class="text-inverse" id="sslabel"></span>
						</td>
					</tr>
					<tr>
						<th class="text-right">ピース数</th>
						<td>
							<div class="input-group" id="input-group-a">
								<div class="input-group-addon">A:キズ</div>
								<input id="a_piece_count" type="text" onchange="Util.zen2han(this)" class="form-control" size="5" name="a_piece_count" value="<?php echo  (\Input::param('reservation_no')) ? (int)$a_piece_count : '0';?>">
								<div class="input-group-addon">ピース</div>
							</div>
							<div class="input-group" id="input-group-b">
								<div class="input-group-addon">B:へこみ</div>
								<input id="b_piece_count" type="text" onchange="Util.zen2han(this)" class="form-control" size="5" name="b_piece_count" value="<?php echo  (\Input::param('reservation_no')) ? (int)$b_piece_count : '0'; ?>">

								<div class="input-group-addon">ピース</div>
							</div>
						</td>
					</tr>

					<tr>
						<th class="text-right">
							写真
							<button type="button" class="btn btn-info btn-sm ds" name="addimg-btn"><i class="glyphicon glyphicon-plus icon-white"></i> 追加</button>
						</th>
						<td id="image-block">
							<div class="text-info">※アップロードに時間がかかるため、予め解像度を落としてください。</div>
							<?php if (isset($image_keys)) { ?>
								<?php foreach ($image_keys as $image_key) { ?>
									<div class="repair-image pull-left">
										<a href="<?php echo Fuel\Core\Uri::base()?>ajax/image/<?php echo htmlspecialchars($image_key) ?>" target="_blank">
											<img src="<?php echo Fuel\Core\Uri::base()?>ajax/image/<?php echo htmlspecialchars($image_key) ?>?w=200">
										</a>
										<button type="button" class="btn btn-danger btn-sm remove-btn">
											<i class="glyphicon glyphicon-remove"></i>
										</button>
										<input type="hidden" name="image_keys[]" value="<?php echo htmlspecialchars($image_key) ?>">
									</div>
								<?php } ?>
							<?php } ?>
						</td>
					</tr>

					<tr>
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
						<th  class="text-right">入庫予定</th>
						<?php
							for($i=0;$i<=23;++$i){
								if($i < 10) { $i = '0'.$i; }
								$listHours[$i] = $i;
							}
							$listMinute = array('00' => '00', '30' => '30');
						?>
						<td>
							<span class="col-time"><input type="text" class="form-control dateform" size="12" name="arrival_time" value="<?php echo $arrival_time_date ?>" ></span>
							<span class="col-time"><?php echo Form::select('arrival_time_hh', Input::post('arrival_time_hh', isset($post) ? $post->arrival_time_hh : $arrival_time_hh), $listHours, array('class'=>'form-control')); ?></span>
							<span class="col-time">:</span>
							<span class="col-time"><?php echo Form::select('arrival_time_mm', Input::post('arrival_time_mm', isset($post) ? $post->arrival_time_mm : $arrival_time_mm), $listMinute, array('class'=>'form-control')); ?></span>

						</td>
					</tr>
					<tr>
						<?php
								$return_time_date='';
								$return_time_hh='';
								$return_time_mm='';
								if(strtotime($return_time))
								{
									$return_time = explode(' ',$return_time);
									$return_time_date = $return_time['0'];
									if(isset($return_time['1']))
									{
										$return_time_t  = explode(':',$return_time['1']);
										$return_time_hh = $return_time_t['0'];
										$return_time_mm = $return_time_t['1'];
									}
								}
								if(\Input::get('date')){
									$date_url = substr(\Input::get('date'), 0, 10);
								}
							?>
						<th class="text-right">納車予定</th>
						<td>
							<span class="col-time"><input type="text" class="form-control dateform" size="12" name="return_time" id="return_time" value="<?php echo isset($date_url) ? $date_url : $return_time_date ?>"></span>
							<span class="col-time"><?php echo Form::select('return_time_hh', Input::post('return_time_hh', isset($post) ? $post->return_time_hh : $return_time_hh), $listHours, array('class'=>'form-control')); ?></span>
							<span class="col-time">:</span>
							<span class="col-time"><?php echo Form::select('return_time_mm', Input::post('return_time_mm', isset($post) ? $post->return_time_mm : $return_time_mm), $listMinute, array('class'=>'form-control')); ?></span>
						</td>
					</tr>
					<tr>
						<th class="text-right">Usappy会員ID</th>
						<td>
							<input type="text" value="<?php if(isset($usappy_id)) echo $usappy_id?>" maxlength="12" readonly="readonly" size="20" class="form-control" name="usappy_id">
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
							<input type="text" class="form-control" onchange="Util.zen2han(this)" size="20" maxlength="16" value="<?php if(isset($cs_card_number)) echo $cs_card_number ?>" name="cs_card_number" />
						</td>
					</tr>
					<tr>
						<th class="text-right">お客様氏名</th>
						<td>
							<input type="text" class="form-control" onchange="Util.han2zen(this)" size="20" value="<?php if(isset($cs_name)) echo $cs_name ?>" name="cs_name" />
							<span class="text-info">※必須</span>
						</td>
					</tr>
					<tr>
						<th class="text-right">お客様氏名(カナ)</th>
						<td>
							<input type="text" class="form-control" onchange="Util.convertKanaToOneByte(this)" size="20" name="cs_name_kana" value="<?php if(isset($cs_name_kana)) echo $cs_name_kana ?>"/>
							<span class="text-info">※必須</span>
						</td>
					</tr>
					<tr>
						<th class="text-right">お客様電話番号(携帯)</th>
						<td>
							<input type="text" onchange="Util.zen2han(this)" class="form-control" size="20" name="cs_mobile_tel" value="<?php if(isset($cs_mobile_tel)) echo $cs_mobile_tel ?>" />
							<span class="text-info">※携帯・自宅のどちらか必須</span>
						</td>
					</tr>
					<tr>
						<th class="text-right">お客様電話番号(自宅)</th>
						<td>
							<input type="text" onchange="Util.zen2han(this)" class="form-control" size="20" name="cs_house_tel" value="<?php if(isset($cs_house_tel)) echo $cs_house_tel ?>" />
							<span class="text-info">※携帯・自宅のどちらか必須</span>
						</td>
					</tr>
					<!-- end Usppy -->
					<tr>
					  <th class="text-right">担当予定技術者</th>
					  <td>

					   <?php if(isset($branch)) echo htmlspecialchars_decode($branch);?>
					   <select class="form-control" name="repair_staff_id" id="repair_staff_id">
						<?php echo htmlspecialchars_decode($list_staff)?>
					   </select>
						<span class="text-info">※必須</span>
					  </td>
					 </tr>

						<tr>
						<th class="text-right">金額</th>
						<td>
						<div class="input-group">
							<input type="text" onchange="Util.zen2han(this)" class="form-control" size="5" name ="price" value="<?php echo $price ?>"/>
						<div class="input-group-addon">円</div>
						</div>
						<span class="text-info">※必須</span>
						</td>
						</tr>

					<tr>
						<th class="text-right">車番</th>
					<td>
						<input type="text" onchange="Util.zen2han(this),Util.convertPlateNo(this)" class="form-control" size="4" name="plate_no" value="<?php if(isset($plate_no)) echo $plate_no?>"/>
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
						<th class="text-right">カラーナンバー</th>
						<td>
							<input type="text" name="color_number" class="form-control" size="12" value="<?php if(isset($color_number)) echo $color_number ?>" onchange="Util.zen2han(this)" maxlength="10">
							<span class="text-info">※半角英数のみ、10桁まで</span>
						</td>
					</tr>
					<tr>
						<th class="text-right">代車の希望</th>
						<td>
							<input type="hidden" name="savejson" value="0" />
							<?php echo Form::select('is_car_request', Input::post('is_car_request', isset($post) ? $post->is_car_request : @$is_car_request), Constants::$is_car_request_oil_tire_wash, array('class'=>'form-control')); ?>
						</td>
					</tr>
					<tr>
						<th class="text-right">送迎の有無</th>
						<td>
							<?php echo  htmlspecialchars_decode($is_shuttle_request)?>
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
				</table>

				<?php if($check_edit != 1 ){?>
				<div class="text-center">
					<button class="btn btn-primary btn-sm" type="submit">
						<i class="glyphicon glyphicon-pencil icon-white"></i>
						保存
					</button>
				</div>
				<?php }?>
				<input type="file" name="imgfile" class="hide">

			</form>



		</div>

<div class="hide">
	<div class="repair-image pull-left">
		<a href="" target="_blank">
			<img src="" target="_blank">
		</a>
		<button type="button" class="btn btn-danger btn-sm remove-btn">
			<i class="glyphicon glyphicon-remove"></i>
		</button>
		<input type="hidden" name="image_keys[]" value="">
	</div>
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
		$('#validation').find('input, textarea, select ,button.ds').attr('disabled','disabled');
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
	function delete_repair(id,pos)
	{

		if(!confirm("削除します、よろしいですか？"))
			return false;
		$.post('<?php echo Fuel\Core\Uri::base()?>repair/list/delete/',
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
							window.location.href='<?php  echo Fuel\Core\Uri::base()?>repair/calendar/';
						}
						else
						{
							window.location.href='<?php if(Session::get('url_redirect_repair')) echo Session::get('url_redirect_repair').'&';else echo Fuel\Core\Uri::base()?>repair/list/';
						}

					}
					else
					{
						alert('エラー');
					}

				}
			);
	}
	$( "#form_branch" ).change(function() {
		if($( "#form_branch option:selected").val()=='-1')
		{
			$("#repair_staff_id").html('<option value=""></option>');
			return ;
		}
		$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/get_staff/',
				{
					'branch':$( "#form_branch option:selected").val()
				},
				function(data){

					$("#repair_staff_id").html(data);
				}
			);
	});

	$( "#sscode" ).change(function() {
		if($( "#sscode").val()=='' || $("#sscode").val().length !='6')
		{
			return false;
		}
		$.post('<?php echo Fuel\Core\Uri::base()?>ajax/common/ss_search/',
				{
					'sscode':$( "#sscode").val(),
				},
				function(data){
					if(data =='1')
					{
						check_sscode = true;
					}
					else
					{
						alert("正しくありません");
						$( "#sscode").val("");
					}
				}
		);

	});

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

	$('button[name=addimg-btn]').on('click', function(e)
	{
		$('input[name=imgfile]').click();
	});

	$('input[name=imgfile]').on('change', function(e)
	{
		var loader = $('<img class="loaderimg" src="<?php echo Fuel\Core\Uri::base()?>img/snake.gif">');
		$('#image-block').append(loader);

		var fd = new FormData();
		fd.append('imgfile', e.target.files[0]);
		$.ajax({
			type: 'POST',
			url: '<?php echo Fuel\Core\Uri::base()?>ajax/image/save',
			data: fd,
			cache: false,
			contentType: false,
			processData: false,
			success: function(response)
			{
				loader.remove();
				var json = JSON.parse(response);
				if (json.key != undefined) {
					var clone = $('div.hide div.repair-image:first').clone();
					clone.find('img')
						.attr('src', '<?php echo Fuel\Core\Uri::base()?>ajax/image/' + json.key + '?w=200');
					clone.find('a')
						.attr('href', '<?php echo Fuel\Core\Uri::base()?>ajax/image/' + json.key);
					clone.find('input[name^=image_keys]').val(json.key);
					$('#image-block').append(clone);
				} else {
					alert(json.error);
				}
			}
		});
	});

	$('div.container table').on('click', 'button.remove-btn', function()
	{
		$(this).parents('div.repair-image:first').remove();
	});
	$('.btn-warning').click(function(){
		var returnUrl = '<?php echo Cookie::get('repair_retun_url') ?>';
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
			window.location.href = '<?php echo \Uri::base(); ?>repair/list';
		}
	});
	$('input[name=arrival_time]').change(function(){
		var val = $(this).val();
		if($('input[name=return_time]').val() == ''){
			$('input[name=return_time]').val(val);
		}
	});

	$.fn.autoKana('input[name=cs_name]', 'input[name=cs_name_kana]', { katakana : true });
	$('input[name=cs_name]').on('blur', function(){ $('input[name=cs_name_kana]').trigger('change'); });

	<?php if (!$reservation_no) { ?>
		$( "#form_branch" ).trigger('change');
	<?php } ?>
</script>
<?php echo Asset::js('jquery.validate.js'); ?>
<?php echo Asset::js('validate/repair-reserve.js'); ?>
