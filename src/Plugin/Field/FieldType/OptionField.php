<?php

namespace Drupal\civicrm_field_options\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'civicrm_field_option' field type.
 *
 * @FieldType(
 *   id = "civicrm_field_option",
 *   label = @Translation("CiviCRM Field Options"),
 *   category = @Translation("CiviCRM"),
 *   default_widget = "civicrm_field_option_widget",
 *   default_formatter = "civicrm_field_option_default_formatter"
 * )
 */
class OptionField extends FieldItemBase implements FieldItemInterface {


  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
        'option_group_id' => '',
      ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    // Store value(s) for the field.
    $schema['columns'] = [
      'value' => [
        'type' => 'varchar',
        'description' => 'Field Option',
        'length' => 256,
        'not_null' => FALSE,
      ],
    ];

    // Return schema.
    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Details about field properties.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Field Option'));

    // Return properties.
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $elements = [];
    \Drupal::service('civicrm')->initialize();
    $optionList = [];
    $options = civicrm_api('OptionGroup', 'get', ['version' => 3, 'is_active' => 1, 'option.limit' => 1000]);
    foreach ($options['values'] as $option) {
      $optionList[$option['id']] = $option['title'];
    }

    $elements['option_group_id'] = [
      '#type' => 'select',
      '#title' => $this->t('CiviCRM Option Groups'),
      '#options' => $optionList,
      '#default_value' => $this->getSetting('option_group_id'),
      '#required' => TRUE,
      '#description' => t('Choose CiviCRM option group for getting options.'),
      '#rows' => 20,
      '#disabled' => $has_data,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();

    return $value === NULL || $value === '';
  }

}
