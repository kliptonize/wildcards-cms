<?php

namespace Collections\Controller;


class Admin extends \Cockpit\AuthController {


    public function index() {

        $collections = $this->module('collections')->getCollectionsInGroup(null, true);

        foreach ($collections as $collection => $meta) {
            $collections[$collection]['allowed'] = [
                'delete' => $this->module('cockpit')->hasaccess('collections', 'delete'),
                'create' => $this->module('cockpit')->hasaccess('collections', 'create'),
                'edit' => $this->module('collections')->hasaccess($collection, 'collection_edit'),
                'entries_create' => $this->module('collections')->hasaccess($collection, 'collection_create')
            ];
        }

        return $this->render('collections:views/index.php', compact('collections'));
    }

    public function _collections() {
        return $this->module('collections')->collections();
    }

    public function _find() {
        if ($this->param('collection') && $this->param('options')) {
            $entries = $this->module('collections')->find($this->param('collection'), $this->param('options'));

            //Update entries to add readable names
            foreach($entries as &$entry){
                $entry = $this->_updateEntryWithReadableReferenceNames($entry);
            }

            return $entries;
        }

        return false;
    }

    public function collection($name = null) {

        if ($name && !$this->module('collections')->hasaccess($name, 'collection_edit')) {
            return $this->helper('admin')->denyRequest();
        }

        if (!$name && !$this->module('cockpit')->hasaccess('collections', 'create')) {
            return $this->helper('admin')->denyRequest();
        }

        $collection = [ 'name' => '', 'label' => '', 'color' => '', 'fields'=>[], 'acl' => new \ArrayObject, 'sortable' => false, 'in_menu' => false ];

        if ($name) {

            $collection = $this->module('collections')->collection($name);

            if (!$collection) {
                return false;
            }
        }

        // get field templates
        $templates = [];

        foreach ($this->app->helper("fs")->ls('*.php', 'collections:fields-templates') as $file) {
            $templates[] = include($file->getRealPath());
        }

        foreach ($this->app->module("collections")->collections() as $col) {
            $templates[] = $col;
        }

        // acl groups
        $aclgroups = [];

        foreach ($this->app->helper("acl")->getGroups() as $group => $superAdmin) {

            if (!$superAdmin) $aclgroups[] = $group;
        }

        return $this->render('collections:views/collection.php', compact('collection', 'templates', 'aclgroups'));
    }

    public function entries($collection) {

        if (!$this->module('collections')->hasaccess($collection, 'entries_view')) {
            return $this->helper('admin')->denyRequest();
        }

        $collection = $this->module('collections')->collection($collection);

        if (!$collection) {
            return false;
        }

        $count = $this->module('collections')->count($collection['name']);

        $collection = array_merge([
            'sortable' => false,
            'color' => '',
            'icon' => '',
            'description' => ''
        ], $collection);

        $view = 'collections:views/entries.php';

        if ($override = $this->app->path('config:collections/'.$collection['name'].'views/entries.php')) {
            $view = $path;
        }

        return $this->render($view, compact('collection', 'count'));
    }

    public function entry($collection, $id = null) {

        if ($id && !$this->module('collections')->hasaccess($collection, 'entries_view')) {
            return $this->helper('admin')->denyRequest();
        }

        if (!$id && !$this->module('collections')->hasaccess($collection, 'entries_create')) {
            return $this->helper('admin')->denyRequest();
        }

        $collection = $this->module('collections')->collection($collection);
        $entry      = new \ArrayObject([]);

        if (!$collection) {
            return false;
        }

        $collection = array_merge([
            'sortable' => false,
            'color' => '',
            'icon' => '',
            'description' => ''
        ], $collection);

        if ($id) {

            $entry = $this->module('collections')->findOne($collection['name'], ['_id' => $id]);

            //Add readable name as "display" if entry contains references
            $entry = $this->_updateEntryWithReadableReferenceNames($entry);

            if (!$entry) {
                return false;
            }
        }

        $view = 'collections:views/entry.php';

        if ($override = $this->app->path('config:collections/'.$collection['name'].'views/entry.php')) {
            $view = $override;
        }

        return $this->render($view, compact('collection', 'entry'));
    }

