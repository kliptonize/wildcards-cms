<?php
 return array (
  'name' => 'Cards',
  'label' => 'Cards',
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
      'label' => 'Image for card',
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
      'name' => 'linked_to_set',
      'label' => 'Linked to set',
      'type' => 'collectionlink',
      'default' => '',
      'info' => '',
      'group' => 'Meta',
      'localize' => false,
      'options' => 
      array (
        'link' => 'Sources',
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
  '_created' => 1501150390,
  '_modified' => 1501150390,
  'color' => '#D8334A',
  'acl' => 
  array (
  ),
  'icon' => 'stop.svg',
  'description' => 'Cards for sets',
);