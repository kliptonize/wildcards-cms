<?php
 return array (
  'name' => 'Sets',
  'label' => 'Sets',
  '_id' => 'Sets5974b6e097ea2',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'title',
      'label' => 'Title of set',
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
      'label' => 'First image of set',
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
      'name' => 'tags',
      'label' => 'Tags',
      'type' => 'tags',
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
    3 => 
    array (
      'name' => 'source',
      'label' => 'Source of cards',
      'type' => 'collectionlink',
      'default' => '',
      'info' => '',
      'group' => 'Meta',
      'localize' => false,
      'options' => 
      array (
        'link' => 'Sources',
        'multiple' => true,
      ),
      'width' => '1-2',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
    4 => 
    array (
      'name' => 'sourceurl',
      'label' => 'Source of set',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => 'Meta',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-2',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
  ),
  'sortable' => true,
  'in_menu' => false,
  '_created' => 1501150289,
  '_modified' => 1501150289,
  'color' => '#A0D468',
  'acl' => 
  array (
  ),
  'icon' => 'archive.svg',
  'description' => 'Sets of cards, contains a set of information',
);