    public function save_entry($collection) {

        $collection = $this->module('collections')->collection($collection);

        if (!$collection) {
            return false;
        }

        $entry = $this->param('entry', false);

        if (!$entry) {
            return false;
        }

        if (!isset($entry['_id']) && !$this->module('collections')->hasaccess($collection['name'], 'entries_create')) {
            return $this->helper('admin')->denyRequest();
        }

        if (isset($entry['_id']) && !$this->module('collections')->hasaccess($collection['name'], 'entries_edit')) {
            return $this->helper('admin')->denyRequest();
        }

        $entry['_by'] = $this->module('cockpit')->getUser('_id');

        if (isset($entry['_id'])) {
            $_entry = $this->module('collections')->findOne($collection['name'], ['_id' => $entry['_id']]);
            $revision = !(json_encode($_entry) == json_encode($entry));
        } else {
            $revision = true;
        }

        //Loop over entry, and update all "_id" with ObjectID. Goddamn, should have length 24, and be string, and be prefixed with "_"
        //new \MongoDB\BSON\ObjectID();
        //Of course, only if storageType = MongoDB
        if ($this->app->storage->type == 'mongodb') {
            $entry = $this->_updateRecursiveArrayWithMongoObjectIDs($entry);
        }

        $entry = $this->module('collections')->save($collection['name'], $entry, ['revision' => $revision]);

        return $entry;
    }

    public function delete_entries($collection) {

        $collection = $this->module('collections')->collection($collection);

        if (!$collection) {
            return false;
        }

        if (!$this->module('collections')->hasaccess($collection['name'], 'entries_delete')) {
            return $this->helper('admin')->denyRequest();
        }

        $filter = $this->param('filter', false);

        if (!$filter) {
            return false;
        }

        $this->module('collections')->remove($collection['name'], $filter);

        return true;
    }

    public function export($collection) {

        if (!$this->app->module("cockpit")->hasaccess("collections", 'manage')) {
            return false;
        }

        $collection = $this->module('collections')->collection($collection);

        if (!$collection) return false;

        $entries = $this->module('collections')->find($collection['name']);

        return json_encode($entries, JSON_PRETTY_PRINT);
    }

    public function find() {

        $collection = $this->app->param('collection');
        $options    = $this->app->param('options');

        if (!$collection) return false;

        $collection = $this->app->module('collections')->collection($collection);

        if (isset($options['filter']) && is_string($options['filter'])) {
            $options['filter'] = $this->_filter($options['filter'], $collection);
        }

        $entries = $this->app->module('collections')->find($collection['name'], $options);
        $count   = $this->app->module('collections')->count($collection['name'], isset($options['filter']) ? $options['filter'] : []);
        $pages   = isset($options['limit']) ? ceil($count / $options['limit']) : 1;
        $page    = 1;

        //Loop over entries, search for ObjectId's, and find a way to add them safely
        foreach ($entries as $entry_key => &$entry) {
            $entry = $this->_updateEntryWithReadableReferenceNames($entry);
        }

        if ($pages > 1 && isset($options['skip'])) {
            $page = ceil($options['skip'] / $options['limit']) + 1;
        }

        return compact('entries', 'count', 'pages', 'page');
    }


    public function revisions($collection, $id) {

        if (!$this->module('collections')->hasaccess($collection, 'entries_edit')) {
            return $this->helper('admin')->denyRequest();
        }

        $collection = $this->module('collections')->collection($collection);

        if (!$collection) {
            return false;
        }

        $entry = $this->module('collections')->findOne($collection['name'], ['_id' => $id]);

        if (!$entry) {
            return false;
        }

        $revisions = $this->app->helper('revisions')->getList($id);

        
        return $this->render('collections:views/revisions.php', compact('collection', 'entry', 'revisions'));
    }

    protected function _filter($filter, $collection) {

        if ($this->app->storage->type == 'mongolite') {
            return $this->_filterLight($filter, $collection);
        }

        if ($this->app->storage->type == 'mongodb') {
            return $this->_filterMongo($filter, $collection);
        }

        return null;

    }

