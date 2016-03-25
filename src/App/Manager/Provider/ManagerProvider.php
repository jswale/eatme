<?php

namespace App\Manager\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use App\Manager\CategoryManager;
use App\Manager\ImageManager;
use App\Manager\IngredientManager;
use App\Manager\IngredientGroupManager;
use App\Manager\RecipieManager;
use App\Manager\StepManager;
use App\Manager\StepGroupManager;
use App\Manager\TagManager;
use App\Manager\TimerManager;
use App\Manager\UserManager;


class ManagerProvider implements ServiceProviderInterface
{

	public function register(Application $app)
	{
		$app['user.manager'] = $app->share(function($app) {
			return new UserManager($app, '\App\Domain\User');
		});
		$app['tag.manager'] = $app->share(function($app) {
			return new TagManager($app, '\App\Domain\Tag');
		});
		$app['category.manager'] = $app->share(function($app) {
			return new CategoryManager($app, '\App\Domain\Category');
		});
		$app['recipie.manager'] = $app->share(function($app) {
			return new RecipieManager($app, '\App\Domain\Recipie');
		});
		$app['image.manager'] = $app->share(function($app) {
			return new ImageManager($app, '\App\Domain\Image');
		});
		$app['timer.manager'] = $app->share(function($app) {
			return new TimerManager($app, '\App\Domain\Timer');
		});
		$app['ingredient.manager'] = $app->share(function($app) {
			return new IngredientManager($app, '\App\Domain\Ingredient');
		});
		$app['step.manager'] = $app->share(function($app) {
			return new StepManager($app, '\App\Domain\Step');
		});
		$app['ingredientGroup.manager'] = $app->share(function($app) {
			return new IngredientGroupManager($app, '\App\Domain\IngredientGroup');
		});
		$app['stepGroup.manager'] = $app->share(function($app) {
			return new StepGroupManager($app, '\App\Domain\StepGroup');
		});
	}

	public function boot(Application $app)
	{
	}

}