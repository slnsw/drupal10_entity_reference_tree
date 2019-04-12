<?php
namespace Drupal\entity_reference_tree\Tree;

use Drupal\Core\Entity\EntityInterface;


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
  public function loadTree(string $entityType, string $bundleID) {
    return \Drupal::entityTypeManager()->getStorage($entityType)->loadTree($bundleID);
  }
  
  /**
   * Create a tree node.
   *
   * @param EntityInterface $entity
   *   The entity for the tree node.
   *
   * @return array
   *   The tree node for the entity.
   */
  public function createTreeNode(EntityInterface $entity) {
    
  }
}