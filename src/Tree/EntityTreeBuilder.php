<?php
namespace Drupal\entity_reference_tree\Tree;

use Drupal\Core\Session\AccountProxyInterface;



/**
 * Provides a class for building a tree from general entity.
 * 
 * @ingroup entity_reference_tree_api
 *
 * @see \Drupal\entity_reference_tree\Tree\TreeBuilderInterface
 */
class EntityTreeBuilder implements TreeBuilderInterface {
  
  /**
   *
   * @var string $accessPermission
   *   The permission name to access the tree.
   */
   private $accessPermission = 'access content';
  
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
      $tree = [
       (object)[
         'id' => $bundleID,
         'parent' => '#', // required
         'text' => $bundleID, // node text
       ],
      ];
      
      // Load the next release node.
      $eids = \Drupal::entityQuery($entityType)
      ->condition('type', $bundleID)
      ->execute();
      // No entity found.
      if (empty($eids)) {
        return $tree;
      }
      
      // Load all entities matched the conditions.
      $entities = \Drupal::entityTypeManager()->getStorage($entityType)->loadMultiple($eids);
      // Find the node whose release date is just next to the current one.
      foreach ($entities as $entity) {
        $tree[] =  (object)[
            'id' => $entity->id(),
            'parent' => $entity->bundle(), // required
            'text' => $entity->label(), // node text
        ];
      }
      
      return $tree;
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
  public function createTreeNode(object $entity, array $selected = []) {   
    
    $node = [
        'id' => $entity->id,  // required
        'parent' => $entity->parent, // required
        'text' => $entity->text, // node text
        'state' => ['selected' => false],
    ];
    
    if (in_array($entity->id, $selected)) {
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
    return $entity->id;
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
