<div class="row">
<div class="one-third column">
<form action="title_ins_exe" method="post">
	<input type="text" name="title">
	<br>
	<textarea name="text"></textarea>
	<br>
	<input type="submit" value="send">
</form>
</div><!--1/3-->
<div class="one-third column">
<form action='' method='get'>
	<input type='text' name='title'>
	<input type='submit' value='title'>
</form>
</div><!--1/3-->
<div class="one-third column">
<form action='' method='get'>
	<input type='text' name='text'>
	<input type='submit' value='text'>
</form>
</div><!--1/3-->
</div><!--row-->
<?php echo $test; ?>
<table>
		<thead>
			<tr>
				<th>id</th>
				<!--<th>date</th>-->
				<th>title</th>
				<!--<th>text</th>-->
				<th>up</th>
				<!--<th>update</th>-->
				<!--<th>delete</th>-->
			</tr>
		</thead>
		<tbody>
			<?php echo $str; ?>
		</tbody>
</table>
			<?php echo $paging; ?>
<script>
	$(function(){

		$("#slideBtn").on("click",slide);	
		function slide(){
			$("#w_query").slideToggle();
		}
	});
</script>
