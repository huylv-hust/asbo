//validation
function pad(n, width, z) {
	z = z || '0';
	n = n + '';
	return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}
function check_is_future(date) {
	var fullDate = new Date();
	var twoDigitMonth = ((fullDate.getMonth().length + 1) === 1) ? (fullDate.getMonth() + 1) : '0' + (fullDate.getMonth() + 1);
	var twoDigitDay = (fullDate.getDate() < 10) ? '0' + (fullDate.getDate()) : fullDate.getDate();
	var currentDate = fullDate.getFullYear() + '-' + twoDigitMonth + '-' + twoDigitDay + ' ' + fullDate.getHours() + ':' + fullDate.getMinutes();

	if (currentDate < date)
		return true;
	return false;
}
$.validator.addMethod("hankana", function (value, element) {
	//^ァ-ン0-9\-\+\s\(\)]
	if (value.match(/^[\uFF65-\uFF9F0-9\-\+\s\(\)]+$/)) {
		return true;
	} else {
		return false;
	}
}, "半角カタカナを入力してください"
		);
$.validator.addMethod("startlessthanenddate", function (value, element) {
    fromdate = $('input[name=arrival_time]').val()+' '+$('#form_arrival_time_hh').val()+':'+$('#form_arrival_time_mm').val();
    enddate = $('input[name=return_time]').val()+' '+$('#form_return_time_hh').val()+':'+$('#form_return_time_mm').val();
    if ($('input[name=return_time]').val() != '') {
        if(fromdate < enddate)
        {
            return true;
        }else{
            return false;
        }
    }
    return true;
}, "入庫予定には納車予定より過去を指定して下さい。");
$.validator.addMethod("totalPiece", function (value, element) {
    var a_piece = $('input[name=a_piece_count]').val();
    var b_piece = $('input[name=b_piece_count]').val();
    total_piece = parseInt(a_piece) + parseInt(b_piece);
    if (total_piece > 0) {
        return true;
    }else{
        return false;
    }
}, "ピース数を入力して下さい");
$.validator.addMethod("is_future", function (value, element) {
	date_time = $('input[name=' + element.name + ']').val()
			+ ' ' + pad($('input[name=' + element.name + '_hh]').val(), 2)
			+ ':' + pad($('input[name=' + element.name + '_mm]').val(), 2);
	//alert(date_time);
	if ($('input[name=' + element.name + ']').val() == '' || $('input[name=' + element.name + ']').val() == null)
		return true;
	if (check_is_future(date_time))
		return true;
	return false
}, "正しくありません");
$.validator.addMethod("int_11", function (value, element) {
	if (value > 2147483648)
		return false;
	return true;
}, "正しくありません");
//date format
$.validator.addMethod("dateformat", function (value, element) {
	if (value.match(/^\d{4}-\d{2}-\d{2}$/)) {

		var arr_date = value.split('-');
		if (arr_date['1'] > 12 || arr_date['2'] > 31)
		{
			return false;
		}
		return true;
	} else {
		return false;
	}
}, "利用の期間が正しくありません");
$.validator.addMethod("is_number", function (value, element) {
	if (value.match(/^[0-9]+$/))
		return true;
	else
		return false;
}, "正しくありません");
$.validator.addMethod("is_latin", function (value, element) {
	if (value.match(/^[0-9A-Za-z]+$/))
		return true;
	else
        {
            if(value == '' || value == null)
                return true;
            return false;
        }
}, "正しくありません");
$.validator.addMethod("interger", function (value, element) {
	if (value > 0) {
		return true;
	} else {
		return false;
	}
}, "正しくありません");

//date format
$.validator.addMethod("hoursformat", function (value, element) {
	if (value >= 0 && value <= 23) {
		return true;
	} else {
		return false;
	}
}, "正しくありません");
//date format
$.validator.addMethod("minutesformat", function (value, element) {
	if (value >= 0 && value <= 59) {
		return true;
	} else {
		return false;
	}
}, "正しくありません");

