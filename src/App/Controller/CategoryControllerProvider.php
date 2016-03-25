<?php
namespace App\Controller;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;

use App\Controller\BaseControllerProvider;

class CategoryControllerProvider extends BaseControllerProvider {


	public function connect (SilexApplication $app) {

		$controllers = parent::connect($app);

		$controllers->get('/list',
				function  (SilexApplication $app, Request $request) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					return $app['twig']->render('category.list.twig.html',
							array(
									'categories' => $app['category.manager']->getAll(),
							));
				})
				->bind('categoryList');

		$controllers->match('/create',
				function  (SilexApplication $app, Request $request) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$form = $app['form.factory']->createBuilder('form')
					->setAction($app['url_generator']->generate('categoryCreate'))
					->add('name', 'text', array(
							'label' => 'Category.Field.name',
					))
					->add('Validate', 'submit', array (
							'attr' => array(
									'class' => 'btn-primary',
							),
					))->getForm();

					$form->handleRequest($request);

					if ($form->isSubmitted() && $form->isValid()) {
						$data = $form->getData();
						$app['category.manager']->create($data['name']);
						return $app->redirect($app['url_generator']->generate('categoryList'));
					}

					// display the form
					return $app['twig']->render('category.create.twig.html', array('form' => $form->createView()));
				})
			->bind('categoryCreate');

		$controllers->get('/{id}/delete',
				function  ($id, SilexApplication $app) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$bean = $app['category.manager']->getById($id);
					if(null == $bean) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}
					$app['category.manager']->remove($bean);

					return $app->redirect($app['url_generator']->generate('categoryList'));
				})
			->bind('categoryDelete');

		$controllers->match('/{id}/edit',
				function  ($id, SilexApplication $app, Request $request) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$bean = $app['category.manager']->getById($id);
					if(null == $bean) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}

					$form = $app['form.factory']->createBuilder('form', $bean)
					->setAction($app['url_generator']->generate('categoryUpdate', array(
							'id' => $id,
					)))
					->add('name', 'text', array(
							'label' => 'Category.Field.name',
					))
					->add('Validate', 'submit', array (
    					'attr' => array(
    							'class' => 'btn-primary',
    					),
    			))
    			->getForm();

					$form->handleRequest($request);

					if ($form->isSubmitted() && $form->isValid()) {
						$app['recipie.manager']->persist($bean);
						return $app->redirect($app['url_generator']->generate('categoryList'));
					}

					// display the form
					return $app['twig']->render('category.edit.twig.html', array(
							'id' => $id,
							'form' => $form->createView(),
					));
				})
			->bind('categoryUpdate');

		return $controllers;
	}

}