  
<div class="wrap">
	 <h1><a href="<?php echo 'admin.php?page='.$_GET['page'];?>">User Download Activity</a>: <?php echo $finalUserName; ?></h1>
	<form method="post" action="options.php">
		 		 
		<table class="form-table cdDesktop" border="0">
			<tr><td colspan="7" align="right"><?php echo $page_pagination_nav; ?></td></tr>
			<tr style="background-color:#1D2F83;">
				<th style="color:#FFFFFF;text-align:center;width:10%">Product ID</th>
				<th style="color:#FFFFFF;text-align:left;">Product Name</th>
				<th style="color:#FFFFFF;text-align:left;">Item Code</th>
				<th style="color:#FFFFFF;text-align:left;">Download Type</th>
				<th style="color:#FFFFFF;text-align:left;">Project Name</th>
				<th style="color:#FFFFFF;text-align:center">Download Date and Time</th>
				<th style="color:#FFFFFF;text-align:center">IP Address</th>	 
			</tr>
			<tbody id="rsUserTransactions">
			<?php echo $tblAllUsersTransaction; ?>
			</tbody>
			<tr><td colspan="7" align="right"><?php echo $page_pagination_nav; ?></td></tr>
		</table>			
		
	</form>
	</div>
  
 	
 <?php
 //echo do_shortcode( '[wp-datatable id="example" fat="LEVEL"]' );
 ?>