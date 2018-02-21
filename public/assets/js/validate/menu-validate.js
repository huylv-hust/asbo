$(document).ready(function () {
	//Onload
	function loadData(code) {
		if (code == '') {
			return false;
		}
		$.post(baseUrl + '/getinfo', {menu_code: code}, function (result) {
			if (!result) {
				return false;
			}
			var data = jQuery.parseJSON(result);
			$('form.menu-' + code + ' input[type=text][name=max_parallel_count]').val(data['max_parallel_count']);
			//append date day in week
			if(typeof(data['week-hoursstart']) != "undefined" && data['week-hoursstart'] !== null){
				append_edit(code, 'week', data['week-hoursstart'], data['week-minutestart'], data['week-hoursend'], data['week-minutesend']);
			}
			//append date holiday
			if(typeof(data['holiday-hoursstart']) != "undefined" && data['holiday-hoursstart'] !== null){
				append_edit(code, 'holiday', data['holiday-hoursstart'], data['holiday-minutestart'], data['holiday-hoursend'], data['holiday-minutesend']);
			}
                        //append list open timer detai in future
                        if(typeof(data['open_timer0']) != "undefined" && data['open_timer0'] !== null){
                            jQuery.each(data['open_timer0'], function (i, val) {
                                if(val['end_date'] != null){
                                    end_date = val['end_date'];
                                }else{
                                    end_date = '';
                                }
                                var is_future0 = '<div><a href="'+baseUrlMain+'reserve/opentime?open_timer_id='+val['open_timer_id']+'">'+val['start_date']+' ～ '+end_date+'</a></div>';
                                $('form.menu-' + code + ' .in_future0').append(is_future0);
                            });
                        }
                        if(typeof(data['open_timer1']) != "undefined" && data['open_timer1'] !== null){
                            jQuery.each(data['open_timer1'], function (i, val) {
                                if(val['end_date'] != null){
                                    end_date = val['end_date'];
                                }else{
                                    end_date = '';
                                }
                                var is_future1 = '<div><a href="'+baseUrlMain+'reserve/opentime?open_timer_id='+val['open_timer_id']+'">'+val['start_date']+' ～ '+end_date+'</a></div>';
                                $('form.menu-' + code + ' .in_future1').append(is_future1);
                            });
                        }
			//append is holiday
			jQuery.each(data['coating_code'], function (i, val) {
				var checkbox = $("form.menu-" + code + " input[class=coating_code][value=" + val['coating_code'] + "]");
				if (checkbox.length) { // 0 == false; >0 == true
					checkbox.prop('checked', true);
				}
			});
			if(data['is_holiday'] == ''){
				$("form.menu-" + code + " .holiday_divapd").find('input.first').val('');
				return false;
			}
			jQuery.each(data['is_holiday'], function (i, val) {
				var append = $("form.menu-" + code + " .holiday_divapd");
				var elementone = '<input type="text" class="form-control dateform" name="is_holiday[]" size="12" value="' + val['stop_date'] + '"> <button type="button" class="btn btn-danger btn-sm appended-one" data-code="'+code+'"><i class="glyphicon glyphicon-trash icon-white"></i></button> ';
				append.append(elementone);
				length = $("form.menu-" + code + " .holiday_divapd").find('button').length;
				//remove first element
				if (length > 1) {
					$("form.menu-" + code + " .holiday_divapd").find('input.first').remove();
					$("form.menu-" + code + " .holiday_divapd").find('button.first').remove();
				}
				//$("form.menu-" + code + " .holiday_divapd").find('button').eq(0).removeClass('appended-one');
				$('.dateform').datepicker();
			});
		});
	}
	//Start load data when page load
	loadData('oil');
	loadData('tire');
	loadData('inspection');
	loadData('wash');
	loadData('coating');

	//Append elements multile rows
	$('.text-right button.append-multi').click(function () {
		type = $(this).attr('name');
		code = $(this).attr('data-code');

		var timerangeLast = $(this).parents('tr:first').find('td.time-ranges div:first div:last');
		var startHour = 0; var startMin = 0;
		var endHour = 0; var endMin = 0;
		if (timerangeLast.size() > 0)
		{
			startHour = parseInt(timerangeLast.find('select:eq(2)').val());
			startMin = parseInt(timerangeLast.find('select:eq(3)').val());
			var diff =
				(parseInt(timerangeLast.find('select:eq(2)').val()) - parseInt(timerangeLast.find('select:eq(0)').val())) * 60 +
				(parseInt(timerangeLast.find('select:eq(3)').val()) - parseInt(timerangeLast.find('select:eq(1)').val()));
			var endTime = startHour * 60 + startMin + diff;
			endHour = Math.floor(endTime / 60);
			endMin = endTime % 60;
		}

		$('.' + type + '_div_apd div:last-child').find('p').remove();

		var elementmore = $('<div style="margin-bottom:7px;"></div>');
		var selectStartHour = $('<select class="form-control" name="' + type + '-hoursstart[]"></select>');
		var selectStartMin = $('<select class="form-control" name="' + type + '-minutestart[]"></select>');
		var selectEndHour = $('<select class="form-control" name="' + type + '-hoursend[]"></select>');
		var selectEndMin = $('<select class="form-control" name="' + type + '-minutesend[]"></select>');

		for (i=0; i<=23; i++)
		{
			valueText = i;
			if (i < 10) { valueText = '0' + i; }
			var optionStart = $('<option></option>').attr('value', i).text(valueText);
			var optionEnd = optionStart.clone();
			if (i === startHour)
			{
				optionStart.prop('selected', true);
			}
			if (i === endHour)
			{
				optionEnd.prop('selected', true);
			}

			selectStartHour.append(optionStart);
			selectEndHour.append(optionEnd);
		}

		for (i=0; i<=30; i+=30)
		{
			valueText = i;
			if (i < 10) { valueText = '0' + i; }
			var optionStart = $('<option></option>').attr('value', i).text(valueText);
			var optionEnd = optionStart.clone();
			if (i === startMin)
			{
				optionStart.prop('selected', true);
			}
			if (i === endMin)
			{
				optionEnd.prop('selected', true);
			}

			selectStartMin.append(optionStart);
			selectEndMin.append(optionEnd);
		}

		elementmore.append(selectStartHour);
		elementmore.append(' : ');
		elementmore.append(selectStartMin);
		elementmore.append(' ～ ');
		elementmore.append(selectEndHour);
		elementmore.append(' : ');
		elementmore.append(selectEndMin);
		elementmore.append(
			' <button type="button" class="btn btn-danger btn-sm appended" data-type="'+type+'" data-code="'+code+'"><i class="glyphicon glyphicon-trash icon-white"></i></button>'
		);

		$(this).parent().next().find('.'+type+'_div_apd').append(elementmore);
	});

	//Append element one row
	$('.text-right button.append-one').click(function () {
		code = $(this).attr('data-code');
		var t = new Date();
		var month = t.getMonth() + 1;
		var day = t.getDate();
		if (month < 10) {
			month = '0' + month;
		}
		if (day < 10) {
			day = '0' + day;
		}
		var nowDate = t.getFullYear() + '-' + month + '-' + day;
		var eInput = $('<input type="text" class="form-control dateform" name="is_holiday[]" size="12" value="' + nowDate + '">');
		var eButton = $('<button type="button" class="btn btn-danger btn-sm appended-one" data-code="'+code+'"><i class="glyphicon glyphicon-trash icon-white"></i></button>');
		$(this).parent().next().find('.holiday_divapd').append(eInput).append(' ').append(eButton).append(' ');
		//call datepicker after append
		eInput.datepicker().focus();
	});
	//Remove element multi
	$(document).on('click', 'button.appended', function () {
		code   = $(this).attr('data-code');
		type = $(this).attr('data-type');
		//length  = $('form.menu-'+code+' td.'+type+' button.appended').length;
		//if(length > 1){
			$(this).parent().remove();
		//}
	});
	//Remove element is_holiday
	$(document).on('click', 'button.appended-one', function () {
		//code   = $(this).attr('data-code');
		//length  = $('form.menu-'+code+' .holiday_divapd button').length;
		//if(length > 1){
		$(this).prev().remove();
		$(this).remove();
		//}
	});

	//Validation
	$('button.single').click(function () {
		var id = $(this).attr('data-id');
		var obj = '#menu-form' + id;
		var max_count = $(obj + ' input[name=max_parallel_count]').val();
                var coating_leng = $(obj + ' input[type=checkbox][class=coating_code]:checked').length;

		if (max_count == '' && (id == 4 || id == 5)) {
		//if (max_count == '' && id == 5) {
			$(obj + ' div.max_parallel_count').html('必須です');
			return false;
		}
		if (max_count != '' && isNaN(max_count)) {
			$(obj + ' div.max_parallel_count').html('数字で入力してください');
			return false;
		}
		if (max_count != '' && max_count.length > 11) {
			$(obj + ' div.max_parallel_count').html('11桁の数字以内で入力してください');
			return false;
		}
                if(id == 5 && coating_leng == 0){
                    $(obj + ' div.coating_code').html('必須です');
                    return false;
                }
		var datastring = $('#menu-form' + id).serialize();
		$.ajax({
			type: "POST",
			url: baseUrl,
			data: datastring,
			success: function (result) {
				errorMgs = '';
				objErr = 'show-error';
				switch (result) {
					case '1'  :
						errorMgs = '期間が正しくありません';
						objErr = 'dayinweek';
						break;
					case '11'  :
						errorMgs = '他に時間枠と重複があります';
						objErr = 'dayinweek';
						break;
					case '2'  :
						errorMgs = '期間が正しくありません';
						objErr = 'holidays';
						break;
					case '22'  :
						errorMgs = '他に時間枠と重複があります';
						objErr = 'holidays';
						break;
					case '3'  :
						errorMgs = '日付が正しくありません。';
						objErr = 'holiday_divapd_err';
						break;
					default :
						errorMgs = '';
						objErr = 'show-error';
						break;
				}

				if (errorMgs != '') {
					$(obj + ' .' + objErr).html(errorMgs);
					return false;
				}
				if (!confirm('保存します、よろしいですか？')) {
					return false;
				}
				$.post(baseUrl + '/savedata', {flag: 'insert'}, function (result) {
					if (result === 'true') {
						location.reload();
					}
				});
			}
		});
	});
	//hide msg error
	$(document).on('focus', 'td.hideerr select, td.hideerr input', function () {
		$('div.errors').html('');
	});

	function append_edit(code, type, hoursstart, minutestart, hoursend, minutesend) {
		jQuery.each(hoursstart, function (i, val) {
			var elementmore = '<div style="margin-bottom:7px;"><select class="form-control" name="' + type + '-hoursstart[]">';
			//start minutes checked
			var listminutes = '<select class="form-control" name="' + type + '-minutestart[]">';
			if (parseInt(minutestart[i]['start_time_m']) === 0) {
				listminutes += '<option value="00" selected="selected">00</option>';
			} else {
				listminutes += '<option value="00">00</option>';
			}
			if (parseInt(minutestart[i]['start_time_m']) === 30) {
				listminutes += '<option value="30" selected="selected">30</option>';
			} else {
				listminutes += '<option value="30">30</option>';
			}
			listminutes += '</select>';
			//end minutes checked
			var listendminutes = '<select class="form-control" name="' + type + '-minutesend[]">';
			if (parseInt(minutesend[i]['end_time_m']) === 0) {
				listendminutes += '<option value="00" selected="selected">00</option>';
			} else {
				listendminutes += '<option value="00">00</option>';
			}
			if (parseInt(minutesend[i]['end_time_m']) === 30) {
				listendminutes += '<option value="30" selected="selected">30</option>';
			} else {
				listendminutes += '<option value="30">30</option>';
			}
			listendminutes += '</select>';
			//end hours checked
			endhours = '～ <select class="form-control" name="' + type + '-hoursend[]">';
			//start hours checked
			for (j = 0; j <= 23; j++) {
				var hours = j;
				if (j < 10) {
					hours = '0' + j;
				}
				//checked
				selected = '';
				selected_end = '';
				if (parseInt(val['start_time_h']) === j) {
					selected = 'selected';
				}
				elementmore += '<option value="' + j + '"' + selected + '>' + hours + '</option>';
				if (parseInt(hoursend[i]['end_time_h']) === j) {
					selected_end = 'selected';
				}
				endhours += '<option value="' + j + '"' + selected_end + '>' + hours + '</option>';
			}
			endhours += '</select>';
			elementmore += '</select> : ' + listminutes + ' ' + endhours + ' : ' + listendminutes + ' <button type="button" class="btn btn-danger btn-sm appended" data-type="'+type+'" data-code="'+code+'"><i class="glyphicon glyphicon-trash icon-white"></i></button></div>';
			$("form.menu-" + code + " ." + type + "_div_apd").append(elementmore);
			length = $("form.menu-" + code + " ." + type + "_div_apd").find('button').length;
			//remove first element
//			if (length > 1) {
//				$("form.menu-" + code + " ." + type + "_div_apd").find('div.first').remove();
//			}
		});
	}
});