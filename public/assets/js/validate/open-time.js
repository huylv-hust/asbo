$.validator.messages.required = '必須です';
$.validator.addMethod("startlessthanenddate", function (value, element) {
    fromdate = $('#form_start_date').val();
    enddate = $('#form_end_date').val();
    if (enddate != '') {
        if ((new Date(fromdate).getTime() <= new Date(enddate).getTime()))
        {
            return true;
        } else {
            return false;
        }
    }

    return true;
}, "利用の期間が正しくありません");
$.validator.addMethod("dateformat", function (value, element) {
    if (value != '') {
        if (value.match(/^\d{4}-\d{1,2}-\d{1,2}$/)) {
            var comp = value.split('-');
            var m = parseInt(comp[1], 10);
            var d = parseInt(comp[2], 10);
            var y = parseInt(comp[0], 10);
            var date = new Date(y, m - 1, d);
            if (date.getFullYear() == y && date.getMonth() + 1 == m && date.getDate() == d) {
                return true;
            } else {
                return false;
            }

            return true;
        } else {
            return false;
        }

    }

    return true;
}, "利用の期間が正しくありません");
$.validator.addMethod("is_future", function (value, element) {
    nowdate = new Date().toJSON().slice(0,10)
    if( (new Date(value).getTime() >= new Date(nowdate).getTime()))
    {
        return true;
    }else{
        return false;
    }
}, "受付期間が正しくありません");
//validation
(function ($, W, D)
{
    var validation = {};

    validation.util =
            {
                setupFormValidation: function ()
                {
                    //form validation rules
                    $("#form-open").validate({
                        rules: {
                            menu_code: {
                                required: true
                            },
                            is_holiday: {
                                required: true
                            },
                            start_date: {
                                required: true,
                                is_future: true,
                                dateformat: true
                            },
                            end_date: {
                                dateformat: true,
                                startlessthanenddate: true
                            }
                        },
                        submitHandler: function (form) {
                            var datastring = $("#form-open").serialize();
                            $.ajax({
                                type: "POST",
                                url: baseUrl + '/reserve/opentime',
                                data: datastring,
                                success: function (result) {
                                    var error = $('.show_err');
                                    if(result == 999){
                                        $('.time-overlap').html('他に時間枠と重複があります');
                                        return false;
                                    }
                                    if(result == 1){
                                        error.html('期間が正しくありません');
                                        return false;
                                    }
                                    if(result == 11){
                                        error.html('他に時間枠と重複があります');
                                        return false;
                                    }
                                    if (!confirm('保存します、よろしいですか？')) {
                                            return false;
                                    }
                                    $.post(baseUrl + '/reserve/opentime/savedata', {flag: 'insert'}, function (result) {
                                            if (result === 'true') {
                                                window.location.href = baseUrl+'reserve/menu';
                                            }else{
                                                error.html(result);
                                            }
                                    });
                                }
                            });
                        }
                    });
                }
            }

    //when the dom has loaded setup form validation rules
    $(D).ready(function ($) {
        validation.util.setupFormValidation();
    });

})(jQuery, window, document);

$(document).ready(function () {
    //Append elements multile rows
    $('button.append-multi').click(function () {
        code = $('#form_menu_code').val();

		var timerangeLast = $(this).parents('tr:first').find('div.time-ranges p:last');
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

		var elementmore = $('<div style="margin-bottom:7px;"></div>');
		var selectStartHour = $('<select class="form-control" name="hoursstart[]"></select>');
		var selectStartMin = $('<select class="form-control" name="minutestart[]"></select>');
		var selectEndHour = $('<select class="form-control" name="hoursend[]"></select>');
		var selectEndMin = $('<select class="form-control" name="minutesend[]"></select>');

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
			' <button type="button" class="btn btn-danger btn-sm appended"><i class="glyphicon glyphicon-trash icon-white"></i></button>'
		);

		/*
        var elementmore = '<div style="margin-bottom:7px;"><select class="form-control" name="hoursstart[]">';
        var hoursend = '～ <select class="form-control" name="hoursend[]">';
        for (i = 0; i <= 23; i++) {
            hours = i;
            if (i < 10) {
                hours = '0' + i;
            }
            elementmore += '<option value="' + i + '">' + hours + '</option>';
            hoursend += '<option value="' + i + '">' + hours + '</option>';
        }
        var minutestart = '<select class="form-control" name="minutestart[]"><option value="00">00</option><option value="30">30</option></select>';
        hoursend += '</select>';
        var minuteend = ': <select class="form-control" name="minutesend[]"><option value="00">00</option><option value="30">30</option></select>';
        elementmore += '</select> : ' + minutestart + ' ' + hoursend + ' ' + minuteend + ' <button type="button" class="btn btn-danger btn-sm appended"><i class="glyphicon glyphicon-trash icon-white"></i></button></div>';
        $('td.is_holiday_app .append').append(elementmore);
		*/

		$('<p></p>').append(elementmore).appendTo('td.is_holiday_app .append');
    });

    //Remove element multi
    $(document).on('click', 'button.appended', function () {
        $(this).parent().remove();
    });

    $(document).on('focus', '#form-open select,#form-open input', function () {
        $('.show_err, .time-overlap').html('');
    });

    //delete record
    $('button#delete').click(function(){
        var open_timer_id = $(this).attr('data-id');
        if(!confirm('削除します、よろしいですか？')){
            return false;
        }
        if(open_timer_id != '')
        {
            $.post(baseUrl + '/reserve/opentime/delete', {open_timer_id:open_timer_id}, function (result) {
                if (result === 'true') {
                    window.location.href = baseUrl+'reserve/menu';
                }
            });
        }
    });
});