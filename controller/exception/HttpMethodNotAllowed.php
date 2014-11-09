<?
namespace stradivari\controller\exception {
    class HttpMethodNotAllowed extends AllowedMethods {
        public function __construct(array $allowedMethods) {
            parent::__construct($allowedMethods);
            $this->code = 405;
            $this->message = 'Method Not Allowed';
        }
    }
}
