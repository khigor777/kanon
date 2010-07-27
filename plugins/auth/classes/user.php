<?php
class user extends extendable{
	protected $_isAuthenticated = false;
	protected $_isRegistered = false;
	protected $_credentials = array();
	protected $_identity = null;
	protected $_identityModels = array();
	protected $_user = null;
	protected static $_model = 'registeredUser'; // real model
	/**
	 * Login using valid authenticated identity
	 * @example
	 * $identity=new emailUserIdentity($email,$password);
	 * if ($identity->authenticate()) kanon::app()->user->login($identity);
	 * @param userIdentityPrototype $identity
	 * @param integer $timeout Keep the user logged in for [default is 7 days].
	 */
	public function login($identity, $timeout = 604800){
		$this->_identity = $identity;
		$this->setAuthenticated();
		$identityModels = $identity->getIdentityModels();
		foreach ($identityModels as $type => $a){
			foreach ($a as $id => $model){
				$this->_identityModels[$type][$id] = $model;
			}
		}
		if ($identity->isRegistered()){
			$this->_user = $identity->getUserModel();
			$this->setRegistered();
		}
	}
	public function getIdentity(){
		return $this->_identity;
	}
	/*public function model(){

	}*/
	public static function getCollection(){
		return modelCollection::getInstance(self::$_model);
	}
	public function logout(){
		$this->_identity = null;
		$this->_identityModels = array();
		$this->_user = null;
		$this->setAuthenticated(false);
		$this->setRegistered(false);
		$this->clearCredentials();
		$this->addCredentials('guest');
	}
	public function setAuthenticated($isAuthenticated = true){
		$this->_isAuthenticated = $isAuthenticated;
	}
	public function isAuthenticated(){
		return $this->_isAuthenticated;
	}
	public function register(){
		$this->_user->save();
		/* @var $identityModel model */
		foreach ($this->_identityModels as $identityModel){
			$identityModel->save();
		}
	}
	public function setRegistered($isRegistered = true){
		$this->_isRegistered = $isRegistered;
	}
	public function isRegistered(){
		return $this->_isRegistered;
	}
	public function addCredentials(){ // assign, addCredentials
		$credentials = func_get_args();
		foreach ($credentials as $credential){
			$this->addCredential($credential);
		}
	}
	public function addCredential($credential){
		$this->_credentials[$credential] = true;
		return $this;
	}
	public function hasCredential($credential){
		return isset($this->_credentials[$credential]);
	}
	public function removeCredential($credential){ // revoke, removeCredential
		unset($this->_credentials[$credential]);
		return $this;
	}
	public function clearCredentials(){
		$this->_credentials = array();
		return $this;
	}
	public function getIdentityModels(){
		return $this->_identityModels;
	}
	public function __construct(){
		if (isset($_SESSION['kanon_user'])){
			$u = $_SESSION['kanon_user'];
			//$u = new user();
			$this->_identity = $u->___get('_identity');
			$this->_identityModels = $u->___get('_identityModels');
			$this->_user = $u->___get('_user');
			$this->_isAuthenticated = $u->___get('_isAuthenticated');
		}
	}
	public function __destruct(){
		$_SESSION['kanon_user'] = $this;
	}
}