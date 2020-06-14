<?php

namespace Drupal\custom_404_redirect\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class Custom404AdminSettingsForm.
 */
class Custom404AdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'custom_404_redirect.custom404adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_404_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['404settings']['#title'] = $this->t('404 Settings Form');
    $form['404settings']['table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Paths'),
        $this->t('Redirect Page'),
      ],
      '#empty' => $this->t('No Redirect settings available'),
    ];

    // Get this module's configuration
    $config = $this->config('custom_404_redirect.custom404adminsettings');
    $redirects = $config->get();

    foreach($redirects as $link => $settings) {
      if (!in_array($link, ['custom_404_redirect', '_core', '']) && isset($link)) {
        $form['404settings']['table'][$link]['paths'] = [
          '#type' => 'textfield',
          '#default_value' => $link,
          '#maxlength' => 255,
          '#size' => 40,
        ];
        $form['404settings']['table'][$link]['redirect_page'] = [
          '#type' => 'textfield',
          '#default_value' => $settings,
          '#maxlength' => 255,
          '#size' => 40,
        ];
      }
    }

    $form['404settings']['table']['new']['paths'] = [
      '#type' => 'textfield',
      '#default_value' => '',
      '#maxlength' => 255,
      '#size' => 40,
    ];
    $form['404settings']['table']['new']['redirect_page'] = [
      '#type' => 'textfield',
      '#default_value' => '',
      '#maxlength' => 255,
      '#size' => 40,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // clear previous set values
    \Drupal::configFactory()->getEditable('custom_404_redirect.custom404adminsettings')->delete();

    foreach ($form_state->getValue('table') as $k => $value) {
      $this->config('custom_404_redirect.custom404adminsettings')->set($value['paths'], $value['redirect_page'])->save();
    }
  }

}
