<?php 
/**
 * Plugin Name: WP Afra User Files Download Log
 * Plugin URI: https://eluminoustechnologies.com/
 * Description: This plugin monitors the files download activity of particular user.
 * Version: 1.0.0
 * Text Domain: elum-afra-user-activity-log
 * Author: Rajendra Mahajan
 * Author URI: https://eluminoustechnologies.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
  
 // Plugin directory url.
 define('EAUFDL_URL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
 
    /**
     * For development: To display error, set WP_DEBUG to true.
     * In production: set it to false
    */
 //define('WP_DEBUG',true);
 
 // Get absolute path 
 if ( !defined('EAUFDL_ABSPATH'))
    define('EAUFDL_ABSPATH', dirname(__FILE__) . '/');

 // Get absolute path 
if ( !defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');

/**
 *  Current plugin version.
 */
 if ( ! defined( 'EAUFDL_VER' ) ) {
	define( 'EAUFDL_VER', '1.0.0' );
 }
  
  
 
 define('EAUFDL_TEMPLATES',EAUFDL_ABSPATH.'templates');
 define('EAUFDL_PAGE_TITLE','Afra User Activity Log');
 define('EAUFDL_PAGE_TITLE_USER_LOGIN_HISTORY','User Login History');
 define('EAUFDL_PAGE_TITLE_ALLUSER_TRANSACTIONS','All Users Transfer Asset Transactions');
 define('EAUFDL_PAGE_TITLE_COMPANY_DISCOUNTS','All Company Discounts');
 
add_action( 'template_redirect', 'wpse_inspect_page_id' );
function wpse_inspect_page_id() {
    $page_object = get_queried_object();
   
    $page_id = get_queried_object_id();
    //echo $page_id;	
}

 // Main Class
 class ElumAfraUserActivityLog {
	
	var $eaufdl_page_menu;
	 
	// for user info
	var $eaufdl_uid;
	var $eaufdl_user_login;
	var $eaufdl_display_name;
	var $eaufdl_user_email;
	 		
	function __construct() {	
		global $wpdb;
		global $wp;
        
		if(isset($_GET['page']) && ($_GET['page']=='eaufdl_user_activity_log' || $_GET['page'] =='eaufdl_latest_download_log')) {
				 
		wp_enqueue_style('eaufdl_css_datep', EAUFDL_URL.'/assets/css/style.css');
		wp_enqueue_script('eaufdl_js3', EAUFDL_URL.'/assets/js/script.js');	
		}
		add_shortcode( 'elm_all_cmpny_discount', array($this,'eaufdl_admin_all_company_discount'));
		 
		//Initial loading				 		 
		add_action('admin_init',array($this,'init'),0);	 
		add_action('admin_menu', array($this, 'eaufdl_admin_menu')); 		
		add_filter( 'submenu_file', 'so3902760_wp_admin_submenu_filter' );	
	}	
	
	function so3902760_wp_admin_submenu_filter( $submenu_file ) {

		global $plugin_page;

		$hidden_submenus = array(
			'my_hidden_submenu' => true,
		);

		// Select another submenu item to highlight (optional).
		if ( $plugin_page && isset( $hidden_submenus[ $plugin_page ] ) ) {
			$submenu_file = 'submenu_to_highlight';
		}

		// Hide the submenu.
		foreach ( $hidden_submenus as $submenu => $unused ) {
			remove_submenu_page( 'my_parent_slug', $submenu );
		}

		return $submenu_file;
	}
	
	function wps_pre_user_query( &$query){
		global $wpdb;
		//print_r($query);
		 if(isset($_GET['cmp']) &&  isset( $query->query_vars['orderby'])) { 
			 $query->query_orderby = str_replace( 'user_login', "$wpdb->usermeta.meta_value+0", $query->query_orderby );
		 }
	}
	
	// initial processing	
	function init() {
		// if session is not start, then start it.
		if(!session_id()) {
			session_start();
		} 
		$this->load(); 		
	} 
	
	function load() {
		 
	}
		 
	// add menu to admin
	function eaufdl_admin_menu() {	  
	  add_menu_page('Afra User Activity Log','Afra User Activity Log','administrator', 'eaufdl_user_activity_log',array($this,'eaufdl_admin_all_company_addresses'),'',100);	   
	  
	     // Submenu for latest download log
	  add_submenu_page('eaufdl_user_activity_log', 'Lastest Download Log', 'Lastest Download Log', 'manage_options', 'eaufdl_latest_download_log' ,array($this,'eaufdl_latest_download_log'));   
	  
    }
	
	// To show latest download log .
	function eaufdl_latest_download_log() {		
		 
		global $wpdb;	
	   $table_name = $wpdb->prefix.'afra_user_activity';
	   
	   
	    $the_user = get_user_by( 'id', $uid ); // 54 is a user ID
		$finalUserName = $the_user->first_name.' '.$the_user->last_name;
	   
	   // Code to display all transaction	
	   $rVal_main = $wpdb->get_results ("SELECT * FROM  $table_name order by id DESC");	    
	    		
		//----- for pagination ---------//
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;      

        $limit = 8; // number of rows in page
        $offset = ( $pagenum - 1 ) * $limit;
        $total = count($rVal_main);
        $num_of_pages = ceil( $total / $limit );
		//---------- end pagination -------//
		 
		$rVal = $wpdb->get_results ("SELECT * FROM  $table_name order by id DESC limit $offset,$limit");
		
		 
		
		$etvud_counter = 1;
		$arrAllVals = array();
		$tblUserTransaction = '';
		if(count($rVal)>0) { 
			foreach ( $rVal as $rsVal ) {
			$postTitle = '';
			$getItemCode = '';
			$downloadtype =  '';
			$ip_address = '';
			$postdate = '';
			// to get user id
			 if($rsVal->pid>0) {	
				 $content_post = get_post($rsVal->pid);
				 $postTitle = $content_post->post_title;
				 
				 $getItemCode = get_post_meta( $rsVal->pid, '_item_code', TRUE );
				 $downloadtype = $this->getDownloadType($rsVal->downloadtype); 
				 $ip_address = $rsVal->ip_address;
				 $postdate = $rsVal->postdate;  
			} 
			 
			$the_user = get_user_by( 'id', $rsVal->uid ); // 54 is a user ID
			$finalUserName = $the_user->first_name.' '.$the_user->last_name; 
			 
			 
			$tblStyle = '';
			if($etvud_counter%2==0){
				$tblStyle = 'style="background-color: #EFEFEF"'; // 	
			} 	
			
			$srNo = $offset+$etvud_counter;
			
			$tblAllUsersTransaction .= "<tr $tblStyle>
										<td style='text-align:center'>".$srNo."</td>	
										<td>".$finalUserName."</td>	
										<td>".$rsVal->pid."</td>
										<td>".$postTitle."</td>
										<td>".$getItemCode."</td>
										<td>".$downloadtype."</td>
										<td style='font-size:11px;;text-align:center'>".$postdate."</td>
										<td style='font-size:11px;;text-align:center'>".$ip_address."</td>	 
									</tr>"; 
			$etvud_counter++;								
		   }	  
		} else {
			$tblAllUsersTransaction = "<tr><td colspan='8' style='text-align:center'> No records found</td></tr>";
		}
		
	    $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ));
		
		$page_pagination_nav = "";
        if ( $page_links ) {
            $page_pagination_nav = '<div class="tablenav" style="width: 99%; float:right"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }	
		 		
	    require_once(EAUFDL_TEMPLATES . '/latest_download_log_template.php'); 
		 
		 
	}	
	
	// for displaying ALL company's addresses
	function eaufdl_admin_all_company_addresses(){	
	   global $wpdb;	
	   
	   if($_GET['uid']>0) {
		    $this->mainUserListing(); 
	   }
	   else if($_GET['uaid']>0) {
		    $this->mainUserActivityListing(); 
	   }	
	   else {
		   $this->allUserListing();
	   }
	   
	}
	
	function getDownloadType($typ) {
		
		switch($typ) {
			
			case "SD": $strType = 'Shop Drawing';
			break;
			case "3D": $strType = '3D Model';
			break;
			case "SS": $strType = 'Spec Sheet';
			break;
			case "PL": $strType = 'Pricelist';
			break;
			default:
			 $strType = 'N/A';
		}	
		return $strType;
	}
	
	// Individual user page visit activit log
	function mainUserActivityListing() {
	   global $wpdb;	
	   $table_name = $wpdb->prefix.'afra_user_visit_activity';
	   $uid = $_GET['uaid'];
	   
	    $the_user = get_user_by( 'id', $uid ); // 54 is a user ID
		$finalUserName = $the_user->first_name.' '.$the_user->last_name;
	   
	   // Code to display all transaction	
	   $rVal_main = $wpdb->get_results ("SELECT * FROM  $table_name where uid=$uid order by id DESC");	    
	    		
		//----- for pagination ---------//
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;      

        $limit = 25; // number of rows in page
        $offset = ( $pagenum - 1 ) * $limit;
        $total = count($rVal_main);
        $num_of_pages = ceil( $total / $limit );
		//---------- end pagination -------//
		 
		$rVal = $wpdb->get_results ("SELECT * FROM  $table_name where uid=$uid order by id DESC limit $offset,$limit");
		 
		$etvud_counter = 1;
		$arrAllVals = array();
		$tblUserTransaction = '';
		if(count($rVal)>0) { 
			foreach ( $rVal as $rsVal ) {
			$postTitle = '';
			$getItemCode = '';
			$downloadtype =  '';
			$ip_address = '';
			$postdate = '';
			$extraInfo = '-';
			$isUserLogggedin = "<span style=color:#FF2020>No</span>";
			// to get user id
			 if($rsVal->uid>0) {					 
				$postTitle = $rsVal->pageinfo; 				 
				$ip_address = $rsVal->ip_address;
				//$postdate = $rsVal->postdate;	
				
				 $pDateTime = new DateTime($rsVal->postdate, new DateTimeZone('MST'));
				 $pDateTime->setTimezone(new DateTimeZone('America/Montreal'));
				 $postdate = $pDateTime->format('M j, Y, g:i a');
				
				
				if($rsVal->isUserLogin==1) {
					$isUserLogggedin = "<span style=color:#47AD03>Yes</span>";
				} 
			} 
			 
			$tblStyle = '';
			if($etvud_counter%2==0){
				$tblStyle = 'style="background-color: #EFEFEF"'; // 	
			} 	
			$srNo = $offset+$etvud_counter; 
			$tblAllUsersTransaction .= "<tr $tblStyle>
										<td style='text-align:center;'>".$srNo."</td>
										<td>".$postTitle."</td> 
										<td>".$isUserLogggedin."</td> 
										<td style='text-align:center'>".$postdate."</td>
										<td style='text-align:center'>".$ip_address."</td>	 
									</tr>"; 
			$etvud_counter++;								
		   }	  
		} else {
			$tblAllUsersTransaction = "<tr><td colspan='5' style='text-align:center'> No records found</td></tr>";
		}
		
	    $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ));
		
		$page_pagination_nav = "";
        if ( $page_links ) {
            $page_pagination_nav = '<div class="tablenav" style="width: 99%; float:right"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }	
		 		
	    require_once(EAUFDL_TEMPLATES . '/user_pagevisit_activity_log_template.php');		
	}
	
	// Individual user download activit log
	function mainUserListing() {
	   global $wpdb;	
	   $table_name = $wpdb->prefix.'afra_user_activity';
	   $uid = $_GET['uid'];
	   
	    $the_user = get_user_by( 'id', $uid ); // 54 is a user ID
		$finalUserName = $the_user->first_name.' '.$the_user->last_name;
	   
	    // Code to display all transaction	
	    $rVal_main = $wpdb->get_results ("SELECT * FROM  $table_name where uid=$uid order by id DESC");	    
	    		
		//----- for pagination ---------//
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;      

        $limit = 5; // number of rows in page
        $offset = ( $pagenum - 1 ) * $limit;
        $total = count($rVal_main);
        $num_of_pages = ceil( $total / $limit );
		//---------- end pagination -------//
		 
		$rVal = $wpdb->get_results ("SELECT * FROM  $table_name where uid=$uid order by id DESC limit $offset,$limit");
		 
		$etvud_counter = 0;
		$arrAllVals = array();
		$tblUserTransaction = '';
		if(count($rVal)>0) { 
			foreach ( $rVal as $rsVal ) {
			$postTitle = '';
			$getItemCode = '';
			$downloadtype =  '';
			$ip_address = '';
			$postdate = '';
			$extraInfo = '-';
			// to get user id
			 if($rsVal->pid>0) {	
				 $content_post = get_post($rsVal->pid);
				 $postTitle = $content_post->post_title;
				 
				 $getItemCode = get_post_meta( $rsVal->pid, '_item_code', TRUE );
				 $downloadtype = $this->getDownloadType($rsVal->downloadtype); 
				 $ip_address = $rsVal->ip_address;
				 //$postdate = $rsVal->postdate;
				 
				 $pDateTime = new DateTime($rsVal->postdate, new DateTimeZone('MST'));
				 $pDateTime->setTimezone(new DateTimeZone('America/Montreal'));
				 $postdate = $pDateTime->format('M j, Y, g:i a');
				 
				 
				 // in case of Shop Drawing show the Project Name
				 if($rsVal->downloadtype=='SD' && !empty($rsVal->custom_data)) {
					$extraInfo =  $rsVal->custom_data;
				 }								 
			} 
			 
			$tblStyle = '';
			if($etvud_counter%2==0){
				$tblStyle = 'style="background-color: #EFEFEF"'; // 	
			} 	
			 
			$tblAllUsersTransaction .= "<tr $tblStyle>
										<td>".$rsVal->pid."</td>
										<td>".$postTitle."</td>
										<td>".$getItemCode."</td>
										<td>".$downloadtype."</td>  
										<td>".$extraInfo."</td>
										<td style='text-align:center'>".$postdate."</td>
										<td style='text-align:center'>".$ip_address."</td>	 
									</tr>"; 
			$etvud_counter++;								
		   }	  
		} else {
			$tblAllUsersTransaction = "<tr><td colspan='7' style='text-align:center'> No records found</td></tr>";
		}
		
	    $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ));
		
		$page_pagination_nav = "";
        if ( $page_links ) {
            $page_pagination_nav = '<div class="tablenav" style="width: 99%; float:right"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }	
		 		
	    require_once(EAUFDL_TEMPLATES . '/user_activity_log_template.php');		
	}
	
	// to Display all user listing.
	function  allUserListing(){
		global $wpdb; 
	    $eSOrdrBy = 'ID';
	    $eSOrdr = 'DESC';
	    $tempIP = elum_getRealIpAddr();		 
		$pDate = '';
		if(isset($_GET['pdate'])) {
			$pDate = date('Y-m-d', strtotime($_GET['pdate']));
		}
		$table_name = $wpdb->prefix.'afra_user_visit_activity';		
		if(!empty($pDate))	
		  $users_count = $wpdb->get_results ("SELECT * FROM  `$table_name` WHERE  DATE(postdate)='$pDate' GROUP BY uid");
	    else 
		  $users_count = $wpdb->get_results ("SELECT * FROM  `$table_name` GROUP BY uid");		
		
		
	    //----- for pagination ---------//
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;      
		
        $limit = 20; // number of rows in page
        $offset = ( $pagenum - 1 ) * $limit;
        $total = count($users_count);
        $num_of_pages = ceil( $total / $limit );
		//---------- end pagination -------//
		
		if(!empty($pDate)) {			 
			$users = $wpdb->get_results ("SELECT *,TIMEDIFF(max(postdate) , min(postdate)) as duration FROM `$table_name` WHERE DATE(postdate)='$pDate' ORDER BY postdate DESC limit $offset,$limit");	
		} else {
			$users = $wpdb->get_results ("SELECT tt.* FROM `$table_name` tt INNER JOIN (SELECT uid, MAX(postdate) AS MaxDateTime FROM `$table_name` GROUP BY uid) groupedtt ON tt.uid = groupedtt.uid AND tt.postdate = groupedtt.MaxDateTime ORDER BY tt.postdate DESC
 limit $offset,$limit");	
		}
								  
		// echo $wpdb->last_query;	
		
		foreach($users as $user_id){
			$usr_meta = get_user_meta ($user_id->uid);
			//echo '<pre>';
			//print_r($user_id);
		 
			$downldCnt = "";
			$downloadCount = $this->postviews_get_ip($user_id->uid);
			if($downloadCount>0) {
				$downldCnt = "($downloadCount)";	
			}
		  
			$pDateTime = new DateTime($user_id->postdate, new DateTimeZone('MST'));
			$pDateTime->setTimezone(new DateTimeZone('America/Montreal'));
			$postDateTime = $pDateTime->format('M j, Y, g:i a');
		  
			$arrUserMeta[$user_id->uid]['id'] = $user_id->uid; 		
			$arrUserMeta[$user_id->uid]['nickname'] =@current($usr_meta['nickname']);
			$arrUserMeta[$user_id->uid]['first_name'] =@current($usr_meta['first_name']);
			$arrUserMeta[$user_id->uid]['last_name'] = @current($usr_meta['last_name']);
			$arrUserMeta[$user_id->uid]['user_company_name'] = @current($usr_meta['user_company_name']);
			$arrUserMeta[$user_id->uid]['user_states_text'] = @current($usr_meta['user_states_text']);
			$arrUserMeta[$user_id->uid]['user_city'] = @current($usr_meta['user_city']);
			$arrUserMeta[$user_id->uid]['last_page_visit'] = $postDateTime;
			$arrUserMeta[$user_id->uid]['downloadCount'] = $downldCnt;
		} 
		 
		
		 $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum 
        ) );
		$page_pagination_nav = "";
        if ( $page_links ) {
            $page_pagination_nav = '<div class="tablenav" style="width: 99%; float:right"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }
		  
		
	   require_once(EAUFDL_TEMPLATES . '/all_user_activity_log_template.php');
		
	}
	
	function postviews_get_ip($uid) {
		global $wpdb;
		$table_name = $wpdb->prefix.'afra_user_activity';		 
		$ipquery= $wpdb->get_results("SELECT * FROM `$table_name` WHERE uid = $uid");		 
		return count($ipquery);
	}
	 
	
	// Function to get username
	function eaufdl_getUserNameInfo($uid){
		
		$uname = '';
		if($uid==0){
			$uname = 'EHL';
		}
		else if($uid>0) {	
			 $uinfo = get_userdata($uid);
			  $uname = ucfirst($uinfo->user_login);
		}	
		return $uname;
    }	 
	 	
 
	
	
 } // Classe
 
 // Call class
 new ElumAfraUserActivityLog();
 
?>