    protected function _filterLight($filter, $collection) {

        $allowedtypes = ['text','longtext','boolean','select','html','wysiwyg','markdown','code'];
        $criterias    = [];
        $_filter      = null;

        foreach($collection['fields'] as $field) {

            if ($field['type'] != 'boolean' && in_array($field['type'], $allowedtypes)) {
                $criteria = [];
                $criteria[$field['name']] = ['$regex' => $filter];
                $criterias[] = $criteria;
            }

            if ($field['type']=='collectionlink') {
                $criteria = [];
                $criteria[$field['name'].'.display'] = ['$regex' => $filter];
                $criterias[] = $criteria;
            }

            if ($field['type']=='location') {
                $criteria = [];
                $criteria[$field['name'].'.address'] = ['$regex' => $filter];
                $criterias[] = $criteria;
            }

        }

        if (count($criterias)) {
            $_filter = ['$or' => $criterias];
        }

        return $_filter;
    }

    protected function _filterMongo($filter, $collection) {

        $allowedtypes = ['text','longtext','boolean','select','html','wysiwyg','markdown','code'];
        $criterias    = [];
        $_filter      = null;

        foreach($collection['fields'] as $field) {

            if ($field['type'] != 'boolean' && in_array($field['type'], $allowedtypes)) {
                $criteria = [];
                $criteria[$field['name']] = ['$regex' => $filter, '$options' => 'i'];
                $criterias[] = $criteria;
            }

            if ($field['type']=='collectionlink') {
                $criteria = [];
                $criteria[$field['name'].'.display'] = ['$regex' => $filter, '$options' => 'i'];
                $criterias[] = $criteria;
            }

            if ($field['type']=='location') {
                $criteria = [];
                $criteria[$field['name'].'.address'] = ['$regex' => $filter, '$options' => 'i'];
                $criterias[] = $criteria;
            }

        }

        if (count($criterias)) {
            $_filter = ['$or' => $criterias];
        }

        return $_filter;
    }

    protected function _updateRecursiveArrayWithMongoObjectIDs($entry){
        //Loop over entry, and update all "_id" with ObjectID. Goddamn, should have length 24, and be string, and be prefixed with "_"
        foreach($entry as $key => &$value){
            if(is_array($value) && !array_key_exists('$oid', $value)){
                $value = $this->_updateRecursiveArrayWithMongoObjectIDs($value);
            }
            if(substr($key, 0, 1) === "_"){
                if(is_string($value) && strlen($value) === 24){
                    $value = new \MongoDB\BSON\ObjectID($value);
                }elseif(is_array($value) && array_key_exists('$oid', $value)){
                    $value = new \MongoDB\BSON\ObjectID($value['$oid']);
                }
            }
        }
        return $entry;
    }

    protected function _updateEntryWithReadableReferenceNames($entry){
        //Update all MongoID-reference fields and add a "display"-field with title of object
        foreach($entry as $key => &$value){
            if(is_array($value)){
                foreach($value as $value_key => &$val){
                    //Check if this $value has nested array
                    if(is_int($value_key) && is_array($val)){
                        if(array_key_exists("_id", $val)){
                            //Add readable name, you never know
                            $object = $this->app->module('collections')->findOne($key . "s", ["_id" => $val["_id"]]);
                            $val["display"] = $this->_determineTitle($object);
                        }
                    }elseif($value_key === "_id"){
                        //Add readable name, you never know
                        $object = $this->app->module('collections')->findOne($key . "s", ["_id" => $val]);
                        $value["display"] = $this->_determineTitle($object);
                    }
                }
            }
        }

        return $entry;
    }

    protected function _determineTitle($object){
        $title = null;
        if($object !== null && is_array($object)){
            $possibilities = ["title", "name"];
            foreach($possibilities as $p){
                if(array_key_exists($p, $object)){
                    $title = $object[$p];
                }
            }
            if($title === null){
                foreach($object as $obj_key => $obj_val){
                    if(is_string($obj_val)){
                        $title = $obj_val;
                        break;
                    }
                }
            }
        }else{
            $title = "Object not found";
        }

        return $title;
    }
}
