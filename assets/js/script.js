 
jQuery(document).ready(function($) {
	 console.log("btnLogUserSearch");	 
	jQuery('#btnLogUserSearch').click(function() { 
		cid = jQuery('#log_dp').val();
		console.log(cid);
		
		if(cid.length >=2) {
		window.location.href="https://afrafurniture.com/wp-admin/admin.php?page=eaufdl_user_activity_log&pdate="+cid;
	/* 	var ajaxurl=etvuds_ajax.ajaxurl; 
        var data ={ action: "city_action",  city:cid    };
		 
		//jQuery.post(ajaxurl, data, function (response){
		//
		//$('#key').html(response);
		//});
		
		 jQuery.ajax({
			type: "POST",
			url: etvuds_ajax.ajaxurl,
			cache: false,
			//dataType: "JSON",
			data: {  
				action: 'city_action_callback',
				city: cid	
			},
			success: function(response) { 
				console.log('---SUCCESS----');  
				jQuery('#key').html(response); 
			},
			error: function(response) { 
				console.log('---error----');
				console.log(response);
			},
		}); */ 
		}				
	});
	
	// for datetime picker
 
	 	
});
 

