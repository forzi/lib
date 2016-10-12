<?
namespace stradivari\controller\exception {
    class AllowedMethods extends \Exception implements \stradivari\interfaces\WebException {
        public $allowedMethods = array();
        public function __construct(array $allowedMethods) {
            parent::__construct();
            $this->allowedMethods = $allowedMethods;
        }
        public function headers() {
            return array(
                'Allowed: ' . implode(',', $this->allowedMethods)
            );
        }
    }
}
