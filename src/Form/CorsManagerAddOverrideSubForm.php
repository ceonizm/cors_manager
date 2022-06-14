<?php

namespace Drupal\cors_manager\Form;

use Drupal\Core\Form\FormStateInterface;

class CorsManagerAddOverrideSubForm extends \Drupal\Core\Form\FormBase {


  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'CorsManagerAddOverrideSubForm';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    // TODO: Implement buildForm() method.
    $form['overrides']['new'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Add Override')
    ];

    $form['overrides']['new']['routeName'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Route name'),
      '#description' => $this->t('route or path'),
      '#required' => TRUE
    ];

    $form['overrides']['new']['allowedHeaders'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed headers'),
      '#description' => $this->t('Allowed headers'),
      '#default_value' => "",
    ];

    $form['overrides']['new']['allowedMethods'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed methods'),
      '#description' => $this->t('Allowed methods'),
      '#default_value' => "",
    ];

    $form['overrides']['new']['allowedOrigins'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed Origins'),
      '#description' => $this->t('Allowed origins'),
      '#default_value' => "",
    ];

    $form['overrides']['new']['checkboxes'] = [
      '#type' => 'container',
      '#tree' => FALSE,
      '#attributes' => ['class' => ['container-inline']]
    ];
    $form['overrides']['new']['checkboxes']['exposedHeaders'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Exposed headers'),
      '#parents' => ['overrides', 'new', 'exposedHeaders'],
      '#default_value' => "",
    ];

    $form['overrides']['new']['checkboxes']['maxAge'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Max Age'),
      '#parents' => ['overrides', 'new', 'maxAge'],
      '#default_value' => "",
    ];

    $form['overrides']['new']['checkboxes']['supportCredentials'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Support credentials'),
      '#parents' => ['overrides', 'new', 'supportCredentials'],
      '#default_value' => "",
    ];

    $form['overrides']['new']['add_btn'] = [
      '#type' => 'button',
      '#title' => $this->t('Add override'),
      '#default_value' => 'Add Override',
      '#ajax' => [
        'wrapper' => 'overrides-wrapper',
        'callback' => '::ajaxAddOverride'
      ]
    ];


    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }
}
