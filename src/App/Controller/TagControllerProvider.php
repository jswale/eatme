<?php
namespace App\Controller;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;

use App\Controller\BaseControllerProvider;

class TagControllerProvider extends BaseControllerProvider {

	public function connect (SilexApplication $app) {

		$controllers = parent::connect($app);

		$controllers->get('/list',
				function  (SilexApplication $app, Request $request) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					return $app['twig']->render('tag.list.twig.html',
							array(
									'tags' => $app['tag.manager']->getAll(),
							));
				})
			->bind('tagList');

		$controllers->match('/create',
				function  (SilexApplication $app, Request $request) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$form = $app['form.factory']->createBuilder('form')
					->setAction($app['url_generator']->generate('tagCreate'))
					->add('name', 'text', array(
							'label' => 'Tag.Field.name',
    			))
    			->add('validate', 'submit', array (
    					'attr' => array(
    							'class' => 'btn-primary',
    					),
    			))
					->getForm();

					$form->handleRequest($request);

					if ($form->isSubmitted() && $form->isValid()) {
						$data = $form->getData();
						$app['tag.manager']->create($data['name']);
						return $app->redirect($app['url_generator']->generate('tagList'));
					}

					// display the form
					return $app['twig']->render('tag.create.twig.html', array('form' => $form->createView()));

				})
			->bind('tagCreate');

		$controllers->get('/{id}/delete',
				function  ($id, SilexApplication $app) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$bean = $app['tag.manager']->getById($id);
					if(null == $bean) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}
					$app['tag.manager']->remove($bean);

					return $app->redirect($app['url_generator']->generate('tagList'));
				})
			->bind('tagDelete');

		$controllers->match('/{id}/edit',
				function  ($id, SilexApplication $app, Request $request) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$bean = $app['tag.manager']->getById($id);
					if(null == $bean) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}

					$form = $app['form.factory']->createBuilder('form', $bean)
					->setAction($app['url_generator']->generate('tagUpdate', array(
							'id' => $id,
					)))
					->add('name', 'text', array(
							'label' => 'Tag.Field.name',
							'required' => true,
					))
    			->add('validate', 'submit', array (
    					'attr' => array(
    							'class' => 'btn-primary',
    					),
    			))
					->getForm();

					$form->handleRequest($request);

					if ($form->isSubmitted() && $form->isValid()) {
						$app['tag.manager']->persist($bean);
						return $app->redirect($app['url_generator']->generate('tagList'));
					}

					// display the form
					return $app['twig']->render('tag.edit.twig.html', array(
							'id' => $id,
							'form' => $form->createView(),
					));
				})
			->bind('tagUpdate');


		return $controllers;
	}

}