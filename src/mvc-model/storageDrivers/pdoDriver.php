<?php
require_once dirname(__FILE__).'/../storageDriver.php';
class pdoDriver extends storageDriver{
	protected function _makeConnection(){
		$this->_connection = new PDO($this->get('dsn'), $this->get('username'), $this->get('password'));
	}
	public function free($result){
		unset($result);
	}
	/**
	 * Execute an SQL statement and return the number of affected rows
	 * @param string $sql
	 */
	public function execute($sql){
		$this->getConnection()->exec($sql);
	}
	/**
	 * Executes an SQL statement, returning a result set
	 * @param string $sql
	 */
	public function query($sql){
		return $this->getConnection()->query($sql);
	}
	public function fetch($resultSet){
		return $resultSet->fetch();
	}
	public function fetchColumn($resultSet, $columnNumber = 0){
		if (is_object($resultSet)){
			return $resultSet->fetchColumn($columnNumber);
		}
		return false;
	}
	public function rowCount($resultSet){
		return $resultSet->rowCount();
	}
	public function quote($string){
		return $this->getConnection()->quote($string);
	}
	public function lastInsertId(){
		$this->getConnection()->lastInsertId();
	}
}