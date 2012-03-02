window.addEvent('domready', function(){ 
            
    /* usage */
    var trash = new trashCan({
      trashCan: $('trash'),
      trashables: $$('.trashable'),
      taskUrl: '" . JRoute::_("index.php?option=com_vipportfolio&task=project.removeExtraImage&format=raw", false) . "'
    });
    
});