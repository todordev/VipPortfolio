/* class */
var trashCan = new Class({
  
  //implements
  Implements: [Options,Events],
  
  //options
  options: {
    trashCan: document.id('trash'),
    trashables: $$('.trashable'),
    taskUrl: ""
  },
  
  //initialization
  initialize: function(options) {
    //set options
    this.setOptions(options);
    //prevent def
    document.ondragstart = function() { return false; };
    //drag/drop
    $$('.trashable').each(function(drag) {
      new Drag.Move(drag, {
        droppables: this.options.trashCan,
        taskUrl: this.options.taskUrl,
        onDrop: function(el,droppable) {
          if(droppable) {
        	el.preventDefault = true;
        	var myRequest = new Request({
        		url: this.options.taskUrl,
        		onSuccess: function(responseText) {
        			
        			var object = JSON.decode(responseText);
        			
        			if(object.success) {
        				var icon = 'message_class_ok.png';
        			} else {
        				var icon = 'message_class_warning.png';
        			}
        			new Message({
        				iconPath: '/media/com_vipportfolio/images/',
        				icon: icon,
        				title: object.title,
        				message: object.text
        			}).say();
        			
        		}
    		});
        	var uri = 'id=' + el.id;
        	myRequest.post(uri);
            drag.dispose();
          }
        },
        onEnter: function(el,droppable) {
          
        },
        onLeave: function(el,droppable) {
          
        }
      });
    }.bind(this));
  }
});