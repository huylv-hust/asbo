<?php echo Asset::js('util.js'); ?>

<div class="container">
	<h3>
		<?php if($staff_name){?>
		<?= $staff_name ?>さんのリペアスケジュール
		<?php }else{ ?>
		※技術者を選択してください
		<?php } ?>
		<button type="button" class="btn btn-info btn-sm" name="findstaff-btn">
			<i class="glyphicon glyphicon-flag icon-white"></i> 指定技術者変更
		</button>
	</h3>

	<div class="row">
		<p id="calendar"></p>
	</div>

</div>

<?php echo $stafffinder; ?>
<script>
    $(function (e)
    {
        var calendar = $('#calendar').fullCalendar({
            theme: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaDay'
            },
            allDaySlot: false,
            axisFormat: 'HH:mm',
            timeFormat: 'HH:mm',
            minTime: '00:00:00',
            maxTime: '24:00:00',
            selectable: true,
            editable: false,
			eventLimit:  true,
            dayClick: function (date, jsEvent, view)
            {
                if (view.name == 'month') {
                    calendar.fullCalendar('gotoDate', date);
                    calendar.fullCalendar('changeView', 'agendaDay');
                }
            },
            select: function (start, end, jsEvent, view)
            {
				if(Util.checkCookie("staff_id")){
					if (
							start.format() != end.add(-1, 's').format() ||
							view.name == 'agendaDay'
							) {
							var allDay = !start.hasTime() && !end.hasTime();
							var month = moment(start).format('YYYY-MM');
							Util.setCookie("currentMonth", month, 1);
							if(allDay){
								var startTime = moment(start).format('YYYY-MM-DD');
								var endTime = moment(end).format('YYYY-MM-DD');
								location.href = '<?php echo Uri::base(true) ?>repair/schedule?start='+startTime+'&end='+endTime;
							}else{
								var start = moment(start).format('YYYY-MM-DD HH:mm:ss');
								var end = moment(end).format('YYYY-MM-DD HH:mm:ss');
								if(moment(end).unix()- moment(start).unix() == '1799' ){
									var startTime = moment(start).format('YYYY-MM-DD HH:mm:ss');
									var endTime ='';
								}else{
									var startTime = moment(start).format('YYYY-MM-DD HH:mm:ss');
									var endTime =moment.unix(moment(end).unix()+1).format('YYYY-MM-DD HH:mm:ss');
								}
								location.href = '<?php echo Uri::base(true) ?>repair/schedule?start='+startTime+'&end='+endTime;
							}

					}
				}else{
					alert('技術者を選択してください');
				}
            },
			eventClick: function (calEvent, jsEvent, view)
			{
				location.href = '<?php echo Uri::base(true) ?>repair/schedule?repair_schedule_id='+calEvent.repair_schedule_id;
			},
            lang: 'ja'
        });

        $('#ss a').on('click', function () {
            $(this).addClass('active').siblings().removeClass('active');
            return false;
        });

        $('button[name=findstaff-btn]').on('click', function () {
            $('#stafffinder').modal();
            return false;
        });

        $('#stafffinder div.list-group a').on('click', function () {
            $('#stafffinder').modal('hide');
            return false;
        });
		getEvents();
    });
	function getEvents(){
		var myCalendar = $('#calendar');
            myCalendar.fullCalendar();
            $.ajax({
                type: "POST",
                url: "<?php echo Uri::base(true) ?>repair/staffschedule/get_booking_data",
                dataType: 'json',
                success: function (data) {
                    $(data).each(function (index) {
                        myCalendar.fullCalendar('renderEvent', data[index] , true );
                    });

                }
        });
	}
</script>
