<?php
/**
 * It's fine don't worry about it
 */
class Filter {
	// get flag name
	protected static function _flag($f) {
		return constant(
			'FILTER_'
			. ($f === 'null_on_failure' || $f === 'require_array' ? '' : 'FLAG_')
			. strtoupper($f)
		);
	}

	// get full filter config array for a variable
	public static function _options($validate, $id, $flag = null, $opts = null) {
		$const = constant('FILTER' .
			(stripos($id, 'callback') === 0 ? '' : '_' . ($validate ? 'VALIDATE' : 'SANITIZE'))
			. '_' . strtoupper($id)
		);

		if ($flag === null && $opts === null) {
			return $const;
		}

		if ($flag || $opts) {
			$return = ['filter' => $const];
			if ($flag) {$flags = 0;
				if (is_array($flag)) {
					foreach ($flag as $f) {
						$flags |= self::_flag($f);
					}
				} else {
					$flags = self::_flag($flag);
				}

				$return['flags'] = $flags;
			}
			if (is_array($opts) || is_scalar($opts)) {
				$return['options'] = $opts;
			}

			return $return;
		}
		return $const; // no options exist without flags
	}

	public $options  = [];
	public $failsafe = [];

	public function __construct() {}

	public function __call($type, $args) {
		if (empty($args)) {
            if($type === 'addEmail') {
                $args[] = 'email';
            } else {
                trigger_error(__METHOD__ . '('.$type.') requires at least 1 argument');
                return;
            }
		}

        $arrMode = 0;
		if (strstr($type, 'Array')) {
			$type = str_replace('Array', '', $type);
			$arrMode = 1;
		}
        $type = strtolower(str_replace('add', '', $type));

        switch($type) {
		case 'regexp':
		case 'pcre':
        case 'regex':
            if($arrMode)
                throw new Exception('Array Filter type for pcre|regex(p)? is currently unsupported!');
            call_user_func_array([$this, 'regex'], $args);
            return;

        case 'callback':
            if($arrMode)
                throw new Exception('Array Filter type for callback functions is currently unsupported!');
            call_user_func_array([$this, 'callback'], $args);
            return;
        }

		$key = array_shift($args);
		$f = count($args) ? array_shift($args) : null;
		$o = count($args) ? array_shift($args) : [];

		switch ($type) {
		case 'email':
            $v = 1;
			$i = 'email';
			$c = 'string';
			break;

		case 'string':
            $v = 0;
			$i = 'full_special_chars';
			$c = 'string';
			break;

		case 'int':
		case 'integer':
            $v = 1;
			$i = 'int';
			$c = 'int';
			break;

		case 'bool':
		case 'boolean':
            $v = 1;
			$i = 'boolean';
			$c = 'bool';
			break;

		default:
            if($arrMode)
                throw new Exception('Array Filter type must have a type e.g. addIntArray, addBoolArray...');
			throw new Exception('Unknown Filter type "' . $type . '"');
		}

		if ($arrMode) {
			unset($arrMode);
			$f = 'require_array';
			$c = 'array';
		}
		$this->setFailsafe($key, $c);
		$this->add($key, self::_options($v, $i, $f, $o));
	}

	public function setFailsafe($key, $type = -1) {
		switch ($type) {
		case 'string':
            $this->failsafe[$key] = '';
			break;
		case 'int':
            $this->failsafe[$key] = 0;
			break;
		case 'bool':
            $this->failsafe[$key] = false;
			break;
		case 'array':
            $this->failsafe[$key] = [];
			break;
		default:
            $this->failsafe[$key] = null;
		}
	}

	public function add($key, $opts) {
		$this->options[$key] = $opts;
	}

	public function callback($key, $callback, $flags = null) {
		$this->setFailsafe($key, gettype($callback(false)));
		$this->add($key, self::_options(0, 'callback', $flags, $callback));
	}

	public function regex($key, $regex, $flags = null, $opts = []) {
		$this->setFailsafe($key, 'string');
		if (is_scalar($opts)) {
			$opts = [$opts];
		}

		$this->add($key, self::_options(1, 'regexp', $flags, array_merge($opts, ['regexp' => $regex])));
	}

	public function stringArray($key) {
		$this->add($key, self::_options(0, 'string', 'require_array'));
		$this->setFailsafe($key, 'array');
	}

	public function input_array() {
		return $this->apply('input', 'post');
	}

	public function var_array($var_array) {
		return $this->apply('var', $var_array);
	}

	protected function apply($function = 'input', $target = 'post', $empty = '') {
		switch ($function) {
		case 'input':
			$function = 'filter_input_array';
			$target = $target === 'get' ? INPUT_GET : INPUT_POST;
			break;

		case 'var':
			$function = 'filter_var_array';
			if (!is_array($target)) {
				throw new InvalidArgumentException('filter_var_array()'
					. ' expects array argument, '
					. strtoupper(gettype($target)) . ' given.');
			}
			break;

		default:
			throw new InvalidArgumentException(__METHOD__
				. ' only does filter_(input|var)_array');
		}

		$result = $function($target, $this->options, false);

		if (count(array_keys($result)) < count($this->options)) {
			$ret = $this->failsafe;
			foreach ($result as $k => $v) {
				if (gettype($v) === gettype($ret[$k])) {
					$ret[$k] = $v;
				}

			}
			$result = $ret;
		}

		return $result;
	}
}
// all this so I can get an empty, keyed and typed array for blank forms
// ._.;
