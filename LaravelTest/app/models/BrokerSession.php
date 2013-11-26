<?php

class BrokerSession extends Eloquent {

	protected $table = 'broke_sessions';
	
	protected $guarded = array();

	public static $rules = array();
}
