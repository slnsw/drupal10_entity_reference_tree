/**
 * @file
 * Entity Reference Tree JavaScript file.
 */

// Codes run both on normal page loads and when data is loaded by AJAX (or BigPipe!)
// @See https://www.drupal.org/docs/8/api/javascript-api/javascript-api-overview
(function($, Drupal) {
  Drupal.behaviors.entityReferenceTree = {
    attach: function(context, settings) {
    	$('#entity-reference-tree-wrapper', context).once('jstreeBehavior').each(function () {
    		var treeContainer = $(this);
    		const fieldEditName =  $('#entity-reference-tree-widget-field').val();
    		const widgetElement = $('#' + fieldEditName);
    		const theme = treeContainer.attr('theme');
    		const dots = treeContainer.attr('dots');
    		// Avoid ajax callback from running following codes again. 
    		if (widgetElement.length) {
      		const entityType = $('#entity-reference-tree-entity-type').val();
      		const bundle = $('#entity-reference-tree-entity-bundle').val();
       		// Selected nodes.
      		var selectedNodes = widgetElement.val().match(/\((\d+)\)/g);
      		if (selectedNodes) {
      		// Pick up nodes id.
        		for (var i = 0; i < selectedNodes.length; i++) {
        			// Remove the round brackets.
        			selectedNodes[i] = parseInt(selectedNodes[i].slice(1, selectedNodes[i].length -1), 10);
        		}   
      		}
      		else {
      			selectedNodes = [];
      		}
      		// Populate the selected entities text.  
      		$('#entity-reference-tree-selected-node').val(widgetElement.val());
      		$('#entity-reference-tree-selected-text').text('Selected entities: ' + widgetElement.val());
      		// Build the tree.
      		treeContainer.jstree({ 
        		'core' : {
        			'data' : {
        		    'url' : function (node) {
        		      return "/admin/entity_reference_tree/json/" + entityType + '/' + bundle;
        		    },
        		    'data' : function (node) {
        		      return { 'id' : node.id, 'text': node.text, 'parent': node.parent, };
        		    }
        			},
        			'themes': {
        				'dots': dots === '1' ? true : false,
        				"name": theme,
        			}
            },
            "checkbox" : {
              "three_state" : false
            },
            "search" : {
            	"show_only_matches": true,
            },
            "plugins" : [
              "search",
              "changed",
              "checkbox",
            ]
        	});
      		// Initialize the selected node.
      		treeContainer.on("loaded.jstree", function (e, data) { data.instance.select_node(selectedNodes); })
      		// Selected event.
      		treeContainer.on(
              "changed.jstree", function(evt, data){
                //selected node objects;
              	const selectedNodes = data.selected;
              	var r = [], selectedText;
                for (var i = 0; i < selectedNodes.length; i++) {
                	var node = data.instance.get_node(selectedNodes[i]);
                  r.push(node.text + ' (' + node.id + ')');
                }
                selectedText = r.join(', ');
                $('#entity-reference-tree-selected-node').val(selectedText);
                $('#entity-reference-tree-selected-text').text('Selected entities: ' + selectedText);
                
            }
          );
        	// Search filter box.
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
    		}
    	});
    }
  }
})(jQuery, Drupal);

// Codes just run once the DOM has loaded.
// @See https://www.drupal.org/docs/8/api/javascript-api/javascript-api-overview
(function($)
		{
		  //argument passed from InvokeCommand
		  $.fn.entitySearchDialogAjaxCallback = function(field_edit_id, selected_entities)
		  {
		  	if ($('#' + field_edit_id).length) {
	    		// submitted entity ids.
		  		$('#' + field_edit_id).val(selected_entities);
	    	}
		  };
})(jQuery);