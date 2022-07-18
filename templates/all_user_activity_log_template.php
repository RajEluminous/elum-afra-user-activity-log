  
<div class="wrap">
	<h1>&nbsp;</h1>
	<h1><?php echo EAUFDL_PAGE_TITLE; ?>&nbsp;|&nbsp;<a href="https://afrafurniture.com/wp-admin/admin.php?page=faulh-admin-listing"><?php echo EAUFDL_PAGE_TITLE_USER_LOGIN_HISTORY; ?></a></h1> 
	<form method="post" action="options.php">
		 		 
		<table class="form-table cdDesktop" border="0">
			<tr><td colspan="2">
			<?php if($tempIP=='49.248.144.234') {?> 
				<input type="text" id="log_dp" />&nbsp;
				<input type="button" name="btnLogUserSearch" id="btnLogUserSearch" value="Search"> 
			<?php } ?></td>
			<td colspan="6" align="right"><?php echo $page_pagination_nav; ?></td></tr>
			<tr style="background-color:#1D2F83;">
				<th style="color:#FFFFFF;text-align:center;width:10%">ID</th> 
				<th style="color:#FFFFFF;text-align:left;">User Name</th>
				<th style="color:#FFFFFF;text-align:left;">Company</th>
				<th style="color:#FFFFFF;text-align:left;">State</th>
				<th style="color:#FFFFFF;text-align:left;">City</th>
				<th style="color:#FFFFFF;text-align:center;width:15%">Last Visit Date</th>
				<th style="color:#FFFFFF;text-align:center">View Activity Log</th>
				<th style="color:#FFFFFF;text-align:center">View Download Log</th>						 
			</tr>
			<?php
				$etvud_counter = 1;
				foreach($arrUserMeta as $usrObj) {  
				$tblStyle = '';
				if($etvud_counter%2==0){
					$tblStyle = 'style="background-color: #EEF1FB"'; // 	
				} 
					 
			?>			 
			<tr <?php echo $tblStyle;?>>
				<td style="text-align:center"><?php echo $usrObj['id']; ?> </td> 
				<td><?php echo ucfirst($usrObj['first_name']).' '.ucfirst($usrObj['last_name']); ?> </td>
				<td><?php echo ucfirst($usrObj['user_company_name']); ?> </td>
				<td><?php echo ucfirst($usrObj['user_states_text']); ?> </td>	 
				<td><?php echo ucfirst($usrObj['user_city']); ?> </td>	
				<td><?php echo ucfirst($usrObj['last_page_visit']); ?> </td>	
				<td style="text-align:center;"><a target="_blank" href="<?php echo 'admin.php?page='.$_GET['page'].'&uaid='.$usrObj['id'];?>" >Activity log</a></td>
				<td style="padding-left:25px;"><a target="_blank" href="<?php echo 'admin.php?page='.$_GET['page'].'&uid='.$usrObj['id'];?>" >Download log</a> <?php echo $usrObj['downloadCount'];?></td>		
				
			</tr> 
			<?php $etvud_counter++; }   ?> 
			<tr><td colspan="8" align="right"><?php echo $page_pagination_nav; ?></td></tr>
		</table>			
		
	</form>
	</div>
  
 	
 <?php
 //echo do_shortcode( '[wp-datatable id="example" fat="LEVEL"]' );
 ?>