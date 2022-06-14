<?php

namespace Drupal\cors_manager\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CorsManagerAdminForm extends ConfigFormBase {


  /**
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $_container;

  public function __construct(ContainerInterface $container) {
    $this->_container = $container;
    parent::__construct($container->get('config.factory'));
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container
    );
  }


  protected function getEditableConfigNames() {
    return [
      'cors_manager.config',
    ];
  }

  public function getMergedConfig($properties, array $config, array $default):array {
    $result = [];
    foreach ($properties as $property) {
      $result[$property] = $config[$property] ?? $default[$property];
    }
    return $result;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $configGlobal = $this->config('cors_manager.config');

    $cors_config = \Drupal::getContainer()->getParameter('cors.config');

    $form['global'] = [
      '#title' => $this->t('Global configuration'),
      '#type' => 'fieldset',
    ];

    $mergedConfig = $this->getMergedConfig(['allowedHeaders', 'allowedMethods', 'allowedOrigins', 'exposedHeaders', 'maxAge', 'supportCredentials'], $configGlobal->get('global')??[], $cors_config);
    $this->buildConfigFields($form['global'], $mergedConfig);

    $overrides = $configGlobal->get('per_route_overrides') ?? [];

    $addOverrideCheckboxState = $form_state->getUserInput()['overrides']['add_override_link'];

    $overrideAdded = FALSE;
    $isAjax = $form_state->getTriggeringElement() !== NULL;
    if ($isAjax) {
      $triggering = $form_state->getTriggeringElement();
      if ($triggering['#ajax']['callback'] == '::ajaxAddOverride') {
        $newOverride = $form_state->getValue(['overrides', 'new']);
        if (!empty($newOverride)) {
          $overrides[] = array_merge( $newOverride, $newOverride['checkboxes']);
          $overrideAdded = TRUE;
          $addOverrideCheckboxState = NULL;
        }
      }
    }

    $form['overrides'] = [
      '#type' => 'fieldset',
      '#tree' => TRUE,
      '#title' => $this->t('Per route overrides'),
      '#prefix' => '<div id="overrides-wrapper">',
      '#suffix' => '</div>',
    ];
    if ($overrideAdded) {
      $form['overrides']['#description'] = $this->t('An override has just been added. Don\'t forget to press save button to get it saved');
    }

    foreach ($overrides as $index => $override) {
      $form['overrides']['list'][$index]['routeName'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Route name'),
        '#description' => $this->t('route or path'),
        '#default_value' => $override['routeName'],
      ];
      $this->buildConfigFields($form['overrides']['list'][$index], $override);

    }


    $form['overrides']['add_override_link'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add an override'),
      '#value' => $addOverrideCheckboxState,
      '#ajax' => [
        'wrapper' => 'overrides-wrapper',
        'callback' => '::displayAddOverridePanel',
      ],
    ];


    if ($addOverrideCheckboxState) {

      $form['overrides']['new'] = [
        '#type' => 'fieldset',
        //        '#title' => $this->t('Add Override'),
      ];
      $form['overrides']['new']['routeName'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Route name'),
        '#description' => $this->t('route or path'),
        '#required' => TRUE,
      ];

      $this->buildConfigFields($form['overrides']['new'], $mergedConfig);

      $form['overrides']['new']['add_btn'] = [
        '#type' => 'button',
        '#title' => $this->t('Add override'),
        '#default_value' => 'Add Override',
        '#ajax' => [
          'wrapper' => 'overrides-wrapper',
          'callback' => '::ajaxAddOverride',
        ],
      ];
    }

    //    $form['overrides']['new'] = $this->_container->get('form_builder')->getForm(CorsManagerAddOverrideSubForm::class);
    return parent::buildForm($form, $form_state);
  }

  public function getFormId() {
    return 'corsManagerAdminForm';
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
  }

  public static function ajaxAddOverride(array &$form, FormStateInterface $form_state) {
    return $form['overrides'];
  }

  public static function displayAddOverridePanel(array &$form, FormStateInterface $form_state) {
    return $form['overrides'];
  }


  protected function buildConfigFields(&$element, $config) {

    $element['allowedHeaders'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed headers'),
      '#description' => $this->t('Allowed headers'),
      '#default_value' => is_array( $config['allowedHeaders']) ? implode(PHP_EOL, $config['allowedHeaders']) : $config['allowedHeaders'],
    ];

    $element['allowedMethods'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed methods'),
      '#description' => $this->t('Allowed methods'),
      '#default_value' => is_array( $config['allowedMethods'] ) ? implode(PHP_EOL, $config['allowedMethods']) : $config['allowedMethods'],
    ];

    $element['allowedOrigins'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Origins'),
      '#description' => $this->t('Allowed origins'),
      '#default_value' => is_array( $config['allowedOrigins'] ) ? implode(PHP_EOL, $config['allowedOrigins']) : $config['allowedOrigins'],
    ];

    $element['checkboxes'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['container-inline']],
    ];

    $element['checkboxes']['exposedHeaders'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Exposed headers'),
      //      '#description' => $this->t('Exposed headers'),
      '#default_value' => $config['exposedHeaders'],
    ];

    $element['checkboxes']['maxAge'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Max Age'),
      //      '#description' => $this->t('Max age.'),
      '#default_value' => $config['maxAge'],
    ];

    $element['checkboxes']['supportCredentials'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Support credentials'),
      //      '#description' => $this->t('Support credentials'),
      '#default_value' => $config['supportCredentials'],
    ];
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('cors_manager.config');
    $global = [];
    $global['allowedHeaders'] = array_map( 'trim', explode(PHP_EOL, $form_state->getValue('allowedHeaders')));
    $global['allowedMethods'] = array_map( 'trim', explode(PHP_EOL, $form_state->getValue('allowedMethods')));
    $global['allowedOrigins'] = array_map( 'trim', explode(PHP_EOL, $form_state->getValue('allowedOrigins')));
    $global['exposedHeaders'] = $form_state->getValue(['checkboxes','exposedHeaders']);
    $global['maxAge'] = $form_state->getValue(['checkboxes','maxAge']);
    $global['maxAge'] = $form_state->getValue(['checkboxes','supportCredentials']);
    $config->set('global', $global);

    $overrides = $form_state->getValue(['overrides', 'list']);
    foreach( $overrides as $index=>$override) {
      $overrides[$index] = array_merge( $override, $override['checkboxes']);
      unset( $overrides[$index]['checkboxes']);
      $overrides[$index]['allowedHeaders'] = array_map( 'trim', explode(PHP_EOL, $override['allowedHeaders']));
      $overrides[$index]['allowedMethods'] = array_map( 'trim', explode(PHP_EOL, $override['allowedMethods']));
      $overrides[$index]['allowedOrigins'] = array_map( 'trim', explode(PHP_EOL, $override['allowedOrigins']));


    }
    $config->set('per_route_overrides', $overrides);

    $config->save();
    parent::submitForm($form, $form_state); // TODO: Change the autogenerated stub
  }
}
