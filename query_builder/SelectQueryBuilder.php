<?

namespace stradivari\query_builder {
    class SelectQueryBuilder extends AbstractQueryBuilder {
        protected $type = 'Select';
        public $distinct = false;
        public $highPriority = false;
        public $straightJoin = false;
        public $fields = array('*');
        public $from = '';
        protected $join = array();
        public $where= array();
        public $groupBy = array();
        public $having = array();
        public $orderBy = array();
        public $limit = null;
        public $offset = null;
        
        protected function abstractJoin($type, $entity, $alias, $on) {
            $result = array(
                'type' => $type,
                'entity' => $entity
            );
            if ( $alias ) {
                $result['alias'] = $alias;
            }
            if ( $on ) {
                $result['on'] = $on;
            }
            $this->join[] = $result;
        }
        
        public function join($entity, $alias = '', $on = null) {
            $this->abstractJoin($on ? 'join' : 'natural join', $entity, $alias, $on);
        }
        
        public function leftJoin($entity, $alias = '', $on = null) {
            $this->abstractJoin('left join', $entity, $alias, $on);
        }
        
        public function rightJoin($entity, $alias = '', $on = null) {
            $this->abstractJoin('right join', $entity, $alias, $on);
        }
        protected function parseConditions($conditions, $keyWord = '', $operator = 'and') {
            if ( !$conditions ) {
                return '';
            }
            $strConditions = array();
            
            foreach ( $conditions as $key => $condition ) {
                if ( $key === 'and' || $key === 'or' ) {
                    $strConditions[] = $this->parseConditions($condition, '', $key);
                } else if ( is_array($condition) ) {
                    $strConditions[] = $this->parseConditions($condition);
                } else {
                    $strConditions[] = "{$condition}";
                }
            }
            
            $strConditions = implode("
                {$operator} ", $strConditions);
                
            return trim("{$keyWord } ({$strConditions})");
        }
        protected function parseGroupBy() {
            $result = trim(implode(", ", $this->groupBy));
            return $result ? "group by {$result}" : '';
        }
        protected function parseOrderBy() {
            $orderByStr = array();
            foreach ( $this->orderBy as $key => $direction ) {
                $orderByStr[] = "{$key} {$direction}";
            }
             $orderByStr = trim(implode(", ", $orderByStr));
             return $orderByStr ? "order by {$orderByStr}" : '';
        }
        protected function parsePagination() {
            if ( !$this->limit ) {
                return '';
            }
            
            if ( $this->offset ) {
                $result = array($this->offset, $this->limit);
                $result = trim(implode(', ', $result));
            } else {
                $result = $this->limit;
            }
            
            return "limit {$result}";
        }
        protected function parseJoins() {
            $joinStr = array();
            foreach ( $this->join as $join ) {
                $curStr = "{$join['type']} ({$join['entity']})";
                if ( isset($join['alias']) ) {
                    $curStr .= " as {$join['alias']}";
                }
                if ( isset($join['on']) ) {
                    $on = $this->parseConditions($join['on']);
                    $curStr .= " on {$on}";
                }
                $joinStr[] = $curStr;
            }
            return trim(implode("\n", $joinStr));
        }
        protected function parseFields() {
            $result =array();
            foreach ( $this->fields as $key => $val ) {
                if ( is_string($key) ) {
                    $result[] = "{$key} as {$val}";
                } else {
                    $result[] = $val;
                }
            }
            return trim(implode(",\n", $result));
        }
        public function __toString() {
            $result = "
                {$this->type}
                    {$this->parseFields()}
                    from {$this->from}
                    {$this->parseJoins()}
                    {$this->parseConditions($this->where, 'where')}
                    {$this->parseGroupBy()}
                    {$this->parseConditions($this->having, 'having')}
                    {$this->parseOrderBy()}
                    {$this->parsePagination()}
            ";
            return trim($result);
        }
    }
}