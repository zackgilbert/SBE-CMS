<?php

	class db {
		var $isConnected = false;
		var $connection = false;
		var $type = DB_TYPE;
		var $host = DB_HOST;
		var $database = DB_DATABASE;
		var $username = DB_USERNAME;
		var $password = DB_PASSWORD;
		var $last_id = false;
		var $queries = array();
		var $tables = false;

		function db() {
			$args = args(func_get_args());
			foreach ($args as $argKey => $argValue) {
				$this->$argKey = $argValue;
			}
		} // db constructor
		
		function close() {
			if (isset($this->connection) && $this->connection && is_resource($this->connection)) {
				switch ($this->type) {
					case "mysql":
					default:
						//mysql_free_result($this->connection);
						return @mysql_close($this->connection);
				}
			} else {
				return true;
			}
		} // close
		
		function connect() {
			if (!$this->isConnected) {
				switch($this->type) {
					case 'mysql':
					default: 
						$this->connection = @mysql_connect($this->host, $this->username, $this->password);
				}

				if (!$this->connection) {
					$this->isConnected = false;
					$this->error("error=>Unable to make " . $this->type . " database connection.", "function=>".__FUNCTION__, "line=>".__LINE__, "host=>" . $this->host);
				} else {
					$this->isConnected = true;
				}
			}
			
			if ($this->isConnected) {
				switch($this->type) {
					case 'mysql':
					default:
						$dbSelected = @mysql_select_db($this->database, $this->connection);
				}
				if (!$dbSelected) {
					$this->error("error=>Unable to select database.", "function=>".__FUNCTION__, "line=>".__LINE__, "connection=>" . $this->connection, "host=>" . $this->type, "database=>"  . $this->database);
					return false;
				}
			}
			
			return $this->isConnected;
		} // connect		
		
		function count($table, $where) {
			$args = args(func_get_args());
			
			$table = (isset($args['table'])) ? $args['table'] : $table;
			$conditions = " WHERE " . $this->createWhere($args);
			if ($conditions == " WHERE ") $conditions = "";
			$return = "COUNT(id)";

			if (DEBUG_SQL && !$this->isTable("table=>" . $table)) {
				return $this->error('error=>Table (' . $table . ') does not exist.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
			}
			
			$sql = sprintf("SELECT " . $return . " FROM `%s`%s", $table, $conditions);

			if (!function_exists('get_memcache') || (!$result = get_memcache($sql))) {
				$result = $this->execute($sql);
				$result = $this->resourceToArray($result);
				if (function_exists('set_memcache'))
					set_memcache('sql://' . $this->database . "/" . md5($sql), $result);
			}
			
			return $result[0]['COUNT(id)'];
		} // count
		
		function createWhere($args) {
			$conditions = "";
			if (isset($args['id'])) {
				$this->last_id = $args['id'];
				$conditions = "(`id` = " . $args['id'] . ")";
			} else if (isset($args['where'])) {
				$conditions = $args['where'];
			} else {
				$params = array();
				foreach($args as $argKey => $argValue)
					if (!in_array($argKey, array('table', 'order', 'return', 'limit', 'property', 'properties')))
						$params[] = "`" . $argKey . "` = " . ((is_numeric($argValue)) ? $argValue : "'" . $argValue . "'");
				if (count($params) > 0)
					$conditions = "(" . join(") AND (", $params) . ")";
			}
			return $conditions;
		} // createWhere
		
		function delete($table, $where, $mustExist = true) {
			$args = args(func_get_args());
			$table = (isset($args['table'])) ? $args['table'] : $table;
			$this->last_id = false;
			
			if (isset($args['where'])) {
				$where = $args['where'];
			} else if (isset($args['id'])) {
				$this->last_id = $args['id'];
				$where = "(`id` = " . $args['id'] . ")";
			} else if (is_numeric($where)) {
				$this->last_id = $where;
				$where = ("(`id` = " . $where . ")");
			}
			
			if (!$where || !$table) {
				return $this->error('error=>Required information was not supplied.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
			}
			if (DEBUG_SQL && !$this->isTable("table=>" . $table)) {
				return $this->error('error=>Table (' . $table . ') does not exist.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
			}
			
			$item = $this->getOne("table=>" . $table, "where=>" . $where);
		
			if ((!isset($item) || !$item) && $mustExist) {
				return $this->error('error=>Item does not exist in database.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'item=>' . var_export($item, true), 'arguments=>' . var_export($args, true));
			}
			
			if ($item) {
				
				//dump($this->isField($table, 'deleted_at'));
				//dump(isset($item['deleted_at']));
				if (DB_DELETED_AT || $this->isField($table, 'deleted_at')) {
					return $this->update("table=>" . $table, "where=>" . $where, array("deleted_at" => NOW));
				} else {
					return $this->remove("table=>" . $table, "where=>" . $where);
				}
			} else {
				return !$mustExist;
			}
		} // delete
		
		function error() {
			global $log;
			$args = args(func_get_args());
			$error = '';
			
			switch ($this->type) {
				case "mysql":
				default: 
					$error .= ('	MySQL Error: ' . mysql_error() . "<br/>\n");
			}
			foreach ($args as $argKey => $argValue) {
				$error .= ('	' . $argKey . ': ' . var_export($argValue, true) . "<br/>\n");
			}
			if ((count($this->queries) > 0) && isset($this->queries[count($this->queries)-1]))
				$error .= ('Last Query: ' . $this->queries[count($this->queries)-1] . "<br/>\n");
			
			$log->log($error);
			return false;
		} // error
		
		function escape($arg) {
			if (!is_string($arg)) return $arg;//$this->error('error=>Non string supplied to escape function.', "function=>".__FUNCTION__, "line=>".__LINE__, "arguments=>" . var_export($arg, true));
			$arg = clean($arg);
			//$arg = addslashes($arg);
			$arg = convert_smart_quotes($arg);
			return mysql_real_escape_string($arg);//($this->connection) ? mysql_real_escape_string($arg) : $arg;
		} // escape
		
		function execute($query, $isMultiple = false) {
			if (!$this->isConnected) $this->connect();
			$this->queries[] = $query;
			
			if (DEBUG_SQL) {
				$sqlLogger = new logger(LOGS . "mysql-calls", false);
				$sqlLogger->log($this->queries[count($this->queries)-1]);
			}
			
			if ($isMultiple) {
				if ($this->type == 'mysql') {
					$qs = trim_explode(';', $query);
					// need to figure out a good way to check multiple statements... with possibility to include ;
					mysql_query("START TRANSACTION", $this->connection);
					mysql_query("BEGIN", $this->connection);

					foreach ($qs as $q) {
						if (!empty($q) && (!$resource = mysql_query($q, $this->connection))) {
							$this->error("error=>Unable to complete SQL call.", "function=>".__FUNCTION__, "line=>".__LINE__, "connection=>" . $this->connection, "resource=>" . $resource, "query=>"  . $q);
							break;
						}
					}

					if (!$resource) {
						mysql_query("ROLLBACK", $this->connection);
					} else {
						mysql_query("COMMIT", $this->connection);
					}
				}
			} else {
				switch ($this->type) {
					case "mysql":
					default:
						$resource = mysql_query($query, $this->connection);
				}
				if (!$resource) {
					$this->error("error=>Unable to complete SQL call.", "function=>".__FUNCTION__, "line=>".__LINE__, "connection=>" . $this->connection, "resource=>" . $resource, "query=>"  . $query);
					return false;
				}
				if (substr(strtolower($query), 0, strlen('INSERT INTO')) == strtolower('INSERT INTO')) {
					$this->setInsertId();
				}
			}
			
			return $resource;
		} // execute
		
		function fields($table = false) {
			$args = args(func_get_args());
			$table = (isset($args['table'])) ? $args['table'] : $table;
			if (!$table) return $this->error('error=>Table was not supplied.', "function=>".__FUNCTION__, "line=>".__LINE__, 'arguments=>' . var_export($args, true));

			if ($table = $this->isTable($table)) {
				$result = $this->execute(sprintf("SHOW COLUMNS FROM `%s`;", $table));
				return $this->resourceToArray($result);
			} else {
				return $this->error('error=>Could not find table (' . $table . ')', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
			}
		} // fields
		
		function get($sql) {
			if (!$this->isSQL($sql)) {
				$args = args(func_get_args());
				
				if (!isset($args['table']) || !is_string($args['table'])) return false;
				$table = $args['table'];
				$order = (isset($args['order'])) ? " ORDER BY " . $args['order'] : "";
				$return = (isset($args['return'])) ? $args['return'] : "*";
				$limit = (isset($args['limit'])) ? " LIMIT " . $args['limit'] : '';
				$conditions = " WHERE " . $this->createWhere($args);
				if ($conditions == " WHERE ") $conditions = "";

				if (DEBUG_SQL && !$this->isTable("table=>" . $table)) {
					return $this->error('error=>Table (' . $table . ') does not exist.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
				}

				$sql = sprintf("SELECT " . $return . " FROM `%s`%s%s%s", $table, $conditions, $order, $limit);
			}
			
			// lookup value in memcache if available
			if (!function_exists('get_memcache') || (!$result = get_memcache($sql))) {
				// fetch from database
				$result = $this->execute($sql);
				$result = $this->resourceToArray($result);				
				// store in memcache if it's available
				if (function_exists('set_memcache'))
					set_memcache('sql://' . $this->database . "/" . md5($sql), $result);
			}
			
			return $result;
		} // get
		
		/*** DEPRECATED ***/
		function getInsertId() {
			return $this->last_id;
		} // getInsertId

		function getOne($sql) {
			if ($this->isSQL($sql)) {
				$items = $this->get($sql);
			} else {
				$args = args(func_get_args());
				$table = (isset($args['table'])) ? "table=>" . $args['table'] : "table=>" . $sql;
				$conditions = "where=>" . $this->createWhere($args);
				$order = (isset($args['order'])) ? "order=>" . $args['order'] : "";
				$return = (isset($args['return'])) ? "return=>" . $args['return'] : "";
				$limit = 'limit=>1';

				$items = $this->get($table, $conditions, $order, $return, $limit);
			}

			return (isset($items[0])) ? $items[0] : false;
		} // getOne
		
		function getProperty($sql, $property) {
			if ($this->isSQL($sql)) {
				$item = $this->getOne($sql);
				return (isset($item[$property])) ? $item[$property] : false;
			} else {
				$args = args(func_get_args());

				$table = "table=>" . $args['table'];
				$conditions = "where=>" . $this->createWhere($args);
				$order = (isset($args['order'])) ? "order=>" . $args['order'] : "";

				if (isset($args['property'])) {
					$return = "return=>" . $args['property'];
				} else if (isset($args['return'])) {
					$return = "return=>" . $args['return'];
				} else {
					return $this->error("error=>Property was not supplied.", "function=>".__FUNCTION__, "line=>".__LINE__, "arguments=>" . var_export($args, true));
				}

				$item = $this->getOne($table, $conditions, $order, $return);
				$prop = (isset($args['property'])) ? 'property' : 'return';

				return (isset($item[$args[$prop]])) ? $item[$args[$prop]] : false;
			}
		} // getProperty
		
		function getRandomId($table, $condition = '') {
			$args = args(func_get_args());
			
			$table = (isset($args['table'])) ? $args['table'] : $table;
			$conditions = " WHERE " . $this->createWhere($args);
			if ($conditions == " WHERE ") $conditions = "";
			
			if ($this->isTable($table)) {
				$query = "SELECT MAX(id) AS max_id , MIN(id) AS min_id FROM `" . $table . "`" . $conditions . ";";
				$range_row = $this->execute($query); // THIS MAY NEED TO BE CHECKED!! FETCH ROW?
				$item =  false;
				while (!$item) {
					$random = mt_rand($range_row[0]['max_id'],$range_row[0]['min_id']);
					$item = $this->getOne("table=>".$table, "id=>".$random);
				}
			} else {
				$random = false;
			}
			return $random;
		}
		
		function insert($table, $fields) {
			$args = args(func_get_args());
			
			$table = (isset($args['table'])) ? $args['table'] : $table;
			$fields = (isset($args['fields'])) ? $args['fields'] : $fields;
			
			$this->last_id = false;

			if (!$table) return $this->error('error=>Required information was not supplied.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
			if (DEBUG_SQL && !$this->isTable("table=>" . $table)) return $this->error('error=>Table (' . $table . ') does not exist.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
			
			// build keys/values
			foreach ($fields as $field => $value) {
				$insertFields[] = "`".$field."`";
				if (($field == 'id') || (substr($field,-3) == "_id") && is_numeric($value)) {
					$insertValues[] = $this->escape($value);
				} else if ($value === NULL) {
					$insertValues[] = "NULL";
				} else {
					$insertValues[] = "'" . $this->escape($value) . "'";
				}
			}
			if (!isset($fields['created_at'])) {
				$insertFields[] = 'created_at';
				$insertValues[] = "'" . NOW . "'";
			}
			
			$sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s);", $table, join(", ", $insertFields), join(", ", $insertValues));
			$result = $this->execute($sql);
			if (function_exists('flush_memcache'))
				flush_memcache();
			return $result;
		} // insert
		
		function isField($table, $field) {
			$args = args(func_get_args());
			$table = (isset($args['table'])) ? $args['table'] : $table;
			$field = (isset($args['field'])) ? $args['field'] : $field;
			
			if ($this->isTable($table)) {
				$fieldsToSearch = $this->fields($table);
				foreach ($fieldsToSearch as $fieldToSearch) {
					if ($fieldToSearch['Field'] == $field) return true;
				}
			}
			return false;
		} // isField
		
		function isSQL($sql) {
			if ((strpos(strtolower($sql), "select ") >= 0) && (strpos(strtolower($sql), " from ") > 0)) {
				return true;
			} else if ((strpos(strtolower($sql), "insert into ") === 0) && (strpos(strtolower($sql), " values ") > 0)) {
				return true;
			} else if ((strpos(strtolower($sql), "update ") === 0) && (strpos(strtolower($sql), " set ") > 0)) {
				return true;
			} else if ((strpos(strtolower($sql), "union ") > 0) && (strpos(strtolower($sql), "select ") > 0)) {
				return true;
			}
			return false;
		} // isSQL

		function isTable($table = false) {
			$args = args(func_get_args());
			$table = (isset($args['table'])) ? $args['table'] : $table;
			if (!$table) return $this->error("error=>Table name was not supplied.", "function=>".__FUNCTION__, "line=>".__LINE__, "arguments=>" . var_export($args, true));
			
			$tablesToSearch = $this->tables($table);
			foreach ($tablesToSearch as $t) {
				if ($table == $t) 
					return $table;
			}
			return false;
		} // isTable
		
		function nextRow($resource, $useBoth = false) {
		 	if (is_resource($resource)) {
				switch ($this->type) {
					case "mysql":
					default:
						$row = mysql_fetch_array($resource, (($useBoth) ? MYSQL_BOTH : MYSQL_ASSOC));
				}				
				return $row;
			} else {
				return $this->error("error=>Invalid resource supplied.", "function=>".__FUNCTION__, "line=>".__LINE__, "connection=>" . $this->connection, "resource=>" . $resource);
			}
		} // nextItem
		
		function remove($table, $id) {
			$args = args(func_get_args());

			$table = (isset($args['table'])) ? $args['table'] : $table;
			$id = (isset($args['id'])) ? $args['id'] : $id;
			$this->last_id = $id;

			if (!$id || !$table || empty($table) || empty($id)) {
				return $this->error('error=>Required information was not supplied.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export(func_get_args(), true));
			}
			if (DEBUG_SQL && !$this->isTable("table=>" . $table)) {
				return $this->error('error=>Table (' . $table . ') does not exist.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export(func_get_args(), true));
			}
			
			$conditions = "`id` = " . $id;
			$item = $this->getOne("table=>" . $table, "where=>(" . $conditions . ")");
			
			if (!isset($item) || !$item) {
				return $this->error('error=>Item does not exist in database.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'item=>' . var_export($item, true), 'arguments=>' . var_export($args, true));
			}
			
			$sql = sprintf("DELETE FROM `%s` WHERE (%s) LIMIT 1;", $table, $conditions);
			$result = $this->execute($sql);
			if (function_exists('flush_memcache'))
				flush_memcache();
			return $result;
		} // remove
		
		function resourceToArray($resource, $useBoth = false) {
			if (is_resource($resource)) {
				$results = array();
				while ($item = $this->nextRow($resource, $useBoth)) {
					$results[] = $item;
				}
			} else if (is_array($resource) || $resource) {
				$results = $resource;
			} else {
				return $this->error("error=>Invalid resource supplied.", "function=>".__FUNCTION__, "line=>".__LINE__, "connection=>" . $this->connection, "resource=>" . $resource);
			}
			
			return $results;
		} // resourceToArray
		
		function run($sql) {
			return $this->execute($sql);
		} //run
		
		function save($table, $fields) {
			$args = args(func_get_args());
			
			$table = (isset($args['table'])) ? $args['table'] : $table;
			$fields = (isset($args['fields'])) ? $args['fields'] : $fields;
			$id = (isset($args['id'])) ? $args['id'] : ((isset($fields['id'])) ? $fields['id'] : false);

			if (DEBUG_SQL && !$this->isTable("table=>" . $table)) return $this->error('error=>Table (' . $table . ') does not exist.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
			
			if ($id) {
				return $this->update("table=>" . $table, "id=>" . $id, array("fields" => $fields));
			} else {
				return $this->insert("table=>" . $table, array("fields" => $fields));
			}
		} // save
		
		function setInsertId() {
			switch($this->type) {
				case 'mysql':
				default:
					$this->last_id = mysql_insert_id($this->connection);
			}
			return $this->last_id;
		} // setInsertId

		function tables() {
			$args = args(func_get_args());
			$prefix = (isset($args['prefix'])) ? $args['prefix'] : '';
			$suffix = (isset($args['suffix'])) ? $args['suffix'] : '';
			$sql = "SHOW TABLES FROM `" . $this->database . "`;";
			
			if ($this->tables === false) {
				//if (!function_exists('get_memcache') || (!$tableResource = get_memcache($sql))) {
					$tableResource = $this->execute($sql);
					$tableResource = $this->resourceToArray($tableResource, true);
				//}
				$this->tables = array("when" => NOW, "tables" => $tableResource);
			} else {
				$tableResource = $this->tables['tables'];
			}

			if ($tableResource !== false) {
				$tables = array();

				foreach ($tableResource as $table) {
					if (((substr($table[0],0,strlen($prefix)) == $prefix) || ($prefix == '')) && ((substr($table[0],(strlen($suffix)*-1)) == $suffix) || ($suffix == ''))) {
						$tables[] = $table[0];
					}
				}
				return $tables;
			} else {
				return false;
			}
		} // tables
		
		function update($table, $id, $fields) {
			$args = args(func_get_args());
			
			$this->last_id = false;
			$table = (isset($args['table'])) ? $args['table'] : $table;
			$where = (isset($args['where'])) ? $args['where'] : ("(`id` = " . ((isset($args['id'])) ? $this->escape($args['id']) : $this->escape($id)) . ")");
			//$id = (isset($args['id'])) ? $this->escape($args['id']) : substr($id, 4);
			$fields = (isset($args['fields'])) ? $args['fields'] : $fields;
			
			if (!$where || !$table) return $this->error('error=>Required information was not supplied. Please supply both table and where clause of record to be updated.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
			if (DEBUG_SQL && !$this->isTable("table=>" . $table)) return $this->error('error=>Table (' . $table . ') does not exist.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'arguments=>' . var_export($args, true));
			
			if (isset($args['id']) && is_numeric($args['id']))
				$this->last_id = $args['id'];
			elseif (is_numeric($id))
				$this->last_id = $id;
			
			if (DEBUG_SQL) {
				$item = $this->get("table=>" . $table, "where=>" . $where);			
				if (!$item || !is_array($item)) return $this->error('error=>Item does not exist in database.', "function=>".__FUNCTION__, "line=>".__LINE__, 'connection=>' . $this->connection, 'item=>' . var_export($item, true), 'arguments=>' . var_export($args, true));
			}
			
			// build values
			$values = array();
			foreach ($fields as $field => $value) {
				if ($field != 'id') {
					if ((substr($field,-3) == "_id") && is_numeric($value)) {
						$values[] .= "`".$field."` = " . $this->escape($value);
					} else if ($value === NULL) {
						$values[] .= "`".$field."` = NULL";
					} else {
						$values[] .= "`".$field."` = '" . $this->escape($value) . "'";
					}
				}
			}
			if (!isset($fields['updated_at'])) $values[] = "`updated_at` = '" . NOW . "'";
			
			$sql = sprintf("UPDATE `%s` SET %s WHERE (%s);", $table, join(", ", $values), $where);
			
			$result = $this->execute($sql);
			if (function_exists('flush_memcache'))
				flush_memcache();
			return $result;
		} // update
		
	} // class db
	
	if (!isset($db)) {
		require_once(CONFIG . "database.php");
	
		global $db;
		$db = new db('localhost=>' . DB_HOST, 'database=>' . DB_DATABASE, 'username=>' . DB_USERNAME, 'password=>' . DB_PASSWORD);
	
		register_shutdown_function('stop_database');
	}
	
	function stop_database() {
		global $db;
		@$db->close();		// make sure to close any database connections.	
	}

?>