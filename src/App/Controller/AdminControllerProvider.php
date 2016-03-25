<?php
namespace App\Controller;

use Silex\Application as SilexApplication;

use App\Controller\BaseControllerProvider;

class AdminControllerProvider extends BaseControllerProvider {

	public function connect (SilexApplication $app) {

		$controllers = parent::connect($app);

		$controllers->get('/normalize',
				function  (SilexApplication $app) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$lng_hour = $app['translator']->trans("Hour");
					$lng_hour_short = $app['translator']->trans("Hour.short");
					$lng_hours = $app['translator']->trans("Hours");
					$lng_minute = $app['translator']->trans("Minute");
					$lng_minute_short = $app['translator']->trans("Minute.short");
					$lng_minutes = $app['translator']->trans("Minutes");
					$lng_tablespoon = $app['translator']->trans("Tablespoon");
					$lng_teaspoon = $app['translator']->trans("Teaspoon");

			    $app['db']->executeUpdate("UPDATE `eatme_timer` SET value = replace(value, '$lng_minute_short', 'minutes') WHERE value like '%$lng_minute_short'");
			    $app['db']->executeUpdate("UPDATE `eatme_timer` SET value = '1 $lng_minute' WHERE value in('1 $lng_minutes', '1$lng_minutes')");
			    $app['db']->executeUpdate("UPDATE `eatme_timer` SET value = replace(value, '$lng_hour_short', '$lng_hours') WHERE value like '%$lng_hour_short'");
			    $app['db']->executeUpdate("UPDATE `eatme_timer` SET value = '1 $lng_hour' WHERE value in('1 $lng_hours', '1$lng_hours')");

			    $app['db']->executeUpdate("UPDATE `eatme_step` SET description = CONCAT(UCASE(LEFT(description, 1)), SUBSTRING(description, 2))");
			    $app['db']->executeUpdate("UPDATE `eatme_step` SET description = CONCAT(description, \".\") WHERE RIGHT(description, 1) != \".\"");

			    $app['db']->executeUpdate("UPDATE `eatme_ingredient` SET unit = '$lng_tablespoon' WHERE unit in('cs', 'cas', 'tbsp')");
			    $app['db']->executeUpdate("UPDATE `eatme_ingredient` SET unit = '$lng_teaspoon' WHERE unit in('cc', 'cac', 'tsp')");
			    $app['db']->executeUpdate("UPDATE `eatme_ingredient` SET name = CONCAT(UCASE(LEFT(name, 1)), SUBSTRING(name, 2))");

					return $app->redirect($app['url_generator']->generate('homepage'));
				})
			->bind('adminNormalize');

		return $controllers;
	}

}