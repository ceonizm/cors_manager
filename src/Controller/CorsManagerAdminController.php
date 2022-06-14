<?php

namespace Drupal\cors_manager\Controller;

class CorsManagerAdminController extends \Drupal\Core\Controller\ControllerBase {


  public function adminConfig(\Symfony\Component\HttpFoundation\Request $request) {

    $render = [];
    $render['main_form'] = $this->formBuilder()->getForm(\Drupal\cors_manager\Form\CorsManagerAdminForm::class);
    $render['add_form'] = $this->formBuilder()->getForm(\Drupal\cors_manager\Form\CorsManagerAddOverrideSubForm::class);


    return $render;
  }

}
