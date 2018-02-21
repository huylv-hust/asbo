//validation
(function ($, W, D)
{
    var validation = {};

    validation.util =
            {
                setupFormValidation: function ()
                {
                    //form validation rules
                    $("#preview_form").validate({
                        rules: {
                            pit_name: {
                                required: true,
                                maxlength: 50
                            },
                            note: {
                                maxlength: 500
                            }
                        },
                        messages: {
                            pit_name: {
                                required: "必須です",
                                maxlength: "50文字以内で入力してください"
                            },
                            note: {
                                maxlength: "500文字以内で入力してください"
                            }
                        },
                        submitHandler: function (form) {
                            var pit_no = $('#pit_no').val();
                            var pit_na = $('input[name=pit_name]').val();
                            $.post(baseUrl + '/unique', {pit_name: pit_na, pit_no: pit_no}, function (result) {
                                if (result == 'unique') {
                                    $('.show_err').show().html('既に同じピット名が登録済みです');
                                    return false;
                                } else {
                                    if (!confirm('保存します、よろしいですか？')) {
                                        return false;
                                    }
                                    var datastring = $("#preview_form").serialize();
                                    $.ajax({
                                        type: "POST",
                                        url: baseUrl + '/input',
                                        data: datastring,
                                        success: function (result) {
                                            window.location = baseUrl;
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