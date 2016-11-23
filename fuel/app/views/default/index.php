<style>
	td.ui-widget-content {
		background-color: #ffffff !important;
	}

</style>
<div class="container">
	<h3>
		作業予約スケジュール（本日)
		<button type="button" class="btn btn-warning btn-sm" name="print-btn">
			<i class="glyphicon glyphicon-print icon-white"></i> 印刷
		</button>
	</h3>

	<div class="panel ">
		<div class="panel-body">
			<p id="calendar"></p>
		</div>
	</div>
</div>
<script>
    $(function (e)
    {
		$('button[name=print-btn]').on('click', function()
		{
			print();
		});

		var sscode;
		if (Util.checkCookie("reserve_sscode")!=""){
			sscode = Util.getCookie('reserve_sscode');
		}else{
			sscode = Util.getCookie('sscode');
		}

        var calendar = $('#calendar').fullCalendar({
            theme: true,
            header: {
                left: '',
                center: 'title',
                right: ''
            },
            allDaySlot: false,
            axisFormat: 'HH:mm',
            timeFormat: 'HH:mm',
            minTime: '00:00:00',
            maxTime: '24:00:00',
            scrollTime: '08:00:00',
            selectable: true,
            editable: false,
            eventClick: function (event, jsEvent, view) {
                if (event.type == 'repair') {
					location.href = '<?php echo Uri::base(true) ?>repair/reserve?reservation_no='+event.reservation_no+'&pos=-1&type='+event.type;

                } else {
					location.href = '<?php echo Uri::base(true) ?>reserve/reserve?reservation_no='+event.reservation_no+'&pos=-1&type='+event.type;

                }
            },
            lang: 'ja',

			select: function (start, end, jsEvent, view)
            {
                if (
					start.format() != end.add(-1, 's').format()
				) {
					var month = moment(start).format('YYYY-MM');
					Util.setCookie("currentMonth", month, 1);
					var start = moment(start).format('YYYY-MM-DD HH:mm:ss');
					var end = moment(end).format('YYYY-MM-DD HH:mm:ss');
					if(moment(end).unix()- moment(start).unix() == '1799' ){
						var startTime = moment(start).format('YYYY-MM-DD HH:mm:ss');
						var endTime = moment(start).format('YYYY-MM-DD');
					}else{
						var startTime = moment(start).format('YYYY-MM-DD HH:mm:ss');
						var endTime =moment.unix(moment(end).unix()+1).format('YYYY-MM-DD HH:mm:ss');
					}
					location.href = '<?php echo Uri::base(true) ?>reserve/reserve?pos=-1&start='+startTime+'&end='+endTime+'&sscode='+sscode+'<?php if(Fuel\Core\Input::get('type_check')) echo '&type_check=1'?>';
				}
            },

        });
        calendar.fullCalendar('changeView', 'agendaDay');
		getEvent();
    });
	function getEvent(){
		var myCalendar = $('#calendar');
            myCalendar.fullCalendar();
            $.ajax({
                type: "POST",
                url: "<?php echo Uri::base(true) ?>default/get_booking_data",
                dataType: 'json',
                success: function (data) {
                    $(data).each(function (index) {
                        myCalendar.fullCalendar('renderEvent', data[index] , true );
                    });

                }
        });
	}
</script>