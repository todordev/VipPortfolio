window.addEvent('domready', function(){ 
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'project.cancel' || document.formvalidator.isValid(document.id('project-form'))) {
            Joomla.submitform(task, document.getElementById('project-form'));
        }
    };
    
    var extraImagesElement = document.id("itp-extra-images");
    if(extraImagesElement) {
    	
    	// Remove additional images 
        var myRequest = new Request({
    	    url: 'index.php?option=com_vipportfolio&task=project.removeExtraImage',
    	    method: "post",
    	    format: "raw",
    	    onRequest: function() {
    	    	document.id("ajax_loader").show();
    	    },
    	    onSuccess: function(responseText){
    	    	var object = JSON.decode(responseText);
    	    	document.id("ai_box"+object.data.item_id).destroy();
    	    	document.id("ajax_loader").hide();
    	    	
    	    	if(object.success) {
    	    		VipPortfolioHelper.displayMessageSuccess(object.title, object.text);
				} else {
					VipPortfolioHelper.displayMessageFailure(object.title, object.text);
				}
    	    }
    	});
        
	    // Add event to the icon for image deleting
        extraImagesElement.addEvent("click:relay(.ai_ri)", function(event) {
	    	event.preventDefault();
	    	var imageId = document.id(this).get("data-image-id");
	    	var data = "id="+imageId;
	    	myRequest.send(data);
	    });
	    
	    // Create the file uploader
	    var upload = new Form.Upload('file', {
	      dropMsg: Joomla.JText._('COM_VIPPORTFOLIO_DROP_FILE', 'Drop files here'),
	      onComplete: function(responseText){
	        
	    	    var object = JSON.decode(responseText);
				
				if(object.success) {
					
					Array.each(object.data, function(image, index){
						VipPortfolioHelper.addExtraImage(image);
					}); 
		    		
					VipPortfolioHelper.displayMessageSuccess(object.title, object.text);
				} else {
					VipPortfolioHelper.displayMessageFailure(object.title, object.text);
				}
				
	      }
	    });
    }
    
});

