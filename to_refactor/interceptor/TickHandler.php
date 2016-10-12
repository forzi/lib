<?
namespace stradivari\interceptor {
    class TickHandler extends AbstractHandler {
        public static function getInstance() {
            return parent::getInstance('register_tick_function');
        }
    }
}