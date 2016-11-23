<style type="text/css">
	label.error{color:red}
</style>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<?php echo Asset::js('jquery.validate.js'); ?>

<div class="container">
    <h3>
        代車予約登録
    </h3>

	<?php if(isset($error)) { ?>
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<?php echo htmlspecialchars($error) ?>
	</div>
	<?php } ?>

    <form class="form-inline booking-car" id="validation" method="post">
        <p class="text-right">
            <?php if($reservation_no) { ?>
			<button type="button" class="btn btn-danger btn-sm" name="delete-reservation">
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
			<?php } ?>
            <tr>
                <th class="text-right">指定代車</th>
                <td>
                    <?php echo $car_info['sscode'].' '.$car_info['ss_name'].' '.$car_info['car_name'] ?> (車番:<?php echo $car_info['plate_no']?>)
					<input name="car_info_sscode" type="hidden" value="<?php echo $car_info['sscode']?>">
				</td>
            </tr>
            <tr>
                <th class="text-right">利用SS</th>
                <td>
                    <?php echo $sscode.' '.$ss_name ?>
                </td>
            </tr>
            <tr>
                <th class="text-right">利用期間</th>
                <td>
					<?php
						$start_d='';
						$start_mm='';
						$start_hh='';
						$end_d='';
						$end_mm='';
						$end_hh='';
						if(\Input::param('reservation_no','')) //Edit
						{
							$date_s = explode(' ',$date_start);
							$start_d = $date_s['0'];
							$date_s_h_m  = explode(':',$date_s['1']);
							$start_hh = $date_s_h_m['0'];
							$start_mm = $date_s_h_m['1'];
							$date_e = explode(' ',$date_end);
							$end_d = $date_e['0'];
							$date_e_h_m  = explode(':',$date_e['1']);
							$end_hh = $date_e_h_m['0'];
							$end_mm = $date_e_h_m['1'];


						}
						else // Add new
						{
							if(isset($date_start))
							{
								$date_s = explode(' ',$date_start);
								$start_d = $date_s['0'];
								if(isset($date_s['1']))
								{
									$date_s_h_m  = explode(':',$date_s['1']);
									$start_hh = $date_s_h_m['0'];
									$start_mm = $date_s_h_m['1'];
								}
							}
							if(isset($date_end))
							{
								$date_e = explode(' ',$date_end);
								$end_d = $date_e['0'];
								if(isset($date_e['1']))
								{
									$date_e_h_m  = explode(':',$date_e['1']);
									$end_hh = $date_e_h_m['0'];
									$end_mm = $date_e_h_m['1'];
								}
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
                    <input type="text" class="form-control dateform" name="from_date" size="12" value="<?php if(isset($error)) echo Fuel\Core\Input::post('from_date');  else echo $start_d ; ?>">
					<?php echo Form::select('from_date_hh', Input::post('from_date_hh', isset($post) ? $post->from_date_hh : $start_hh), $listHours, array('class'=>'form-control')); ?>
                    :
					<?php echo Form::select('from_date_mm', Input::post('from_date_mm', isset($post) ? $post->from_date_mm : $start_mm), $listMinute, array('class'=>'form-control')); ?>
                    ～
                    <input type="text" class="form-control dateform" name="to_date" size="12" value="<?php if(isset($error)) echo Fuel\Core\Input::post('to_date');else  echo $end_d?>">
					<?php echo Form::select('to_date_hh', Input::post('to_date_hh', isset($post) ? $post->to_date_hh : $end_hh), $listHours, array('class'=>'form-control')); ?>
                    :
					<?php echo Form::select('to_date_mm', Input::post('to_date_mm', isset($post) ? $post->to_date_mm : $end_mm), $listMinute, array('class'=>'form-control')); ?>
                    <span class="text-info"></span>
                </td>
            </tr>
            <tr>
                <th class="text-right">Usappy会員ID</th>
                <td>
                    <input type="text" class="form-control" size="20" readonly="readonly" maxlength="12" value="<?php if(count($cs_info)) echo $cs_info['usappy_id']; else echo Fuel\Core\Input::post('usappy_id') ?>" name="usappy_id">
                    <span class="text-info">※手入力不可</span>
                    <button type="button" class="btn btn-success btn-sm" name="findcard-btn">
                        <i class="glyphicon glyphicon-search icon-white"></i>
                        Usappyカード番号から呼出
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" name="clear_usappy_id">
                        会員IDクリア
                    </button>
                </td>
            </tr>
            <tr>
                <th class="text-right">掛カード番号</th>
                <td>
                    <input type="text" class="form-control" onchange="Util.zen2han(this)" size="20" maxlength="16" value="<?php if(count($cs_info)) echo $cs_info['cs_card_number'] ; else echo Fuel\Core\Input::post('cs_card_number') ?>" name="cs_card_number">
                </td>
            </tr>
            <tr>
                <th class="text-right">お客様氏名</th>
                <td>
                    <input type="text" class="form-control" size="20" value="<?php if(count($cs_info)) echo $cs_info['cs_name']; else echo Fuel\Core\Input::post('cs_name'); ?>" name="cs_name">
					<span class="text-info">※必須</span>
                </td>
            </tr>
            <tr>
                <th class="text-right">お客様氏名(カナ)</th>
                <td>
                    <input type="text" class="form-control" onchange="Util.convertKanaToOneByte(this)" size="20" name="cs_name_kana" value="<?php if(count($cs_info)) echo $cs_info['cs_name_kana']; else echo Fuel\Core\Input::post('cs_name_kana') ?>">
					<span class="text-info">※必須</span>
                </td>
            </tr>
            <tr>
                <th class="text-right">お客様電話番号(携帯)</th>
                <td>

                    <input type="text" onchange="Util.zen2han(this)" class="form-control" size="20" name="cs_mobile_tel" value="<?php if(count($cs_info)) echo $cs_info['cs_mobile_tel']; else echo Fuel\Core\Input::post('cs_mobile_tel') ?>">
					<span class="text-info">※携帯・自宅のどちらか必須</span>

                </td>
            </tr>
            <tr>
                <th class="text-right">お客様電話番号(自宅)</th>
                <td>

                   <input type="text" onchange="Util.zen2han(this)" class="form-control" size="20" name="cs_house_tel" value="<?php if(count($cs_info)) echo $cs_info['cs_house_tel']; else echo Fuel\Core\Input::post('cs_house_tel') ?>">
				  <span class="text-info">※携帯・自宅のどちらか必須</span>

                </td>
            </tr>
			<tr>
				<th class="text-right">用途</th>
				<td>
					<?php $purpo_code = isset($purpose_code) ? $purpose_code : ''; ?>
					<?php echo Form::select('purpose_code', Input::post('purpose_code', isset($post) ? $post->purpose_code : $purpo_code), \Constants::$purpose_list, array('class'=>'form-control')); ?>
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
		<input type ="hidden" name="type" value="0" id="type" val-data="0">

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-sm submit-car">
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
</div>
<div id="dialog" title="代車予約登録">
	<input type="button" value="作業予約へ行く" onclick="submitForm(1);">
	<input type="button" value="リペア予約へ行く" onclick="submitForm(2);">
	<input type="button" value="代車予約状況へ戻る" onclick="submitForm(0);">
</div>
<?php echo $ssfinder; ?>

<script>
$(function (e)
{
	$( "#dialog" ).dialog({
      autoOpen: false,
	  minWidth: 500
    });
	$('.dateform').datepicker( {
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
	<?php if($reservation_no) { ?>
	$('button[name=delete-reservation]').on('click', function ()
		{
			if(confirm('削除します、よろしいですか？')){
			$.post("<?php echo Fuel\Core\Uri::base()?>car/reserve/delete/",{'reservation_no': '<?php echo $reservation_no ?>'},
			function(data){
				window.location.href='<?php echo Fuel\Core\Uri::base().'car/calendar/'?>'
			}
			);
	}
		}
	);
	<?php } ?>
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
				$.post('<?php echo Fuel\Core\Uri::base()?>car/reserve/getcardinfo/',
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

	$.fn.autoKana('input[name=cs_name]', 'input[name=cs_name_kana]', { katakana : true });
	$('input[name=cs_name]').on('blur', function(){ $('input[name=cs_name_kana]').trigger('change'); });

});
function submitForm(m){
	$('#type').val(m);
	$('#type').attr('val-data',1);
	$('.booking-car').submit();
}
</script>
<?php echo Asset::js('validate/reserve.js'); ?>
<?php echo Asset::js('util.js'); ?>
