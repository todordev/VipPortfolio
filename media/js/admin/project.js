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
    	    url: 'index.php?option=com_vipportfolio&task=project.removeExtraImage&format=raw',
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
    	    		VipPortfolio.displayMessageSuccess(object.title, object.text);
				} else {
					VipPortfolio.displayMessageFailure(object.title, object.text);
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
					VipPortfolio.addExtraImage(image);
				}); 
	    		
				VipPortfolio.displayMessageSuccess(object.title, object.text);
			} else {
				VipPortfolio.displayMessageFailure(object.title, object.text);
			}
				
	      }
	    });
	
    }
    
});

var VipPortfolio = {
		
		displayMessageSuccess: function(title, text) {
			new Message({
				iconPath: '/media/com_vipportfolio/images/',
				icon: 'message_class_ok.png',
				title: title,
				message: text
			}).say();
		},
		displayMessageFailure: function(title, text) {
			new Message({
				iconPath: '/media/com_vipportfolio/images/',
				icon: 'message_class_warning.png',
				title: title,
				message: text
			}).say();
		},
		addExtraImage: function(image) {
			var imagesWrapper = document.id("itp-extra-images");
			
			// Image box
			var elDiv = new Element('div', {
			    "id": 'ai_box'+image.id,
			    "class": 'ai_box',
			});
			
			// Link
			var elA = new Element('a', {
			    "href": image.image,
			});

			// Thumbnail
			var elImg = new Element('img', {
			    "src": image.thumb,
			});
			
			// Icon for image removing
			var elImgRemove = new Element('img', {
			    "src": "../media/com_vipportfolio/images/icon_remove_16.png",
			    "class": "ai_ri",
			    "data-image-id": image.id
			});

			elImg.inject(elA);
			elA.inject(elDiv);
			elImgRemove.inject(elDiv);
			elDiv.inject(imagesWrapper);
		}
}

