<?
namespace stradivari\enum {
    $compareString = 'абвгґдеёжзиіїйклмнопрстуфхцчшщъыьэюя';
    abstract class AbstractSort {
        public static $compareString;
        
        public static function compareArguments($first, $second) {
            $first = is_numeric($first) || is_bool($first) || is_string($first) ? $first : null;
            $second = is_numeric($second) || is_bool($second) || is_string($second) ? $second : null;
            $functions = array('self::compareString', 'self::compareNull', 'self::compareNumeric');
            foreach ($functions as &$function) {
                $result = call_user_func($function, $first, $second);
                if ( $result !== false ) {
                    return $result;
                }
            }
            return false;
        }
        public static function compareString($first, $second) {
            if ( !(is_string($first) && is_string($second)) ) {
                return false;
            }
            return call_user_func(self::$compareString, $first, $second);
        }
        public static function compareNull($first, $second) {
            if ( !(is_null($first) || is_null($second)) ) {
                return false;
            }
            if ( $first === $second ) {
                return 0;
            }
            if ( is_null($first) ) {
                return -1;
            }
            return 1;
        }
        public static function compareNumeric($first, $second) {
            $first = (float) $first;
            $second = (float) $second;
            if ( $first == $second ) {
                return 0;
            }
            if ( $first < $second ) {
                return -1;
            }
            return 1;
        }
    }
    $this_function = null;
	AbstractSort::$compareString = function($first, $second) use (&$this_function, $compareString) {
		$compare = mb_strtoupper($compareString, 'UTF-8') . $compareString;
		
		if ( $first == '' || $second == '' ) {
			return strnatcmp($first, $second);
		}

		$first_chr = mb_substr($first, 0, 1, 'UTF-8');
		$second_chr = mb_substr($second, 0, 1, 'UTF-8');

		$first_str_pos = strpos($compare, $first_chr);
		$second_str_pos = strpos($compare, $second_chr);

		if ( $first_str_pos !== false && $second_str_pos !== false ) {
			if ( $first_str_pos == $second_str_pos ) {
				return $this_function(mb_substr($first, 1), mb_substr($second, 1));
			} else {
				return $first_str_pos < $second_str_pos ? -1 : 1;
			}
		} else {
			return strnatcmp($first, $second);
		}
	};
	$this_function = AbstractSort::$compareString;
}
