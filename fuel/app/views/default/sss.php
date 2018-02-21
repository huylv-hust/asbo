<form method="post" action="http://<?php echo htmlspecialchars($ssshost) ?>/sss/s/tp00300/forward.do">
	<input type="hidden" name="btnTp" value="戻る">
</form>
<script>
    $(function()
    {
		$('form').trigger('submit');
	});
</script>