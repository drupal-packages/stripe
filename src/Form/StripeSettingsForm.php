<?php

namespace Drupal\stripe\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StripeConfig.
 *
 * @package Drupal\stripe\Form
 */
class StripeSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'stripe.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'stripe_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('stripe.settings');

    $form['environment'] = [
      '#type' => 'radios',
      '#options' => ['test' => $this->t('Test'), 'live' => $this->t('Live')],
      '#title' => $this->t('Environment'),
      '#default_value' => $config->get('environment'),
      '#required' => TRUE,
    ];

    $form['apikey_test'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Test'),
      '#description' => $this->t('<a href=":uri">Stripe dashboard</a>', [':uri' => 'https://dashboard.stripe.com/account/apikeys']),
    ];

    $form['apikey_test']['apikey_public_test'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Publishable'),
      '#default_value' => $config->get('apikey')['test']['public'],
    ];

    $form['apikey_test']['apikey_secret_test'] = [
      '#type' => 'password',
      '#title' => $this->t('Secret'),
      '#default_value' => $config->get('apikey')['test']['secret'],
      '#placeholder' => $config->get('apikey')['test']['secret'] ? str_repeat('●', 32) : '',
    ];

    $form['apikey_live'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Live'),
      '#description' => $this->t('<a href=":uri">Stripe dashboard</a>', [':uri' => 'https://dashboard.stripe.com/account/apikeys']),
    ];

    $form['apikey_live']['apikey_public_live'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Publishable'),
      '#default_value' => $config->get('apikey')['live']['public'],
    ];

    $form['apikey_live']['apikey_secret_live'] = [
      '#type' => 'password',
      '#title' => $this->t('Secret'),
      '#default_value' => $config->get('apikey')['live']['secret'],
      '#placeholder' => $config->get('apikey')['live']['secret'] ? str_repeat('●', 32) : '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('stripe.settings');

    $secret = $form_state->getValue('apikey_secret_test');
    if ($secret) {
      $config->set('apikey.test.secret', $secret);
    }

    $secret = $form_state->getValue('apikey_secret_live');
    if ($secret) {
      $config->set('apikey.live.secret', $secret);
    }

    $this->config('stripe.settings')
      ->set('apikey.test.public', $form_state->getValue('apikey_public_test'))
      ->set('apikey.live.public', $form_state->getValue('apikey_public_live'))
      ->set('environment', $form_state->getValue('environment'))
      ->save();
  }

}
