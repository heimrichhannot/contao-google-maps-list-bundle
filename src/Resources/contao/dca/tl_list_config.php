<?php

System::getContainer()->get('huh.utils.dca')->loadLanguageFile('tl_content');

$dca = &$GLOBALS['TL_DCA']['tl_list_config'];

/**
 * Palettes
 */
$dca['palettes']['__selector__'][] = 'renderItemsAsMap';
$dca['palettes']['default']        = str_replace('isTableList', 'isTableList,renderItemsAsMap', $dca['palettes']['default']);

/**
 * Subpalettes
 */
$dca['subpalettes']['renderItemsAsMap'] = 'itemMap,addMapControlList';

/**
 * Fields
 */
$fields = [
    'renderItemsAsMap'  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_list_config']['renderItemsAsMap'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'itemMap'           => [
        'label'            => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_map'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => ['huh.google_maps.data_container.google_map', 'getMapChoices'],
        'eval'             => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
        'sql'              => "int(10) unsigned NOT NULL default '0'"
    ],
    'addMapControlList' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_list_config']['addMapControlList'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''"
    ],
];

$dca['fields'] = array_merge(is_array($dca['fields']) ? $dca['fields'] : [], $fields);