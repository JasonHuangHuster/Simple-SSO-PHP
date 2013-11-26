<?php

class User extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'staffCode' => 'required',
		'password' => 'required',
		'name' => 'required',
		'gender' => 'required',
		'ip' => 'required',
		'email' => 'required',
		'info' => 'required'
	);
}
