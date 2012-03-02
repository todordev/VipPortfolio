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
        	var myRequest = new Request({url: this.options.taskUrl});
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