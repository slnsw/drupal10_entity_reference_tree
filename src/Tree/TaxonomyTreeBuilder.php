<?php
namespace Drupal\entity_reference_tree\Tree;

use Drupal\Core\Session\AccountProxyInterface;



/**
 * Provides a class for building a tree from taxonomy entity.
 * 
 * @ingroup entity_reference_tree_api
 *
 * @see \Drupal\entity_reference_tree\Tree\TreeBuilderInterface
 */
class TaxonomyTreeBuilder implements TreeBuilderInterface {
  
  /**
   *
   * @var string $accessPermission
   *   The permission name to access the tree.
   */
   private $accessPermission = 'access taxonomy overview';
  
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
    if ($this->hasAccess()) {
      return \Drupal::entityTypeManager()->getStorage($entityType)->loadTree($bundleID, $parent, $max_depth);
    }
    // The user is not allowed to access taxonomy overviews.
    return NULL;
  }
  
  /**
   * Create a tree node.
   *
   * @param object $entity
   *   The entity for the tree node.
   *
   * @param array $selected
   *   A anrray for all selected nodes.
   *
   * @return array
   *   The tree node for the entity.
   */
  public function createTreeNode(object $entity, array $selected) {
    $parent = $entity->parents[0];
    
    if ($parent === '0') {
      $parent = '#';
    }
    
    $node = [
        'id' => $entity->tid,  // required
        'parent' => $parent, // required
        'text' => $entity->name, // node text
        'state' => ['selected' => false],
    ];
    
    if (in_array($entity->tid, $selected)) {
      // Initially selected node.
      $node['state']['selected'] = true;
    }
    
    return $node;
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
  
  /**
   * Check if a user has the access to the tree.
   *
   * @param AccountProxyInterface $user
   *   The user object to check.
   *
   * @return boolean
   *   If the user has the access to the tree return TRUE,
   *   otherwise return FALSE.
   */
  private function hasAccess(AccountProxyInterface $user = NULL) {
    // Check current user as default.
    if (empty($user)) {
      $user = \Drupal::currentUser();
    }
    
    return $user->hasPermission($this->accessPermission);
  }
}
