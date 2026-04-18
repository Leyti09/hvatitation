<?php
	class _time {
		public function timeAgo($datetime) {
			$timestamp = strtotime($datetime);
			$timeDifference = time() - $timestamp;
			
			$timeIntervals = [
				31536000 => 'year',
				2592000 => 'month',
				604800 => 'week',
				86400 => 'day',
				3600 => 'hour',
				60 => 'minute',
				1 => 'second',
			];
			
			foreach ($timeIntervals as $seconds => $unit) {
				if ($timeDifference < $seconds) continue;

				$count = floor($timeDifference / $seconds);
				return $count === 1 ? "1 $unit ago" : "$count {$unit}s ago";
			}

			return "just now"; 
		}
	}
?>
