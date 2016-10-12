<?
namespace stradivari\date_time {
	class DateTime extends \DateTime {
		const BASE_FORMAT	= 'Y-m-d H:i:s P';
		const DATE_TIME		= 'Y-m-d H:i:s';
		
		public static $defaultFormat = 'U';
		public static $week = array(
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday',
			7 => 'Sunday',
		);
		/*
		public static $week = array(
			0 => 'Sunday',
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday',
		);
		*/
		
		public function __construct($value = null, $format = 'U') {
			if ( $value == null ) {
				parent::__construct();
				return;
			}
			if ( $value instanceof \DateTime ) {
				parent::__construct($value->format(self::BASE_FORMAT));
				return;
			}
			$obj = date_create_from_format($format, $value, new \DateTimeZone('GMT'));
			if ( !$obj ) {
				throw new exception\IncorrectFormat();
			}
			$this->__construct($obj);
		}
		public function __get($name) {
			if ( $name == 'timestamp' ) {
				return ( int ) $this->format('U');
			}
			if ( $name == 'year' ) {
				return ( int ) $this->format('Y');
			}
			if ( $name == 'month' ) {
				return ( int ) $this->format('n');
			}
			if ( $name == 'day' ) {
				return ( int ) $this->format('j');
			}
			if ( $name == 'hours' ) {
				return ( int ) $this->format('G');
			}
			if ( $name == 'minutes' ) {
				return ( int ) $this->format('i');
			}
			if ( $name == 'seconds' ) {
				return ( int ) $this->format('s');
			}
			if ( $name == 'yday' ) {
				return ( int ) $this->format('z');
			}
			if ( $name == 'week' ) {
				return ( int ) $this->format('W');
			}
			if ( $name == 'weekday' ) {
				return $this->format('l');
			}
			if ( $name == 'timezone' ) {
				return $this->format('P');
			}
			if ( $name == 'offset' ) {
				return $this->getOffset();
			}
			if ( $name == 'wday' ) {
				return array_search($this->weekday, static::$week);
			}
			if ( $name == 'quarter' ) {
				return floor(($this->month - 1) / 3) + 1;
			}
			return null;
		}
		public function __set($name, $value) {
			if ( $name == 'timezone' ) {
				$this->setTimezone($value);
				return $this->timezone;
			}
			if ( $name == 'timestamp' ) {
				$this->setTimestamp($value);
				return $this->timestamp;
			}
			if ( $name == 'year' ) {
				$this->setDate($value, $this->month, $this->day);
				return $this->year;
			}
			if ( $name == 'month' ) {
				$this->setDate($this->year, $value, $this->day);
				return $this->month;
			}
			if ( $name == 'day' ) {
				$this->setDate($this->year, $this->month, $value);
				return $this->day;
			}
			if ( $name == 'hours' ) {
				$this->setTime($value, $this->minutes, $this->seconds);
				return $this->hours;
			}
			if ( $name == 'minutes' ) {
				$this->setTime($this->hours, $value, $this->seconds);
				return $this->minutes;
			}
			if ( $name == 'seconds' ) {
				$this->setTime($this->hours, $this->minutes, $value);
				return $this->seconds;
			}
			if ( $name == 'wday' ) {
				$value -= $this->wday;
				$this->day += $value;
				return $this->wday;
			}
			if ( $name == 'yday' ) {
				$value -= $this->yday;
				$this->day += $value;
				return $this->yday;
			}
			if ( $name == 'quarter' ) {
				$value -= $this->quarter;
				$this->month += $value * 3;
				return $this->month;
			}
			if ( $name == 'weekday' ) {
				$value = array_search($value, static::$week);
				$this->wday = $value;
				return $this->weekday;
			}
			if ( $name == 'week' ) {
				$value -= $this->week;
				$this->day += $value * 7;
				return $this->week;
			}
			throw new exception\CanNonSet();
		}
		public function setTimezone($timezone) {
			if ( !($timezone instanceof \DateTimeZone) ) {
				$timezone = new \DateTimeZone($timezone);
			}
			parent::setTimezone($timezone);
			$this->setTimestamp($this->getTimestamp());
			return $this;
		}
		public function __toString() {
			return $this->format();
		}
		public function format($format = null) {
			$format = $format ?: static::$defaultFormat;
			return parent::format($format);
		}
	}
}
