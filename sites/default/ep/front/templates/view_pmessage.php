
			<div class="table-responsive">
			<?php
			title('pmessages');
			if($_DB->scheme->result_count($res))
			{
			
			?>
			<table class="table table-striped">
			<tr>
			<th>id</th><th>title</th><th>content</th><th>from</th><th>to</th><th>date</th><th></th><th></th>
		</tr>
		<?php
			
			while($row=$_DB->scheme->res_row($res))
			{
			?>
			<tr><td><?php echo $row['id']; ?></td><td><?php echo $row['title']; ?></td><td><?php echo nl2br(truncstr($row['content'],30)); ?></td><td><?php echo template_parse($row,'{_from_login} '); ?></td><td><?php echo template_parse($row,'{_to_login} '); ?></td><td><?php echo $row['date']; ?></td><td><a href="<?php echo url("pmessages/edit/".$row['id']); ?>"><button type="button" class="btn btn-default">Edit</button></a></td><td><?php echo get_form('/crud/delete_pmessage',Array('ID'=>$row['id'])); ?></td>	</tr>
				<?php
		}
		?>
		</table>
		<?php
		}
		else
		{
		?>
		<h4>[t@No data]</h4>
		<?php
		}
		?>
		</div>
		<?php
		if($pcount>1)
		{
		?>
		<ul class="pagination">
		<?php
		for($p=1;$p<=$pcount;$p++)
		{
		$theclass='';
		if($p==$_QUERY['page'])
		$theclass=' class="active"';
		?>
		<li <?php echo $theclass; ?>><a href="<?php echo url("$_PAGE/page:$p"); ?>/"><?php echo $p; ?></a></li>
		<?php
		}
		?>
		</ul>
		<?php
		}
		/* VIEW TEMPLATE END */
		