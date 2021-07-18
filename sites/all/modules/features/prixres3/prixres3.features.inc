<?php

/**
 * @file
 * prixres3.features.inc
 */

/**
 * Implements hook_node_info().
 */
function prixres3_node_info() {
  $items = array(
    'prixresidences' => array(
      'name' => t('PrixResidences'),
      'base' => 'node_content',
      'description' => t('autres que Ehpad ( qui sont gérées dans "chambres" )'),
      'has_title' => '1',
      'title_label' => t('Title'),
      'help' => '',
    ),
    'residence' => array(
      'name' => t('Residence'),
      'base' => 'node_content',
      'description' => '',
      'has_title' => '1',
      'title_label' => t('Raison Sociale'),
      'help' => '',
    ),
  );
  drupal_alter('node_info', $items);
  return $items;
}
