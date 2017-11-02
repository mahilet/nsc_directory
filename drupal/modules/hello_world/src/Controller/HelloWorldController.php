<?php
namespace Drupal\hello_world\Controller;
use Drupal\Core\Controller\ControllerBase;

class HelloWorldController extends ControllerBase{

	public  function content(){

		return array(
			'#title' => 'Hello World',
			'#markup' => 'This is some content'

		);
	}
}
