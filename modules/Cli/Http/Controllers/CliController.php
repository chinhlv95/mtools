<?php namespace Modules\Cli\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class CliController extends Controller {
	
	public function index()
	{
		return view('cli::index');
	}
	
}