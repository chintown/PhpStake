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
        protected $localSortCriteria = array();
        protected $globalSortCriteria = array();
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
         * @param $criteria  OrderCriterion('-fieldNameMajor')
         *                   OrderCriterion('+fieldNameMinor')
         *                  ...
         */
        public function setLocalSortCriteria() {
            $args = func_get_args();
            if (func_num_args() === 1 && is_array($args[0])) {
                $this->localSortCriteria = $args[0];
            } else {
                $this->localSortCriteria = func_get_args();
            }
        }
        public function setGlobalSortCriteria() {
            $args = func_get_args();
            if (func_num_args() === 1 && is_array($args[0])) {
                $this->globalSortCriteria = $args[0];
            } else {
                $this->globalSortCriteria = func_get_args();
            }
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

                if (!empty($this->globalSortCriteria)) {
                    map($this->globalSortCriteria, function (OrderCriterion $criterion) use($query) {
                        $orderSetter = ($criterion->getOrder() === SORT_ASC)
                                    ? array($query, 'orderByAscending')
                                    :  array($query, 'orderByDescending');
                        call_user_func($orderSetter, $criterion->getField());
                    }, false);
                }
                //de($query);
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
            if (!empty($this->localSortCriteria)) {
                $paramsOfSorter = array();
                foreach ($this->localSortCriteria as $criterion) {
                    $field = $criterion->getField();
                    $order = $criterion->getOrder();
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