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
	fromdate = $('.from_date').val()
			+ ' ' + pad($('.from_date_hh').val(), 2)
			+ ':' + pad($('.from_date_mm').val(), 2);
	todate = $('.to_date').val()
			+ ' ' + pad($('.to_date_hh').val(), 2)
			+ ':' + pad($('.to_date_mm').val(), 2);


	if (fromdate < todate)
	{
		return true;
	} else
	{
		return false;
	}
}, "利用の期間が正しくありません");
//date format
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
//date format
$.validator.addMethod("hoursformat", function (value, element) {
	if (value >= 0 && value <= 23) {
		return true;
	} else {
		return false;
	}
}, "正しくありません");
$.validator.addMethod("is_number", function (value, element) {

	if (value > 0)
		return true;
	else
		return false;
}, "利用の期間が正しくありません");
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
							var err1 = element.parents('.from_date_1');
							$(err1).append(error);
							var err2 = element.parents('.to_date_1');
							$(err2).append(error);
                                                    },
                                                    rules: {
							sscode: {
								required: true,
								number: true,
								rangelength: [6, 6]
							},
							menu_name: {
								required: {
									depends: function (element) {

										var pit_work = $("#form_pit_work option:selected").val();
										if (pit_work == "other")
											return true;
										return false;
									}
								},
								maxlength: 50

							},
							pit_no: {
								is_number: {
									depends: function (element) {


										var pit_work = $("#form_pit_work option:selected").val();
										if ((pit_work == "oil" || pit_work == "tire" || pit_work == "inspection"))
										{
											return true;
										}
										else
											return false;
									}
								}
							},
							arrival_time: {
								required: {
									depends: function (element) {
										var pit_work = $("#form_pit_work option:selected").val();
										if (pit_work == "inspection")
											return true;
										return false;
									}
								},
								is_future: false,
								dateformat: {
									depends: function (element) {
										if ($(this).val() == '')
											return false;
										return true;
									},
								}
							},
							arrival_time_mm: {
								required: {
									depends: function (element) {
										var pit_work = $("#form_pit_work option:selected").val();
										if (pit_work == "inspection")
											return true;
										return false;
									}
								},
								number: true,
								maxlength: 2,
								minutesformat: true
							},
							arrival_time_hh: {
								required: {
									depends: function (element) {
										var pit_work = $("#form_pit_work option:selected").val();
										if (pit_work == "inspection")
											return true;
										return false;
									}
								},
								number: true,
								maxlength: 2,
								hoursformat: true
							},
							from_date: {
								required: true,
								dateformat: true,
								is_future: false
							},
							from_date_mm: {
								required: true,
								number: true,
								maxlength: 2,
								minutesformat: true
							},
							from_date_hh: {
								required: true,
								number: true,
								maxlength: 2,
								hoursformat: true
							},
							from_date_re: {
								required: true,
								dateformat: true,
							},
							from_date_mm_re: {
								required: true,
								number: true,
								maxlength: 2,
								minutesformat: true
							},
							from_date_hh_re: {
								required: true,
								number: true,
								maxlength: 2,
								hoursformat: true
							},
							to_date: {
								required: true,
								dateformat: true,
								is_future: false
							},
							to_date_mm: {
								required: true,
								number: true,
								maxlength: 2,
								startlessthanenddate: true,
								minutesformat: true
							},
							to_date_hh: {
								required: true,
								number: true,
								maxlength: 2,
								hoursformat: true
							},
							to_date_re: {
								required: true,
								dateformat: true,
							},
							to_date_mm_re: {
								required: true,
								number: true,
								maxlength: 2,
								startlessthanenddate: true,
								minutesformat: true
							},
							to_date_hh_re: {
								required: true,
								number: true,
								maxlength: 2,
								hoursformat: true
							},
							inspection_date: {
								dateformat: {
									depends: function (element) {
										if ($(this).val() == '')
											return false;
										return true;
									}
								}
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
								number: true,
								rangelength: [4, 4]
							},
							car_maker_code: {
								required: true
							},
							car_model_code: {
								required: {
									depends: function (element)
									{
										if ($('#check_model_code').is(":checked"))
										{
											return false;
										}
										return true;
									}
								}

							},
							check_model_code: {
								required: {
									depends: function (element)
									{
										if ($("#car_model_code option:selected").val() == '0')
											return true;

										return false;
									}
								}
							},
							other_request: {
								maxlength: 1000
							},
							policy: {
								required: true
							}
						},
						messages: {
							sscode: {
								required: "必須です",
								number: "正しくありません",
								rangelength: "6桁の数字で入力してください"
							},
							menu_name: {
								required: "必須です",
								maxlength: "50桁の数字で入力してください"
							},
							pit_no: {
								is_number: "必須です"
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
							from_date: {
								required: "必須です",
								is_future: "過去の日付を入力することはできません"

							},
							from_date_mm: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							from_date_hh: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							from_date_re: {
								required: "必須です",
							},
							from_date_mm_re: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							from_date_hh_re: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							to_date_mm: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							to_date_hh: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							to_date: {
								required: "必須です",
								is_future: "過去の日付を入力することはできません"

							},
							to_date_mm_re: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							to_date_hh_re: {
								required: "必須です",
								maxlength: "利用の期間が正しくありません",
								number: "正しくありません"
							},
							to_date_re: {
								required: "必須です",
							},
							inspection_date: {
								dateformat: "日付がが正しくありません"
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
								is_number: "必須です"
							},
							check_model_code: {
								required: "必須です"
							},
							other_request: {
								maxlength: "1000文字以内で入力してください"
							},
							policy: {
								required: "必須です",
							}
						},
						submitHandler: function (form) {
							if (!confirm('保存します、よろしいですか？')) {
								return false;
							}
							if ($('#form_is_car_request').val() == 1) {
								if (!confirm('代車予約画面に行きますか？')) {
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

