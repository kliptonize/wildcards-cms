<?php
 return array (
  'name' => 'Cards',
  'label' => 'Card',
  '_id' => 'Cards5974b5a160809',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'title',
      'label' => 'Title of card',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => 'Content',
      'localize' => true,
      'options' => 
      array (
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
    1 => 
    array (
      'name' => 'image',
      'label' => 'Image of card',
      'type' => 'image',
      'default' => '',
      'info' => '',
      'group' => 'Content',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
    2 => 
    array (
      'name' => 'set',
      'label' => 'Set of this card',
      'type' => 'collectionlink',
      'default' => '',
      'info' => '',
      'group' => 'Meta',
      'localize' => false,
      'options' => 
      array (
        'link' => 'Sets',
        'multiple' => false,
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
  ),
  'sortable' => true,
  'in_menu' => false,
  '_created' => 1500820897,
  '_modified' => 1501685026,
  'color' => '#A0D468',
  'acl' => 
  array (
  ),
  'icon' => 'stop.svg',
  'description' => 'A card contains information, and are grouped in \'sets\'',
);