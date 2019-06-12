<?php

System::getContainer()->get('huh.utils.dca')->loadLanguageFile('tl_content');

$dca = &$GLOBALS['TL_DCA']['tl_list_config'];

/**
 * Palettes
 */
$dca['palettes']['__selector__'][] = 'renderItemsAsMap';
$dca['palettes']['__selector__'][] = 'useListAsMapControl';
$dca['palettes']['default']        = str_replace('isTableList', 'isTableList,useListAsMapControl', $dca['palettes']['default']);

/**
 * Subpalettes
 */
$dca['subpalettes']['renderItemsAsMap']    = 'itemMap';
$dca['subpalettes']['useListAsMapControl'] = 'controlledMap';

/**
 * Fields
 */
$fields = [
    // not implemented, yet
    'renderItemsAsMap'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_list_config']['renderItemsAsMap'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    // not implemented, yet
    'itemMap'             => [
        'label'            => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_map'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['huh.google_maps.data_container.google_map', 'getMapChoices'],
        'eval'             => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "int(10) unsigned NOT NULL default '0'"
    ],
    'useListAsMapControl' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_list_config']['useListAsMapControl'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'controlledMap'       => [
        'label'            => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_map'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['huh.google_maps.data_container.google_map', 'getMapChoices'],
        'eval'             => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "int(10) unsigned NOT NULL default '0'"
    ],
];

$dca['fields'] = array_merge(is_array($dca['fields']) ? $dca['fields'] : [], $fields);