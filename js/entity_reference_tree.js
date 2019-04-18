/**
 * @file
 * Fullcalendar View plugin JavaScript file.
 */

(function($, Drupal) {
  Drupal.behaviors.entityReferenceTree = {
    attach: function(context, settings) {
    	$('#entity-reference-tree-wrapper', context).once('jstreeBehavior').each(function () {
    		var treeContainer = $(this);
    		treeContainer.jstree({ 
      		'core' : {
      		  'data' : drupalSettings.tree_data,
          },
          "plugins" : [
            "contextmenu", "dnd", "search",
            "state", "types", "wholerow"
          ]
      	});
      	
      	 var to = false;
         $('#entity-reference-tree-search').keyup(function () {
        	 var searchInput = $(this)
           if(to) {
          	 clearTimeout(to); 
           }
           to = setTimeout(
          		 function () {
                 var v = searchInput.val();
                 treeContainer.
                 jstree(true).
                 search(v);
               },
               250);
           });
    	});
    }
  }
})(jQuery, Drupal);