<?php

namespace Drupal\entity_reference_tree\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\SettingsCommand;


/**
 * ModalForm class.
 * 
 * To properly inject services, override create() and use the setters provided
 * by the traits to inject the needed services.
 *
 * @code
 * public static function create($container) {
 *   $form = new static();
 *   // In this example we only need string translation so we use the
 *   // setStringTranslation() method provided by StringTranslationTrait.
 *   $form->setStringTranslation($container->get('string_translation'));
 *   return $form;
 * }
 * @endcode
 */
class SearchForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_reference_tree_search_form';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $field_edit_id = [], $bundles = [], $entity_type= '', $selected = '') {
    $bundlesAry = explode(',', $bundles);
    $selectedAry = explode(',', $selected);
    // Entitys array.
    $entityAry = [];
    $entityTrees = [];
    
    // Instance a entity tree builder for this entity type if it exists.
    if (\Drupal::hasService('entity_reference_' . $entity_type . '_tree_builder')) {
      $treeBuilder = \Drupal::service('entity_reference_' . $entity_type . '_tree_builder');
    }
    else {
      // Todo: A basic entity tree builder.
      return [];
    }
    
    // The status messages that will contain any form errors.
    $form['status_messages'] = [
        '#type' => 'status_messages',
        '#weight' => -10,
    ];
    
    // Selectd entity text.
    $form['selected_node'] = [
        '#type' => 'textfield',
        '#title' => $this
          ->t('Selected'),
        '#attributes' => [
            'class' => [
                'selected-entities-text',
            ],
            'id' => [
                'entity-reference-tree-selected-node',
            ],
            'readonly' => ['true'],
        ],
        '#weight' => 1000,
        '#size' => 160,
    ];
    
      foreach ($bundlesAry as $bundle_id) {
        $tree = $treeBuilder->loadTree($entity_type, $bundle_id);
        if (!empty($tree)) {
          $entityTrees[] = $tree;
        }
      }
      
      if (empty($entityTrees)) {
        $form['selected_node']['#value'] = $this->t('No tree access!');
      }
      else {
        foreach ($entityTrees as $tree) {
          foreach ($tree as $entity) {
            // Create tree node for each entity.
            // Store them into an array passed to JS.
            // An array in JavaScript is indexed list.
            // JavaScript's array indices are always sequential
            // and start from 0.
            $entityAry[] = $treeBuilder->createTreeNode($entity, $selectedAry);
          }
        }
        
        $form['tree_search'] = [
            '#type' => 'textfield',
            '#title' => $this
            ->t('Search'),
            '#size' => 60,
            '#attributes' => [
                'id' => [
                    'entity-reference-tree-search',
                ],
            ],
        ];
        
        $form['tree_container'] = [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => [
                'id' => [
                    'entity-reference-tree-wrapper',
                ],
            ],
        ];
        
        $form['actions'] = array('#type' => 'actions');
        $form['actions']['send'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit modal form'),
            '#attributes' => [
                'class' => [
                    'use-ajax',
                ],
            ],
            '#ajax' => [
                'callback' => [$this, 'submitForm'],
                'event' => 'click',
            ],
        ];
        
        $form['#attached']['library'][] = 'entity_reference_tree/jstree';
        $form['#attached']['library'][] = 'entity_reference_tree/entity_tree';
        // Pass data to js file.
        $form['#attached']['drupalSettings'] = [
            'tree_data' => $entityAry,
        ];
        
        $form['field_id'] = array(
            '#name' => 'field_id',
            '#type' => 'hidden',
            '#weight' => 80,
            '#value' => $field_edit_id,
            '#attributes' => [
                'id' => [
                    'entity-reference-tree-widget-field',
                ],
            ],
        );
      }
    
    return $form;
  }
  
  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function submitModalFormAjax(array $form, FormStateInterface $form_state) {
  
  }
  
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    
    // If there are any form errors, re-display the form.
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#entity_reference_tree_wrapper', $form));
    }
    else {
      $response->addCommand(new SettingsCommand(['entity_tree_items' => ['field_id' => $form_state->getValue('field_id'), 'selected_entities' => $form_state->getValue('selected_node')]], TRUE));
      $response->addCommand(new CloseModalDialogCommand());
    }
    
    return $response;
  }
}