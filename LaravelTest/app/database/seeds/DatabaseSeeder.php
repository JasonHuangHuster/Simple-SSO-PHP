<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');

	
		
	
		$this->call('TtsTableSeeder');
		$this->call('TweetsTableSeeder');
		$this->call('BlogsTableSeeder');
		$this->call('UsersTableSeeder');
	}

}