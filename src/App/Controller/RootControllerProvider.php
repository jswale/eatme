<?php
namespace App\Controller;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;

use App\Controller\BaseControllerProvider;

class RootControllerProvider extends BaseControllerProvider {

	public function connect (SilexApplication $app) {

		$controllers = parent::connect($app);

		$controllers->get('',
				function  (SilexApplication $app) {

					return $app['twig']->render('index.twig.html', array(
							'recipie_count' => $app['recipie.manager']->getCount(),
							'categoriesCounter' => $app['recipie.manager']->getGroupByCategoryCount(),
							'tagsCounter' => $app['recipie.manager']->getGroupByTagCount(),
							'usersCounter' => $app['recipie.manager']->getGroupByUserCount(),
					));
				})
			->bind('homepage');


			$controllers->get('rss',
					function  (SilexApplication $app) {
						return $app['twig']->render('rss.twig.xml',
								array(
										'searchResult' => $app['recipie.manager']->getSearchResult(1)
								));
					})
					->bind('rss');


		$controllers->get('recettes/detail/{id}/{name}',
				function  (SilexApplication $app, $id) {

					$bean = $app['recipie.manager']->getById($id);
					if(null == $bean) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}

					return $app['twig']->render('recipie.detail.twig.html',
							array(
									'recipie' => $bean
							));
				})
				->assert('id', '\d+')
				->bind('recipieDetail');

		$controllers->get('recettes/page/{page}',
				function  (SilexApplication $app, $page) {
					return $app['twig']->render('recipie.list.twig.html',
							array(
									'searchResult' => $app['recipie.manager']->getSearchResult($page),
									'paginationUrlModel' => $app['url_generator']->generate('recipies', array('page' => '__PAGE_ID__')),
							));
				})
		->assert('page', '\d+|__PAGE_ID__')
		->bind('recipies');

		$controllers->match('random',
				function  (SilexApplication $app, Request $request) {

					$recipie = $app['recipie.manager']->getRandom();
					if(null == $recipie) {
						return $app->redirect($app['url_generator']->generate('homepage'));
					} else {
						return $app->redirect($app['url_generator']->generate('recipieDetail', array(
								'id' => $recipie->getId(),
								'name' => $recipie->getCleanName(),
						)));
					}

				})
				->bind('random');

