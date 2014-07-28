<?php

function db_connect( $h, $u, $p, $d ) {
	$db = new mysqli($h, $u, $p, $d);
	return $db;
}

function db_set( $c ) {
	global $g_db;
	$g_db = $c;
	return $c;
}

function db_insert_id() {
	global $g_db;
	return $g_db->insert_id;
}

function db_affected_rows() {
	global $g_db;
	return $g_db->affected_rows;
}

function db_error() {
	global $g_db;
	return $g_db->error;
}

function db_errno() {
	global $g_db;
	return $g_db->errno;
}

function db_select( $tbl, $where = '' ) {
	return db_fetch('SELECT * FROM ' . $tbl . ( $where ? ' WHERE ' . $where : '' ));
}

function db_fetch( $query ) {
	$r = db_query($query);
	if ( !is_object($r) ) {
		return false;
	}

	$a = array();
	while ( $l = $r->fetch_assoc() ) {
		$a[] = $l;
	}

	return $a;
}

function db_fetch_fields( $query ) {
	$r = db_query($query);
	if ( !is_object($r) ) {
		return false;
	}

	$a = array();
	while ( $l = $r->fetch_row() ) {
		$a[$l[0]] = $l[1];
	}

	return $a;
}

function db_select_one( $tbl, $field, $where = '' ) {
	$query = 'SELECT ' . $field . ' FROM ' . $tbl . ( $where ? ' WHERE ' . $where : '' );
	$r = db_query($query);

	if ( !is_object($r) || 0 >= $r->num_rows ) {
		return false;
	}

	$row = $r->fetch_row();
	return $row[0];
}

function db_max( $tbl, $field, $where = '' ) {
	return db_select_one($tbl, 'MAX(' . $field . ')', $where);
}

function db_min( $tbl, $field, $where = '' ) {
	return db_select_one($tbl, 'MIN(' . $field . ')', $where);
}

function db_count( $tbl, $where = '' ) {
	return db_select_one($tbl, 'COUNT(1)', $where);
}

function db_select_by_field( $tbl, $field, $where = '' ) {
	$r = db_query('SELECT * FROM ' . $tbl . ( $where ? ' WHERE ' . $where : '' ));
	if ( !is_object($r) ) {
		return false;
	}

	$a = array();
	while ( $l = $r->fetch_assoc() ) {
		$a[$l[$field]] = $l;
	}

	return $a;
}

function db_select_fields( $tbl, $fields, $where = '' ) {
	$query = 'SELECT ' . $fields . ' FROM ' . $tbl . ( $where ? ' WHERE ' . $where : '' );
	return db_fetch_fields($query);
}

function _db_escape_values( $values ) {
	foreach ( $values AS $k => $v ) {
		if ( $v === null ) {
			$values[$k] = 'NULL';
		}
		else if ( is_bool($v) ) {
			$values[$k] = "'" . (int)$v . "'";
		}
		else if ( 'NOW()' == $v ) {
			$values[$k] = 'NOW()';
		}
		else {
			$values[$k] = "'" . $g_db->real_escape_string($v) . "'";
		}
	}

	return $values;
}

function db_replace_into( $tbl, $values ) {
	$values = _db_escape_values($values);
	return db_query('REPLACE INTO ' . $tbl . ' (`' . implode('`, `', array_keys($values)) . '`) VALUES (' . implode(", ", $values) . ')');
}

function db_insert($tbl, $values) {
	$values = _db_escape_values($values);
	return db_query('INSERT INTO ' . $tbl . ' (`' . implode('`, `', array_keys($values)) . '`) VALUES (' . implode(", ", $values) . ')');
}

function db_update( $tbl, $update, $where = '' ) {
	if ( !is_string($update) ) {
		$values = _db_escape_values($update);

		$update = '';
		foreach ( $values AS $k => $v ) {
			$update .= ', ' . $k . ' = ' . $v;
		}
		$update = substr($update, 2);
	}

	return db_query('UPDATE ' . $tbl . ' SET ' . $update . ( $where ? ' WHERE ' . $where : '' ));
}

function db_delete( $tbl, $where ) {
	return db_query('DELETE FROM ' . $tbl . ' WHERE ' . $where);
}

function db_query( $query ) {
	global $g_db, $g_iQueries, $g_arrQueries;
	$r = @$g_db->query($query);

	// Log error
	// if ( !$r ) {
	// 	static $log;
	// 	if ( !$log ) $log = fopen(PROJECT_LOGS.'/sqlerrors.log', 'a');
	// 	fwrite($log, $query."\r\n".db_error()."\r\n\r\n");
	// }

	@$g_iQueries++;
	@$g_arrQueries[] = $query;

	return $r;
}
