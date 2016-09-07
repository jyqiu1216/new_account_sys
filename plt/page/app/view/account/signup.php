<?php $this->_extends('_layouts/default_layout'); ?>
<?php $this->_block('title'); ?>Promotion Setting<?php $this->_endblock(); ?>
<?php $this->_block('contents'); ?>

<table align="center" width="1000px">
	<tr>
		<td align="center"><b>LEYI account sign up</b></td>
	</tr>
	<tr>
		<td>
			<form method="post" action="<?php echo url("account/signup"); ?>">
				<table width="100%">
				<tr>
					<td><label>Email</label></td>
					<td><input type="text" value="" name="email"/></td>
				</tr>
				<tr>
					<td><label>Password</label></td>
					<td><input type="password" value="" name="password"/></td>
				</tr>
				<tr>
					<td><label>Confirm Password</label></td>
					<td><input type="password" value="" name="confirm_password"/></td>
				</tr>
				<tr>
					<td></td>
					<td align="center"><input type="submit" value="sign up" /></td>
				</tr>
				</table>
			</form>
		</td>
	</tr>
	<tr>
		<td align="center"><?php echo $signup_result; ?></td>
	</tr>
</table>
			

<?php $this->_endblock(); ?>


