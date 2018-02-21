<style>
	.fc-time{display: none}
</style>
<?php
	if(\Fuel\Core\Input::get('redirect') != '1')
	{
		Fuel\Core\Cookie::delete('repair_calendar_url_redirect');
	}
?>
<div class="container">
	<h3>
		<span <?php if(\Cookie::get('sscode') != $repair_sscode){echo "class='ss-name'";} ?>><?= $repair_sscode ?> <?= $repair_sscodename ?></span>のリペア予約状況
		<button type="button" class="btn btn-info btn-sm" name="findss-btn">
			<i class="glyphicon glyphicon-flag icon-white"></i> 指定SS変更
		</button>
		<button type="button" class="btn btn-warning btn-sm" name="print-btn">
			<i class="glyphicon glyphicon-print icon-white"></i> 印刷
		</button>
	</h3>

	<p class="text-center">
		<a href="calendar">カレンダー表示</a>
		|
		<a href="list">リスト表示</a>
	</p>

	<p id="calendar"></p>

</div>

<?php echo $ssfinder; ?>

<script>
    $(function (e)
    {
		$('button[name=print-btn]').on('click', function()
		{
			print();
		});


		if (Util.checkCookie("repair_sscode")!=""){
			sscode = Util.getCookie('repair_sscode');
		}else{
			sscode = Util.getCookie('sscode');
		}
        var calendar = $('#calendar').fullCalendar({
            theme: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaDay'
            },
            dayClick: function (date, jsEvent, view)
            {
                if (view.name == 'month') {
                    calendar.fullCalendar('gotoDate', date);
                    calendar.fullCalendar('changeView', 'agendaDay');
                } else if (view.name == 'agendaDay') {
					date = moment(date).format('YYYY-MM-DD HH:mm:ss');
					location.href = '<?php echo Uri::base(true) ?>repair/reserve?date='+date+'&sscode='+sscode+'<?php if(Fuel\Core\Input::get('type_check')) echo '&type_check=1'?>';

                }

            },
			viewRender:function (view, element) {
				var b = $('#calendar').fullCalendar('getDate');
				Util.setCookieRedirect('<?php echo Uri::base(true) ?>',b.format('L'),'repair_calendar');
			},
			eventMouseover: function(calEvent, jsEvent) {
				var tooltip = '<div class="tooltipevent" style="padding:0px 10px 0px 10px;background:#ccc;position:absolute;z-index:10001;">' + calEvent.title + '</div>';
				$("body").append(tooltip);
				$(this).mouseover(function(e) {
					$(this).css('z-index', 10000);
					$('.tooltipevent').fadeIn('500');
					$('.tooltipevent').fadeTo('10', 1.9);
				}).mousemove(function(e) {
					$('.tooltipevent').css('top', e.pageY + 10);
					$('.tooltipevent').css('left', e.pageX + 20);
				});
			},

			eventMouseout: function(calEvent, jsEvent) {
				$(this).css('z-index', 8);
				$('.tooltipevent').remove();
			},
            eventClick: function (calEvent, jsEvent, view)
            {
				Util.pleaseWait();
				location.href = '<?php echo Uri::base(true) ?>repair/reserve?reservation_no='+calEvent.reservation_no;
            },
//			eventAfterRender: function (event, $el, view) {
//                $el.removeClass('fc-short');
//
//            },
            allDaySlot: false,
            axisFormat: 'HH:mm',
            timeFormat: 'HH:mm',
            minTime: '00:00:00',
            maxTime: '24:00:00',
			eventLimit:  true,
			editable : false,
            scrollTime: '08:00:00',
			defaultDate: moment('<?php if( Fuel\Core\Cookie::get('repair_calendar_url_redirect') !='') echo Fuel\Core\Cookie::get('repair_calendar_url_redirect'); else echo date('Y-m')  ?>'),
            lang: 'ja'
        });

        $('button[name=add-btn]').on('click', function () {
            location.href = 'reserve';
            return false;
        });

        $('button[name=findss-btn]').on('click', function () {
            $('#ssfinder').modal();
            return false;
        });

        $('#ssfinder div.list-group a').on('click', function () {
            $('#ssfinder').modal('hide');
            return false;
        });
		getEvents();
    });
	function getEvents(){
		var myCalendar = $('#calendar');
            myCalendar.fullCalendar();
            $.ajax({
                type: "POST",
                url: "<?php echo Uri::base(true) ?>repair/calendar/get_booking_data",
                dataType: 'json',
                success: function (data) {
                    $(data).each(function (index) {
                        myCalendar.fullCalendar('renderEvent', data[index] , true );
                    });

                }
        });
	}
</script>
