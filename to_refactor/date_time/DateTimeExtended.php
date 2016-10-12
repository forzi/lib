<?
namespace stradivari\date_time {
	class DateTimeExtended extends DateTime {
		const NEXT = 1;
		const CURRENT = 0;
		const PREVIOUS = -1;
		public static $inPlace = true;
		
		public static function validate($date, $format = self::DATE_TIME) {
			$obj = new static($date, $format);
			return $obj && $obj->format($format) == $date;
		}
		public function isLeapYear() {
			return (boolean) $this->format('L');
		}
		public function firstDayOfMonth($monthOffset = 0) {
			$obj = self::$inPlace ? $this : clone $this;
			$obj->day = 1;
			$obj->month += $monthOffset;
			return $obj;
		}
		public function lastDayOfMonth($monthOffset = 0) {
			$obj = $this->firstDayOfMonth($monthOffset);
			$obj->month += 1;
			$obj->day -= 1;
			return $obj;
		}
		public function firstDayOfQuarter($quarterOffset = 0) {
			$obj = self::$inPlace ? $this : clone $this;
			$obj->day = 1;
			$obj->quarter += $quarterOffset;
			$obj->month -= ($obj->month - 1) % 3;
			return $obj;
		}
		public function lastDayOfQuarter($quarterOffset = 0) {
			$obj = $this->firstDayOfQuarter($quarterOffset);
			$obj->lastDayOfMonth(2, true);
			return $obj;
		}
		public function beginOfDay($dayOffset = 0) {
			return $this->timeOfDay(0, 0, 0, $dayOffset);
		}
		public function midOfDay($dayOffset = 0) {
			return $this->timeOfDay(12, 0, 0, $dayOffset);
		}
		public function endOfDay($dayOffset = 0) {
			return $this->timeOfDay(23, 59, 59, $dayOffset);
		}
		protected function timeOfDay($hours, $minutes, $seconds, $dayOffset = 0) {
			$obj = self::$inPlace ? $this : clone $this;
			$obj->setTime($hours, $minutes, $seconds);
			$obj->day += $dayOffset;
			return $obj;
		}
		public function daysInMonth() {
			return cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
		}
	}
}
