<?php

namespace rdx\ps;

class Model {

	static protected $table = '';

	static protected $cache = [];

	/**
	 * Methods
	 */

	public function decorate( array $data ) {
		$object = clone $this;
		$object->fill($data);
		return $object;
	}

	public function update( array $data ) {
		global $db;
		$this->presave($data);
		$db->update(static::$table, $data, ['id' => $this->id]);
		$this->fill($data);
	}

	protected function presave( array &$data ) {
		// Alter data pre-save
	}

	protected function fill( array $data = [] ) {
		foreach ( $data as $key => $value ) {
			if ( !is_int($key) ) {
				$this->$key = $value;
			}
		}
	}

	/**
	 * Static
	 */

	static public function first( $conditions, $params = [] ) {
		global $db;
		$options = static::_fetchOptions();
		return $db->select(static::$table, $conditions, $params, $options)->first();
	}

	static public function find( $id ) {
		global $db;
		if ( !isset(self::$cache['find'][get_called_class()][$id]) ) {
			$options = static::_fetchOptions();
			$result = $db->select(static::$table, compact('id'), [], $options)->first();
			self::$cache['find'][get_called_class()][$id] = $result;
		}

		return self::$cache['find'][get_called_class()][$id];
	}

	static public function all( $conditions = '', $params = [] ) {
		global $db;

		// From cache
		if ( !$conditions ) {
			if ( isset(self::$cache['all'][get_called_class()]) ) {
				return self::$cache['all'][get_called_class()];
			}
		}

		$options = static::_fetchOptions();
		$result = $db->select_by_field(static::$table, 'id', $conditions ?: static::_allQuery(), $params, $options)->all();

		// To cache
		if ( !$conditions ) {
			self::$cache['all'][get_called_class()] = $result;
		}

		return $result;
	}

	static protected function _allQuery() {
		return '1';
	}

	static public function _fetchOptions() {
		return ['class' => get_called_class()];
	}

	/**
	 * Magic
	 */

	public function __get( $name ) {
		if ( is_callable($method = [$this, "get_{$name}"]) ) {
			return $this->$name = call_user_func($method);
		}
	}

}
