<?php
abstract class PHPIO_Hook_Class {
	const classname = '';
	var $log = array();
	var $hooks = array();

	function _preCallback($jp) {
	    $args   = $jp->getArguments();
	    $traces = debug_backtrace();

	    $this->preCallback($args, $traces);
	}

	function _postCallback($jp) {
	    $args   = $jp->getArguments();
	    $traces = debug_backtrace();
	    $result = $jp->getReturnedValue();

	    $this->postCallback($args, $traces, $result);
	}
	
	function preCallback($args, $traces) {
		$traces[1]['object'] = $this->getObjectId($traces[1]['object']);
        $traces[1]['time']   = microtime(true);
		$traces[1]['trace']  = $this->getPrintTrace($traces);
		$traces[1]['class']  = $this::classname;
		
		$this->log[] = $traces[1];
	}
	
	function postCallback($args, $traces, $result) {
		$traces[1]['result'] = $this->getObjectId($result);
        $traces[1]['time']   = microtime(true);
		// pre_log_id
		$i = $this->log->count() - 1;
		$this->log[$i] = $traces[1]+$this->log[$i];
		// add result log
		$this->log[] = $traces[1];
	}
	
	function getPrintTrace($traces) {
		//0  c() called at [/tmp/include.php:10]
		$i = 0;
		$trace_len = count($traces)-1;
		$printTraces = array();
		for ($i = $trace_len; $i >= 1; $i--) {
			if ( isset($traces[$i]['class']) ) {
				$traces[$i]['function'] = $traces[$i]['class'] .'->'.$traces[$i]['function'];
			}
			
			$printTraces[] = sprintf("%s() called at [%s:%d]", $traces[$i]['function'],$traces[$i]['file'],$traces[$i]['line']);
		}
		return $printTraces;
	}
	
	function getObjectId($object) {
        if ( !is_object($object) ) return $object;
        
		ob_start();
        var_dump($object); 
		$object_string = ob_get_clean();
        
		if ( preg_match('|object\((\w+)\)#(\d+)|', $object_string, $match) ) {
			return $match[0];
		}
		return 0;
	}
	
	function init($log) {
		$this->log = $log;
		$this->hooks = $this->getHooks();
		$this->hook();
		
		return $this;
	}
	
	function getHooks() {
		$hooks = array();
		$hooks_file = __DIR__.'/'.$this::classname.'.hooks';
		if ( file_exists($hooks_file) ) {
			$hooks = file($hooks_file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
		} elseif ( !empty($this->hooks) ) {
			array_walk($this->hooks,array($this, 'replaceClassPrefix'));
            $hooks = $this->hooks;
		} else {
			$hooks = $this->getFunctions();
		}
		return $hooks;
	}
    
    function replaceClassPrefix(&$method) {
        $method = str_replace($this::classname.'::', '', $method);
    }
	
	function getFunctions() {
		return get_class_methods($this::classname);
	}
	
	function hook() {
		if ($this->hooks) foreach ( $this->hooks as $func ) {
			$func = $this->getHookFunc($func);
			aop_add_before($func, array($this, '_preCallback'));
			aop_add_after($func, array($this, '_postCallback'));
		}
	}
	
	function getHookFunc($func) {
		return $this::classname . '->' . $func . '()';
	}
}

abstract class PHPIO_Hook_Func extends PHPIO_Hook_Class {
	const classname = '';
	var $log = array();
	var $hooks = array();
	var $link_hooks = array();
	var $link = 0;
	
	function getFunctions() {
		return get_extension_funcs($this::classname);
	}
	
	function preCallback($args, $traces) {
		$traces[1]['object_id'] = (int) $this->getLink($args);
		parent::preCallback($args, $traces);
	}
	
	function postCallback($args, $traces, $result) {
		if ( in_array( $traces[1]['function'], $this->link_hooks ) && 
			 is_resource($result) ) {
			$this->link = $result;
		}
		
		parent::postCallback($args, $traces, $result);
	}
	
	function getLink($args) {
		$link = $args[ count($args)-1 ];
		$link = (is_resource($link) ? $link : $this->link);
		return $link;
	}
	
	function getHookFunc($func) {
		return $func . '()';
	}
}
