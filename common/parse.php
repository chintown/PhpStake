<?php

    /*
    $query_helper = new UtilParseQuery('Brand');
    $query_helper->setSortCriteria(array('field'=>'title', 'order'=> SORT_ASC));
    $query_helper->addConstraint(function($query) use ($objectId) {
        $query->whereEqualTo("objectId", $objectId);
        $query->whereContainedIn('objectId', $item_object_ids);
        $query->whereInclude('images');
        return $query;
    });
    $result = $query_helper->find();
    */
    class UtilParseQuery {
        protected $baseClass = '';
        protected $constraints = array();
        protected $sortCriteria = array();
        protected $sorter;
        protected $count;

        public function __construct($baseClass){
            $this->baseClass = $baseClass;
        }

        /**
         * @param $constraint callable function ($query) { ...; return $query }
         * @throws Exception if $constraint is not callable
         */
        public function addConstraint($constraint) {
            //
            if (!is_callable($constraint)) {
                throw new Exception("$constraint must be a function with one parameter, \$query");
            }
            $this->constraints[] = $constraint;
        }
        /**
         * @param $criteria array('field'=>'fieldNameMajor', 'order'=SORT_DESC),
         *                  array('field'=>'fieldNameMinor', 'order'=SORT_ASC),
         *                  ...
         */
        public function setSortCriteria() {
            $this->sortCriteria = func_get_args();
        }
        public function find() {
            $result = array();
            try {
                $query = new ParseQuery($this->baseClass);
                foreach ($this->constraints as $constraint) {
                    $query = $constraint($query);
                }
                if(empty($this->_query) && (!empty($this->_inclue) || !empty($this->_order) || !empty($this->_limit) || !empty($this->_skip) || !empty($this->_count))) {
                    throw new Exception('due to limitation of parse php library, you must give a "where" clause for using "order", "limit" or similar constraint!!');
                }
                $ret = $query->find();
            } catch (ParseLibraryException $e) {
                bde('ParseLibraryException: '.$e->getMessage());
                return $result;
            }

            if (!$ret) {
                bde('false response for querying '.$this->baseClass.' with Query '.var_export($query, true));
                return $result;
            } else {
                $result = $this->convertParseResultsToArrays($ret->results);
            }

            if (isset($ret->count)) {
                $this->count = $ret->count;
            }

            $this->sort($result);
            return $result;
        }
        public function getCount() {
            return $this->count;
        }


        private function convertParseResultsToArrays($results) {
            $self = $this;
            return map($results, function($object) use ($self) {
                return $self->objectToArray($object);
            }, false);
        }
        public function objectToArray($object) {
            $array = get_object_vars($object);
            unset($object);
            return $array;
        }

        private function sort(&$result) {
            if (!empty($this->sortCriteria)) {
                $paramsOfSorter = array();
                foreach ($this->sortCriteria as $criterion) {
                    $field = $criterion['field'];
                    $order = $criterion['order'];
                    $fields = map($result, function($object) use ($field) {
                        return $object[$field];
                    }, false);
                    $paramsOfSorter[] = &$fields;
                    $paramsOfSorter[] = &$order;
                }
                $paramsOfSorter[] = &$result; // make use array_multisort will ...
                call_user_func_array('array_multisort', $paramsOfSorter); // change result rather than copy
            }
            return $result;
        }
    }

    /*
    $query_helper = new UtilParseForeignKeyQuery('Item', 'Brand');
    $query_helper->addConstraintForeignKey('brand', $brand_object_id);
    $query_helper->addConstraint(function ($query) {
        $query->whereInclude('images');
        return $query;
    });
    $query_helper->setSortCriteria(array('field'=>'title', 'order'=> SORT_ASC));
    $results = $query_helper->find();
    */
    class UtilParseForeignKeyQuery extends UtilParseQuery {
        private $foreignClass = '';
        public function __construct($baseClass, $foreignClass){
            $this->baseClass = $baseClass;
            $this->foreignClass = $foreignClass;
        }

        public function addConstraintForeignKey($key, $value) {
            $foreignClass = $this->foreignClass;

            $this->addConstraint(function ($query) use($key, $value, $foreignClass) {
                $pointer = array(
                    "__type" => "Pointer",
                    "className" => $foreignClass,
                    "objectId" => $value
                );
                $query->where($key, $pointer);
                return $query;
            });

        }
    }