<?
namespace stradivari\enum {
    class SortKey extends AbstractUnique {
        public function __construct(&$entity, $attributes = array()) {
            parent::__construct($entity);
            if ( !$attributes || !isset($attributes['order']) ) {
                throw new \stradivari\enum\exception\IncorrectArgList();
            }
            $keys = array();
            foreach ($entity as $key=>$val) {
                $keys[] = $key;
            }
            $this->value = $this->multikeySort($keys, $attributes['order']);
        }
        private function multikeySort($entity, array &$sortRules) {
            $mainEntity = &$this->entity;
            $sortFunction = function(&$first, &$second) use (&$sortRules, &$mainEntity) {
                $mainFirst = $mainEntity[$first];
                $mainSecond = $mainEntity[$second];
                foreach ($sortRules as $sortField => &$order) {
                    $order = strtolower($order);
                    $currentFirst = isset($mainFirst[$sortField]) ? $mainFirst[$sortField] : null;
                    $currentSecond = isset($mainSecond[$sortField]) ? $mainSecond[$sortField] : null;
                    $result = AbstractSort::compareArguments($currentFirst, $currentSecond);
                    if ( $result ) {
                        return $order == 'asc' ? $result : -$result;
                    }
                }
                return 0;
            };
            usort($entity, $sortFunction);
            return $entity;
        }
    }
}
