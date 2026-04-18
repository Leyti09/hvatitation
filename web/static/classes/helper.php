<?php
	class _helper {
        function shortenString($string, $length) {
            if(strlen($string) > $length) {
                return substr($string, 0, $length - 3) . '...';
            }
            return $string;
        }
	}
?>
