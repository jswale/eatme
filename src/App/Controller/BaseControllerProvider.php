<?php
namespace App\Controller;

use Silex\Application as SilexApplication;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

class BaseControllerProvider implements ControllerProviderInterface {

	private $app;

	public function connect (SilexApplication $app) {
		$this->app = $app;

		$app->error([
				$this,
				'error'
		]);

		$controllers = $app['controllers_factory'];

		return $controllers;
	}

	public function error (\Exception $e, $code) {
		if ($this->app['debug']) {
			return;
		}

		switch ($code) {
			case 404:
				$message = 'The requested page could not be found.';
				break;
			default:
				$message = 'We are sorry, but something went terribly wrong.';
		}

		return new Response($message, $code);
	}
}