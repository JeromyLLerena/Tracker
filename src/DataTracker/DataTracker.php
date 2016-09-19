<?php

namespace DataTracker;

class DataTracker
{
	public static function trace() {
		$trace = debug_backtrace();
		$len   = count($trace);
		$stack = [];
		$order = 0;

		foreach ($trace as $key => $item) {
			if (array_key_exists("class", $item)) {
				$item["order"] = $order;
				if (strpos($item["class"], app()->getNamespace()) !== false || $key == $len - 1) {

					if(isset($item["function"])) {
						$class_text =  $item["class"] . "::" . $item["function"] . "()";
						$item["class"] = wordwrap($class_text, 80, "\n", true);
						unset($item["function"]);
					}
					unset($item["line"]);
					unset($item["object"]);
					unset($item["type"]);
					unset($item["file"]);
					$args = $item["args"];

					foreach ($args as $key => $arg) {
						if (!is_array($arg) && get_class($arg) != 'stdClass') {
							if (get_class($arg) == 'Illuminate\Http\Request' || get_parent_class($arg) == 'Illuminate\Http\Request' || get_parent_class($arg) == 'Illuminate\Foundation\Http\FormRequest') {
								$item["args"][$key] = [
									'url' => $arg->url(),
									'inputs' => $arg->all(),
									'method' => $arg->getMethod()
								];
								if(get_parent_class($arg) == 'Illuminate\Foundation\Http\FormRequest') {
									$item["args"][$key]['class'] = get_class($arg);
								}
							} elseif (get_class($arg) == 'Closure'){
								unset($item["args"][$key]);
							}
						}
					}

					$encoded_args = var_export($item["args"], true) . "\n";
					$item["args"] = wordwrap($encoded_args , 70, "\n", true);
					krsort($item);
					$order++;
					array_unshift($stack, $item);
				}
			}
		}

		for ($i = 0; $i < count($stack); $i++) {
			$stack[$i]['order'] = $i;
		}

		\Cache::forever('trace', $stack);

		return $stack;
	}
}