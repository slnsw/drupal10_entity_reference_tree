<?php
namespace Drupal\entity_reference_tree\Tree;

use Drupal\Core\Entity\EntityInterface;

/**
 * Provides an interface for a tree builder.
 *
 * @ingroup entity_reference_tree_api
 */
interface TreeBuilderInterface {
  
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
  public function loadTree(string $entityType, string $bundleID);
  
  /**
   * Create a tree node.
   *
   * @param EntityInterface $entity
   *   The entity for the tree node.
   *
   * @return array
   *   The tree node for the entity.
   */
  public function createTreeNode(EntityInterface $entity);
  
}