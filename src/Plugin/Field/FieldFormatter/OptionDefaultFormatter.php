<?php

namespace Drupal\civicrm_field_options\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\civicrm_field_options\Utility\CivicrmService;

/**
 * Plugin implementation of the 'civicrm_field_option' formatter.
 *
 * @FieldFormatter(
 *   id = "civicrm_field_option_default_formatter",
 *   label = @Translation("CiviCRM Option default formatter"),
 *   field_types = {
 *     "civicrm_field_option"
 *   }
 * )
 */
class OptionDefaultFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The CiviCRM API service.
   *
   * @var \Drupal\civicrm_field_options\Utility\CivicrmService
   */
  protected $civicrm;

  /**
   * The logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('civicrm.service'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, CivicrmService $service, LoggerChannelFactoryInterface $logger) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->civicrm = $service;
    $this->logger = $logger->get('civicrm_field_options');
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    // Not to cache this field formatter.
    $elements['#cache']['max-age'] = 0;

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $settings = $item->getFieldDefinition()->getSettings();
    $field_option_group = $settings['option_group_id'];
    if (empty($field_option_group)) {
      return '';
    }
    $result = '';
    if ($item->get('value')->getValue() != NULL) {
      $optionValue = $item->get('value')->getValue();
      try {
        $result = $this->civicrm->api('OptionValue', 'getvalue', [
          'return' => "label",
          'option_group_id' => $field_option_group,
          'value' => $optionValue,
        ]);
      }
      catch (\Exception $e) {
        $this->logger->error(print_r($e->getMessage(), TRUE));
      }
    }

    return $result;
  }

}
