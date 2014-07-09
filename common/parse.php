<?php
    class UtilParseQuery {
        protected $baseClass = '';
        protected $constraints = array();
        protected $sortCriteria = array();
        protected $sorter;

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

            $this->sort($result);
            return $result;
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