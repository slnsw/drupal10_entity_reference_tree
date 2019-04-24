/**
 * @file
 * Fullcalendar View plugin JavaScript file.
 */

(function($, Drupal) {
  Drupal.behaviors.entityReferenceTree = {
    attach: function(context, settings) {
    	if (undefined !== drupalSettings.entity_tree_items) {
    		// submitted entity ids.
      	$('#' + drupalSettings.entity_tree_items.field_id).val(drupalSettings.entity_tree_items.selected_entities);
    	}
    	$('#entity-reference-tree-wrapper', context).once('jstreeBehavior').each(function () {
    		var treeContainer = $(this);
    		
    	//	console.log($('#' + $('#entity-reference-tree-widget-field').val()).val().match(/\((\d+)\)/g));
    		$('#entity-reference-tree-selected-node').val($('#' + $('#entity-reference-tree-widget-field').val()).val());
    		
    		treeContainer.jstree({ 
      		'core' : {
      		  'data' : drupalSettings.tree_data,
          },
          "plugins" : [
            "search",
          ]
      	});
    		
    		// Selected event.
    		treeContainer.on(
            "select_node.jstree", function(evt, data){
              //selected node objects;
            	const selectedNodes = data.instance.get_selected(true);
            	var r = [], selectedText;
              for (var i = 0; i < selectedNodes.length; i++) {
                  r.push(selectedNodes[i].text + ' (' + selectedNodes[i].id + ')');
              }
              selectedText = r.join(', ');
              $('#entity-reference-tree-selected-node').val(selectedText);
              
          }
        );
      	
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