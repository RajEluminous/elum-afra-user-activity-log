  
<div class="wrap">
	 <h1>Latest Download Log</h1>
	<form method="post" action="options.php">
		 		 
		<table class="form-table cdDesktop" border="0">
			<tr><td colspan="8" align="right"><?php echo $page_pagination_nav; ?></td></tr>
			<tr style="background-color:#1D2F83;">
				<th style="color:#FFFFFF;text-align:center;font-size:11px;">#No</th>
				<th style="color:#FFFFFF;text-align:center;font-size:11px;">Username</th>
				<th style="color:#FFFFFF;text-align:center;width:10%;font-size:11px;">Product ID</th>
				<th style="color:#FFFFFF;text-align:left;font-size:11px;">Product Name</th>
				<th style="color:#FFFFFF;text-align:left;font-size:11px;">Item Code</th>
				<th style="color:#FFFFFF;text-align:left;font-size:11px;">Download Type</th>
				<th style="color:#FFFFFF;text-align:center;font-size:11px;">Download Date and Time</th>
				<th style="color:#FFFFFF;text-align:center;font-size:11px;">IP Address</th>	 
			</tr>
			<tbody id="rsUserTransactions">
			<?php echo $tblAllUsersTransaction; ?>
			</tbody>
			<tr><td colspan="8" align="right"><?php echo $page_pagination_nav; ?></td></tr>
		</table>			
		
	</form>
	</div>
  
 	
 <?php
 //echo do_shortcode( '[wp-datatable id="example" fat="LEVEL"]' );
 ?>