		$controllers->get('user/{id}/{name}/page/{page}',
				function  (SilexApplication $app, $id, $name, $page) {

					$user = $app['user.manager']->getById($id);
					if(null == $user) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}

					return $app['twig']->render('recipie.list.twig.html',
							array(
									'currentUser' => $user,
									'searchResult' => $app['recipie.manager']->getSearchResultByUser($user, $page),
									'paginationUrlModel' => $app['url_generator']->generate('user', array(
											'id' => $id,
											'name' => $name,
											'page' => '__PAGE_ID__',
									)),
							));
				})
				->assert('page', '\d+|__PAGE_ID__')
				->bind('user');

		$controllers->get('categories/{id}/{name}/page/{page}',
				function  (SilexApplication $app, $id, $name, $page) {

					$bean = $app['category.manager']->getById($id);
					if(null == $bean) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}

					return $app['twig']->render('recipie.list.twig.html',
							array(
									'currentCategory' => $bean,
									'searchResult' => $app['recipie.manager']->getSearchResultByCategory($bean, $page),
									'paginationUrlModel' => $app['url_generator']->generate('categories', array('id' => $id, 'name' => $name,'page' => '__PAGE_ID__')),
							));
				})
				->assert('id', '\d+')
				->assert('page', '\d+|__PAGE_ID__')
				->bind('categories');

		$controllers->get('tags/{id}/{name}/page/{page}',
				function  (SilexApplication $app, $id, $name, $page) {

					$bean = $app['tag.manager']->getById($id);
					if(null == $bean) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}

					return $app['twig']->render('recipie.list.twig.html',
							array(
									'currentTag' => $bean,
									'searchResult' => $app['recipie.manager']->getSearchResultByTag($bean, $page),
									'paginationUrlModel' => $app['url_generator']->generate('tags', array('id' => $id, 'name' => $name,'page' => '__PAGE_ID__')),
							));
				})
		->assert('id', '\d+')
		->assert('page', '\d+|__PAGE_ID__')
		->bind('tags');

		$controllers->get('advancedSearch/{categoryId}/{query}/page/{page}',
				function  (SilexApplication $app, $categoryId, $query, $page) {

					$category = $app['category.manager']->getById($categoryId);
					if(null == $category) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}

					preg_match_all("/[\+-][^\+-]*/", $query, $matches);
					$includes = array();
					$excludes = array();
					foreach($matches[0] as $match) {
						$action = substr($match, 0, 1);
						$name = substr($match, 1);
						switch($action) {
							case "+":
								array_push($includes, $name);
								break;
							case "-":
								array_push($excludes, $name);
								break;
						}
					}

					return $app['twig']->render('recipie.list.twig.html',
							array(
									'currentAdvancedQuery' => $query,
									'currentAdvancedQueryInclude' => $includes,
									'currentAdvancedQueryExclude' => $excludes,
									'category' => $category,
									'searchResult' => $app['recipie.manager']->getSearchResultByIngredients($category, $includes, $excludes, $page),
									'paginationUrlModel' => $app['url_generator']->generate('advancedSearch', array('query' => $query, 'categoryId' => $categoryId, 'page' => '__PAGE_ID__')),
							));
				})
		->assert('categoryId', '\d+')
		->assert('page', '\d+|__PAGE_ID__')
		->bind('advancedSearch');

		$controllers->get('advancedSearch',
				function  (SilexApplication $app) {
					return $app['twig']->render('search.twig.html', array(
							'categories' => $app['category.manager']->getAllAsList()
					));
				})
		->bind('searchByIngredient');

		$controllers->match('advancedSearch',
				function  (SilexApplication $app, Request $request) {

					$category = $app['category.manager']->getById($request->get("category"));
					if(null == $category) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}

					$withs = array();
					if(null != $request->get("avec")) {
						foreach($request->get("avec") as $name) {
							$name = trim($name);
							if("" != $name) {
								array_push($withs, $name);
							}
						}
					}

					$withouts = array();
					if(null != $request->get("sans")) {
						foreach($request->get("sans") as $name) {
							$name = trim($name);
							if("" != $name) {
								array_push($withouts, $name);
							}
						}
					}

					$with = (count($withs) > 0 ? "+" . join("+", $withs) : "");
					$without = (count($withouts) > 0 ? "-" . join("-", $withouts) : "");

					$query = $with . $without;
					if("" == $query) {
						return $app->redirect($app['url_generator']->generate('categories', array(
								'id' => $category->getId(),
								'name' => $category->getName(),
								'page' => 1,
						)));
					} else {
						return $app->redirect($app['url_generator']->generate('advancedSearch', array(
								'query' => $query,
								'categoryId' => $category->getId(),
								'page' => 1,
						)));
					}
				})
		->bind('doAdvancedSearch');

		$controllers->get('search/{query}/page/{page}',
				function  (SilexApplication $app, $query, $page) {

					return $app['twig']->render('recipie.list.twig.html',
							array(
									'currentQuery' => $query,
									'searchResult' => $app['recipie.manager']->getSearchResultByQuery($query, $page),
									'paginationUrlModel' => $app['url_generator']->generate('search', array('query' => $query,'page' => '__PAGE_ID__')),
							));
				})
				->assert('page', '\d+|__PAGE_ID__')
				->bind('search');

		$controllers->match('search',
				function  (SilexApplication $app, Request $request) {

					return $app->redirect($app['url_generator']->generate('search', array(
							'query' => $request->get("query"),
							'page' => 1,
					)));
				})
				->bind('doSearch');

		$controllers->get('accessDenied',
				function  (SilexApplication $app) {

					$app['session']->getFlashBag()->add('danger', "L'accès à la ressource vous a été interdit");
					return $app->redirect($app['url_generator']->generate('homepage'));
				})
		->bind('accessDenied');

		$controllers->get('login',
				function  (SilexApplication $app, Request $request) {
					return $app['twig']->render('login.twig.html',
							array(
									'error' => $app['security.utils']->getLastAuthenticationError(),
									'username' => $app['security.utils']->getLastUsername(),
            			'allowRememberMe' => isset($app['security.remember_me.response_listener']),
							));
				})
			->bind('login');

		return $controllers;
	}

}