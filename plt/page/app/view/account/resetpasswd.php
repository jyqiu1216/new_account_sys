<?php $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>Promotion Setting<?php $this->_endblock(); ?>
<?php $this->_block('contents'); ?>

<?php

	if(0 == $show_flag)
	{
?>		
	<table align="center" width="1000px">
		<tr>
			<td align="center"><b>LEYI account passsword reset</b></td>
		</tr>
		<tr>
			<td>
				<form method="post" action="<?php echo url("account/resetpasswd"); ?>">
					<table width="100%">
					<tr>
						<td><label>Email</label></td>
						<td><?php echo $email; ?></td>
						<input type="hidden" value="<?php echo $email; ?>" name="email"/>
						<input type="hidden" value="<?php echo $seq; ?>" name="seq"/>
						<input type="hidden" value="<?php echo $pid; ?>" name="pid"/>
						<input type="hidden" value="<?php echo $game_platform; ?>" name="game_platform"/>
					</tr>
					<tr>
						<td><label>Password</label></td>
						<td><input type="password" value="" name="password"/></td>
					</tr>
					<tr>
						<td></td>
						<td align="center"><input type="submit" value="reset" /></td>
					</tr>
					</table>
				</form>
			</td>
		</tr>
		<tr>
			<td align="center"><?php echo $resetpasswd_result; ?></td>
		</tr>
	</table>

<?php
	}
	else
	{
		echo $resetpasswd_result;
	}
?>



<?php $this->_endblock(); ?>


