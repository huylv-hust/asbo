//validation
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
	fromdate = $('input[name=from_date]').val()
			+ ' ' + $('select[name=from_date_hh]').val()
			+ ':' + $('select[name=from_date_mm]').val();
	todate = $('input[name=to_date]').val()
			+ ' ' + $('select[name=to_date_hh]').val()
			+ ':' + $('select[name=to_date_mm]').val();
	if (fromdate < todate) {
		return true;
	} else {
		return false;
	}
}, "利用の期間が正しくありません");
//date format
$.validator.addMethod("dateformat", function (value, element) {
	if (value.match(/^\d{4}-\d{2}-\d{2}$/)) {
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
                                                    },
                                                    rules: {
							from_date: {
								required: true,
								dateformat: true
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
							to_date: {
								required: true,
								dateformat: true
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
							policy: {
								required: true
							}
						},
						messages: {
							from_date: {
								required: "必須です"
							},
							from_date_mm: {
								required: "必須です",
								number: "正しくありません"
							},
							from_date_hh: {
								required: "必須です",
								number: "正しくありません"
							},
							to_date_mm: {
								required: "必須です",
								number: "正しくありません"
							},
							to_date_hh: {
								required: "必須です",
								number: "正しくありません"
							},
							to_date: {
								required: "必須です"
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
							policy: {
								required: "必須です",
							}
						},
						submitHandler: function (form) {
							var val_data = $('#type').attr('val-data');
							if (val_data == 0)
							{
								if (confirm('保存します、よろしいですか？'))
									$("#dialog").dialog("open");
								//form.submit();
								return false;
							}
							else {
								form.submit();
							}
						}
					});
					$("#saveCar").validate({
						rules: {
							car_name: "required",
						},
						messages: {
							car_name: {
								required: "利用期間は必須です"
							},
						},
						submitHandler: function (form) {
							form.submit();
						},
					});
				}
			}

	//when the dom has loaded setup form validation rules
	$(D).ready(function ($) {
		validation.util.setupFormValidation();
	});

})(jQuery, window, document);

