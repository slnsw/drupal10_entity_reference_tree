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
   * @var string
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
   * @param $entity
   *   The entity for the tree node.
   *
   * @param array $selected
   *   A anrray for all selected nodes.
   *
   * @return array
   *   The tree node for the entity.
   */
  public function createTreeNode($entity, array $selected = []) {
    $parent = $entity->parents[0];

    if ($parent === '0') {
      $parent = '#';
    }

    $node = [
    // Required.
      'id' => $entity->tid,
    // Required.
      'parent' => $parent,
    // Node text.
      'text' => $entity->name,
      'state' => ['selected' => FALSE],
    ];

    if (in_array($entity->tid, $selected)) {
      // Initially selected node.
      $node['state']['selected'] = TRUE;
    }

    return $node;
  }

  /**
   * Get the ID of a tree node.
   *
   * @param $entity
   *   The entity for the tree node.
   *
   * @return string|int|null
   *   The id of the tree node for the entity.
   */
  public function getNodeId($entity) {
    return $entity->tid;
  }

  /**
   * Check if a user has the access to the tree.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $user
   *   The user object to check.
   *
   * @return bool
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
