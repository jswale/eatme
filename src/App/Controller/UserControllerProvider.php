<?php
namespace App\Controller;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;

use App\Controller\BaseControllerProvider;

class UserControllerProvider extends BaseControllerProvider {


	public function connect (SilexApplication $app) {

		$controllers = parent::connect($app);

		$controllers->get('/list',
				function  (SilexApplication $app, Request $request) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					return $app['twig']->render('user.list.twig.html',
							array(
									'users' => $app['user.manager']->getAll(),
							));
				})
				->bind('userList');

		$controllers->match('/myAccount',
				function  (SilexApplication $app, Request $request) {

					if (!$app['security']->isGranted('ROLE_USER')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$user = $app['user'];

					$builder = $app['form.factory']->createBuilder('form', $user, array(
							'allow_extra_fields' => true,
					))
					->setAction($app['url_generator']->generate('myAccount'))
					->add('username', 'text', array(
							'label' => 'User.Field.username',
					))
					->add('name', 'text', array(
							'label' => 'User.Field.name',
					));

					$form = $builder->getForm();

					$form->handleRequest($request);

					if ($form->isSubmitted() && $form->isValid()) {
						$data = $request->request->get("form");

						if($data["password"] != $data["password_repeat"]) {
							$app['session']->getFlashBag()->add('danger', 'Error.Password.mismatch');
						} else {
							$app['user.manager']->update($user, $data['username'], $data['name'], $user->isAdmin());
							if(!empty($data["password"])) {
								$app['user.manager']->changePassword($user, $data['password']);
							}
							return $app->redirect($app['url_generator']->generate('myAccount'));
						}
					}

					return $app['twig']->render('user.myAccount.twig.html', array(
							'form' => $form->createView(),
							'user' => $user,
					));
				})
			->bind('myAccount');

		$controllers->match('/edit/{id}',
				function  (SilexApplication $app, $id, Request $request) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$user = null;
					if(null != $id) {
						$user = $app['user.manager']->getById($id);
						if(null == $user) {
							return $app->redirect($app['url_generator']->generate('accessDenied'));
						}
					}

					$builder = $app['form.factory']->createBuilder('form', $user, array(
							'allow_extra_fields' => true,
					))
					->setAction($app['url_generator']->generate('userCreateOrUpdate', array(
							'id' => $id,
					)))
					->add('username', 'text', array(
							'label' => 'User.Field.username',
					))
					->add('name', 'text', array(
							'label' => 'User.Field.name',
					))
					->add('admin', 'checkbox', array(
							'required' => false,
							'label' => 'User.Field.role.admin',
					));

					$form = $builder->getForm();

					$form->handleRequest($request);

					if ($form->isSubmitted() && $form->isValid()) {
						$data = $request->request->get("form");

						if($data["password"] != $data["password_repeat"]) {
							$app['session']->getFlashBag()->add('danger', 'Error.Password.mismatch');
						} else {
							if(null == $user) {
								$user = $app['user.manager']->create($data['username'], $data['name'], array_key_exists("admin", $data), $data['password']);
							} else {
								$app['user.manager']->update($user, $data['username'], $data['name'], array_key_exists("admin", $data));
								if(!empty($data["password"])) {
									$app['user.manager']->changePassword($user, $data['password']);
								}
							}
							return $app->redirect($app['url_generator']->generate('userList'));
						}
					}

					// display the form
					return $app['twig']->render('user.createOrUpdate.twig.html', array(
							'form' => $form->createView(),
							'user' => $user,
					));
				})
			->bind('userCreateOrUpdate')
			->value('id', null);

		$controllers->get('/{id}/delete',
				function  ($id, SilexApplication $app) {

					if (!$app['security']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$bean = $app['user.manager']->getById($id);
					if(null == $bean) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}
					$app['user.manager']->delete($bean);

					return $app->redirect($app['url_generator']->generate('userList'));
				})
			->bind('userDelete');

		return $controllers;
	}

}