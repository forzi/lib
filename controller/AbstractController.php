<?
namespace stradivari\controller {
    abstract class AbstractController {
		public static function loadView($view, array $arguments = array()) {
            extract($arguments, EXTR_OVERWRITE);
            $filepath = static::searchFile($view);
			ob_start();
                include $filepath;
				$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}
        protected static function searchFile($file) {
            return realpath($file);
        }
	}
}
