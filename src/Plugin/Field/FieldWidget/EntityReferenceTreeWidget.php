<?php

namespace Drupal\entity_reference_tree\Plugin\Field\FieldWidget;

use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;

/**
 * A entity reference tree widget.
 *
 * @FieldWidget(
 *   id = "entity_reference_tree",
 *   label = @Translation("Entity reference tree widget"),
 *   field_types = {
 *     "entity_reference",
 *   },
 *   multiple_values = TRUE
 * )
 */
class EntityReferenceTreeWidget extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $arr_element = parent::formElement($items, $delta, $element, $form, $form_state);

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'entity_reference_tree/widget';

    $arr_target = $arr_element['target_id']['#selection_settings']['target_bundles'];

    $str_target = implode(',', $arr_target);
    $str_target_type = $arr_element['target_id']['#target_type'];
    $edit_id = 'edit-' . str_replace('_', '-', $items->getName()) . '-target-id';

    $arr_element['target_id']['#id'] = $edit_id;
    $arr_element['target_id']['#tags'] = TRUE;
    $arr_element['target_id']['#default_value'] = $items->referencedEntities();
    
    $arr_element['dialog_link'] =  [
        '#type' => 'link',
        '#title' => $this->t('Entity tree'),
        '#url' => Url::fromRoute('entity_reference_tree.widget_form',['field_edit_id' => $edit_id, 'bundle' => $str_target, 'entity_type' => $str_target_type]),
        '#attributes' => [
            'class' => [
                'use-ajax',
                'button',
            ],
        ],
    ];

    return $arr_element;
  }
  
  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    return $values['target_id'];
  }
}
