<?php

use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Model\Projects::create([
        	'name' => 'General',
        ]);
        App\Model\Projects::create([
        	'name' => 'Tutorial',
        ]);
    }
}
