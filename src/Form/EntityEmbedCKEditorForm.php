<?php

/**
 * @file
 * Contains \Drupal\entity_embed\Form\EntityEmbedCKEditorForm
 */

namespace Drupal\entity_embed\Form;

use Drupal\Component\Uuid\Uuid;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\entity_embed\Ajax\EntityEmbedDialogSave;

/**
 * Provides a form to embed entities by specifying data attributes.
 */
class EntityEmbedCKEditorForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_embed_ckeditor_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $form['entity_type'] = array(
      '#type' => 'select',
      '#name' => 'entity_type',
      '#title' => 'Entity type',
      '#options' => array(
        'node' => 'Node',
        'others' => 'Others',
      ),
    );
    $form['entity'] = array(
      '#type' => 'textfield',
      '#name' => 'entity',
      '#title' => 'Entity',
      '#placeholder' => 'Enter ID/UUID of the entity',
    );
    $form['view_mode'] = array(
      '#type' => 'select',
      '#name' => 'view_mode',
      '#title' => 'View Mode',
      '#options' => array(
        'teaser' => 'Teaser',
        'others' => 'Others',
      ),
    );
    $form['display_links'] = array(
      '#type' => 'checkbox',
      '#name' => 'display_links',
      '#title' => 'Display links',
    );
    $form['align'] = array(
      '#type' => 'select',
      '#name' => 'align',
      '#title' => 'Align',
      '#options' => array(
        'none' => 'None',
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right',
      ),
    );
    $form['show_caption'] = array(
      '#type' => 'checkbox',
      '#name' => 'show_caption',
      '#title' => 'Show Caption',
    );
    $form['actions'] =array('#type' => 'actions');
    $form['actions']['save_modal'] = array(
      '#type' => 'submit',
      '#value' => 'Save',
      // No regular submit-handler. This form only works via JavaScript.
      '#submit' => array(),
      '#ajax' => array(
        'callback' => array($this, 'submitForm'),
        'event' => 'click',
      ),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $response = new AjaxResponse();

    // Detect if a valid UUID was specified. Set embed method based based on
    // whether or not it is a valid UUID.
    $values = $form_state['values'];
    $entity = $values['entity'];
    if(Uuid::isValid($entity)) {
      $values['embed_method'] = 'uuid';
    }
    else {
      $values['embed_method'] = 'id';
    }

    $response->addCommand(new EntityEmbedDialogSave($values));
    $response->addCommand(new CloseModalDialogCommand());

    return $response;
  }

}