(function ($, W, D)
{
	var validation = {};

	validation.util =
			{
				setupFormValidation: function ()
				{
					//form validation rules
					$("#validation").validate({
						errorPlacement: function (error, element) {
							var err = element.parents('td');
							$(err).append(error);
							var err1 = element.parents('.input-group');
							$(err1).append(error);
							var err2 = element.parents('.col-time');
							$(err2).append(error);
						},
						rules: {
							sscode: {
								required: true,
								number: true,
								rangelength: [6, 6]
							},
                                                        color_number: {
                                                            is_latin: true,
                                                            maxlength: 10
                                                        },
							return_time: {
								dateformat: {
									depends: function (element) {
										if ($(this).val() == '')
											return false;
										return true;
									}
								},
								is_future: false

							},
							return_time_mm: {
								required: {
									depends: function (element) {

										if ($("#return_time").val() == '' || $("#return_time").val() == null)
											return false;
										return true;
									}
								},
								number: true,
								maxlength: 2,
								minutesformat: true,
								startlessthanenddate: true

							},
							return_time_hh: {
								required: {
									depends: function (element) {

										if ($("#return_time").val() == '' || $("#return_time").val() == null)
											return false;
										return true;
									}
								},
								number: true,
								maxlength: 2,
								minutesformat: true

							},
							arrival_time: {
								required: true,
								dateformat: true,
								is_future: false
							},
							arrival_time_mm: {
								required: true,
								number: true,
								maxlength: 2,
								minutesformat: true
							},
							arrival_time_hh: {
								required: true,
								number: true,
								maxlength: 2,
								hoursformat: true
							},
							cs_card_number: {
								number: true,
								rangelength: [16, 16]
							},
							cs_name: {
								required: true,
								maxlength: 15
							},
							cs_name_kana: {
								required: true,
								maxlength: 20,
								hankana: true
							},
							cs_mobile_tel: {
								required: {
									depends: function (element) {
										return $.trim($("input[name=cs_house_tel]").val()) === '';
									}
								},
								number: true,
								maxlength: 11
							},
							cs_house_tel: {
								required: {
									depends: function (element) {
										return $.trim($("input[name=cs_mobile_tel]").val()) === '';
									}
								},
								number: true,
								maxlength: 11
							},
							plate_no: {
								required: true,
								is_number: true,
								rangelength: [4, 4]
							},
							car_maker_code: {
								required: true
							},
							car_model_code:{
                                                        required:{
                                                            depends: function(element)
                                                                {
                                                                   if ($('#check_model_code').is(":checked"))
                                                                   {    
                                                                       return false;
                                                                   }
                                                                   return true;
                                                                }
                                                            },
                                                        },
                                                        check_model_code:{
                                                            required:{
                                                                    depends: function(element)
                                                                    {
                                                                        if($("#car_model_code option:selected").val() == '0')
                                                                            return true;

                                                                        return false;
                                                                    }
                                                                }
                                                        },
							price: {
								required: true,
								is_number: true,
								number: true,
								int_11: true
							},
							a_piece_count: {
								required: true,
								int_11: true,
								is_number: true
							},
							b_piece_count: {
								required: true,
								int_11: true,
								is_number: true,
                                                                totalPiece: true

							},
                                                       
                                                        repair_staff_id:{
                                                            required: true
                                                        },
                                                        policy:{
                                                             required: true
                                                        },
                                                        
                                                        
						},
						messages: {
							sscode: {
								required: "必須です",
								number: "正しくありません",
								rangelength: "6桁の数字で入力してください"
							},
                                                        color_number: {
								is_latin: "半角英数字の10桁以内で入力してください"
							},
							pit_no: {
								required: "必須です"
							},
							return_time: {
								is_future: "過去の日付を入力することはできません"
							},
							return_time_mm: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							return_time_hh: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							arrival_time: {
								required: "必須です",
								is_future: "過去の日付を入力することはできません"
							},
							arrival_time_mm: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							arrival_time_hh: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							cs_card_number: {
								required: "必須です",
								number: "16桁の数字で入力してください",
								rangelength: "16桁の数字で入力してください"
							},
							cs_name: {
								required: "必須です",
								maxlength: "15文字以内で入力してください"
							},
							cs_name_kana: {
								required: "必須です",
								maxlength: "20文字以内で入力してください"
							},
							cs_mobile_tel: {
								required: "必須です",
								number: "正しくありません",
								maxlength: "11桁の数字で入力してください"
							},
							cs_house_tel: {
								required: "必須です",
								number: "正しくありません",
								maxlength: "11桁の数字で入力してください"
							},
							plate_no: {
								required: "必須です",
								number: "正しくありません",
								rangelength: "4桁の数字で入力してください"
							},
							car_maker_code: {
								required: "必須です"
							},
							car_model_code: {
								required: "必須です",
								interger: "必須です"
							},
                                                        check_model_code: {
								required: "必須です"
								
							},
							a_piece_count: {
								required: "必須です",
								int_11: "入力した値が大きすぎです",
								is_number: "数字で入力してください"
							},
							b_piece_count: {
								required: "必須です",
								int_11: "入力した値が大きすぎです",
								is_number: "数字で入力してください"
							},
							price: {
								required: "必須です",
								int_11: "入力した値が大きすぎです",
								is_number: "数字で入力してください"
							},
                                                       
                                                        repair_staff_id :{
                                                            required: "必須です"
                                                        },
                                                        policy :{
                                                            required: "必須です",
                                                        }
                                                       

						},
						submitHandler: function (form) {
							if(!confirm('保存します、よろしいですか？')){
                                                            return false;
                                                        }
                                                        if($('#form_is_car_request').val() == 1){
                                                            if(!confirm('代車予約画面に行きますか？')){
                                                                form.submit();
                                                                return false;
                                                            }
                                                            $('input[name=savejson]').val(1);
                                                        }
                                                        
                                                        form.submit();
						}
					});
				}
			}

	//when the dom has loaded setup form validation rules
	$(D).ready(function ($) {
		validation.util.setupFormValidation();
	});

})(jQuery, window, document);

