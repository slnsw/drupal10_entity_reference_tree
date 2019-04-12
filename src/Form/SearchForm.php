<?php

namespace Drupal\entity_reference_tree\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\entity_reference_tree\Tree\TreeBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


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
  public function buildForm(array $form, FormStateInterface $form_state, $field_id = [], $bundles = [], $entity_type= '') {
    $bundlesAry = explode(',', $bundles);
    // Entitys array.
    $entityAry = [];
    $entityTrees = [];
    
    if (\Drupal::hasService('entity_reference_' . $entity_type . '_tree_builder')) {
      $treeBuilder = \Drupal::service('entity_reference_' . $entity_type . '_tree_builder');
    }
    else {
      return [];
    }
    
    foreach ($bundlesAry as $bundle_id) {
      $entityTrees[] = $treeBuilder->loadTree($entity_type, $bundle_id);
    }
    
    foreach ($entityTrees as $tree) {
      foreach ($tree as $entity) {
        $entityAry[$entity->tid] = $entity->name;
      }
    }
    
    $form['#prefix'] = '<div id="entity_reference_tree_wrapper">';
    $form['#suffix'] = '</div>';
    
    // The status messages that will contain any form errors.
    $form['status_messages'] = [
        '#type' => 'status_messages',
        '#weight' => -10,
    ];
    
    // A required checkbox field.
    $form['our_checkbox'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('I Agree: modal forms are awesome!'),
        '#required' => TRUE,
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
    
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    
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
      $response->addCommand(new CloseModalDialogCommand());
    }
    
    return $response;
  }
}