<?php

namespace Drupal\civicrm_field_options\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * {@inheritdoc}
   */

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    \Drupal::service('civicrm')->initialize();
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
    $results = '';
    if ($item->get('value')->getValue() != NULL) {
      $optionValue = $item->get('value')->getValue();
      try {
        $result = civicrm_api3('OptionValue', 'getvalue', [
          'return' => "label",
          'option_group_id' => $field_option_group,
          'value' => $optionValue,
        ]);
      }
      catch (\Exception $e) {
        $this->logger->get('Option Value')->error($e->getMessage());
      }
      if (!is_null($results) && !empty($results)) {
        $elements[] = [
          '#type' => 'markup',
          '#markup' => $result,
          '#cache' => [
            'max-age' => 0,
          ],
        ];
      }
    }

    return $result;
  }
}
