<?php
namespace Drupal\entity_reference_tree\Tree;



/**
 * Provides a class for building a tree from taxonomy entity.
 * 
 * @ingroup entity_reference_tree_api
 *
 * @see \Drupal\entity_reference_tree\Tree\TreeBuilderInterface
 */
class TaxonomyTreeBuilder implements TreeBuilderInterface {
  
  /**
   * Load all entities from an entity bundle for the tree.
   *
   * @param string $entityType
   *   The type of the entity.
   *   
   * @param string $bundleID
   *   The bundle ID.
   *
   * @return array
   *   All entities in the entity bundle.
   */
  public function loadTree(string $entityType, string $bundleID, int $parent = 0, int $max_depth = NULL) {
    return \Drupal::entityTypeManager()->getStorage($entityType)->loadTree($bundleID, $parent, $max_depth);
  }
  
  /**
   * Create a tree node.
   *
   * @param object $entity
   *   The entity for the tree node.
   *
   * @return array
   *   The tree node for the entity.
   */
  public function createTreeNode(object $entity) {
    $parent = $entity->parents[0];
    
    if ($parent === '0') {
      $parent = '#';
    }
    
    return [
        'id' => $entity->tid,  // required
        'parent' => $parent, // required
        'text' => $entity->name, // node text
    ];
  }
  
  /**
   * Get the ID of a tree node.
   *
   * @param object $entity
   *   The entity for the tree node.
   *
   * @return string|int|null
   *   The id of the tree node for the entity.
   */
  public function getNodeID(object $entity) {
    return $entity->tid;
  }
}
