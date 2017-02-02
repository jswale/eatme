<?php
namespace App\Controller;

use Silex\Application as SilexApplication;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use App\Controller\BaseControllerProvider;
use App\Domain\Category;

class RecipieControllerProvider extends BaseControllerProvider implements ControllerProviderInterface {


	public function connect (SilexApplication $app) {

		$controllers = parent::connect($app);

		$controllers->post('/template/recipie/image',
				function  (SilexApplication $app, Request $request) {
					return $app['twig']->render('recipie.createOrUpdate.imageLine.twig.html', array(
							'counter' => $request->get("counter"),
							'id' => $request->get("id"),
							'name' => $request->get("name"),
					));
				})
			->bind('recipieImageLineTemplate');

		$controllers->post('/template/recipie/timer',
				function  (SilexApplication $app, Request $request) {
					return $app['twig']->render('recipie.createOrUpdate.timerLine.twig.html', array(
							'counter' => $request->get("counter"),
							'id' => $request->get("id"),
							'name' => $request->get("name"),
							'value' => $request->get("value"),
					));
				})
			->bind('recipieTimerLineTemplate');

		$controllers->post('/template/recipie/ingredientGroup',
				function  (SilexApplication $app, Request $request) {
					return $app['twig']->render('recipie.createOrUpdate.ingredientGroup.twig.html', array(
							'counter' => $request->get("counter"),
							'id' => $request->get("id"),
							'name' => $request->get("name")
					));
				})
			->bind('recipieIngredientGroupTemplate');

		$controllers->post('/template/recipie/ingredientLine',
				function  (SilexApplication $app, Request $request) {
					return $app['twig']->render('recipie.createOrUpdate.ingredientLine.twig.html', array(
							'counter' => $request->get("counter"),
							'id' => $request->get("id"),
							'group' => $request->get("group"),
							'name' => $request->get("name"),
							'quantity' => $request->get("quantity"),
							'unit' => $request->get("unit"),
							'ref' => $request->get("ref"),
					));
				})
			->bind('recipieIngredientLineTemplate');

		$controllers->post('/template/recipie/stepGroup',
				function  (SilexApplication $app, Request $request) {
					return $app['twig']->render('recipie.createOrUpdate.stepGroup.twig.html', array(
							'counter' => $request->get("counter"),
							'id' => $request->get("id"),
							'name' => $request->get("name")
					));
				})
			->bind('recipieStepGroupTemplate');

		$controllers->post('/template/recipie/stepLine',
				function  (SilexApplication $app, Request $request) {
					return $app['twig']->render('recipie.createOrUpdate.stepLine.twig.html', array(
							'counter' => $request->get("counter"),
							'id' => $request->get("id"),
							'group' => $request->get("group"),
							'description' => $request->get("description"),
					));
				})
			->bind('recipieStepLineTemplate');

		$controllers->get('/delete/{id}',
				function  ($id, SilexApplication $app) {

					if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$bean = $app['recipie.manager']->getById($id);
					if(null == $bean) {
						return $app->redirect($app['url_generator']->generate('accessDenied'));
					}
					$app['recipie.manager']->remove($bean);

					return $app->redirect($app['url_generator']->generate('recipies', array(
							'page' => 1,
					)));
				})
			->bind('recipieDelete');

		$controllers->match('/edit/{id}',
				function  ($id, SilexApplication $app, Request $request) {

					if (!$app['security.authorization_checker']->isGranted('ROLE_USER')) {
						return $app->redirect($app['url_generator']->generate('login'));
					}

					$recipie = null;
					if(null != $id) {
						$recipie = $app['recipie.manager']->getById($id);
						if(null == $recipie) {
							return $app->redirect($app['url_generator']->generate('accessDenied'));
						} else {
							if (!($app['security.authorization_checker']->isGranted('ROLE_USER') && ($app['security.authorization_checker']->isGranted('ROLE_ADMIN') || $app['user']->getId() == $recipie->getUser()->getId() ))) {
								return $app->redirect($app['url_generator']->generate('accessDenied'));
							}
						}
					}

					$builder = $app['form.factory']->createBuilder(FormType::class, $recipie, array(
							'allow_extra_fields' => true,
							'attr' => array(
								'enctype' => "multipart/form-data",
							),
					))
					->setAction($app['url_generator']->generate('recipieCreateOUpdate', array(
							'id' => $id,
					)))
					->add('name', TextType::class, array(
							'label' => 'Recipie.Field.name',
					))
					->add('description', TextareaType::class, array(
							'label' => 'Recipie.Field.description',
							'required' => false,
					))
					->add('quantity', TextType::class, array(
							'label' => 'Recipie.Field.quantity',
							'attr' => array(
								'placeholder' => 'Recipie.CreateOrUpdate.Field.quantity.help'
							),
					))
					->add('category', EntityType::class, array(
							'label' => 'Recipie.Field.category',
    					'class' => Category::class,
							'choice_label' => 'name',
							'em' => $app['orm.em'],
							'required' => true,
					))
					;

					$form = $builder->getForm();

					$form->handleRequest($request);

					if ($form->isSubmitted() && $form->isValid()) {

						$data = $request->request->get("form");

						$recipieManager = $app['recipie.manager'];
						if(null == $recipie) {
							$category = $app['category.manager']->getById($data['category']);

							$recipie = $recipieManager->create(
								$app['user'],
								$data['name'],
								$data['description'],
								$data['quantity'],
								$category
							);
						}

						// --- Images -------------------
						$imageManager = $app['image.manager'];

						$imagesToDelete = array();
						foreach($recipie->getImages() as $image) {
							$imagesToDelete[$image->getId()] = $image;
						}


						if(array_key_exists("images", $data)) {
							$files = $request->files->get("form");
							foreach($data['images'] as $imageJsonId => $imageJson) {

								$imageId = $imageJson["id"];
								$image = null;
								if("-1" == $imageId) {
									$image = $imageManager->create($recipie, $imageJson["name"], $files['images'][$imageJsonId]['file']);
								} else {
									$image = $imageManager->getById($imageId);
									$imageManager->update($image, $imageJson["name"], $files['images'][$imageJsonId]['file']);
									unset($imagesToDelete[$imageId]);
								}
								$recipie->addImage($image);
							}
						}

						foreach($imagesToDelete as $imageId => $image) {
							$imageManager->cleanFile($image);
							$imageManager->remove($image);
						}
						// ----- END ----------------------

						// --- Tags -------------------
						$tagManager = $app['tag.manager'];

						$tagsToDelete = array();
						foreach($recipie->getTags() as $tag) {
							$tagsToDelete[$tag->getId()] = $tag;
						}

						if(array_key_exists("tags", $data)) {
							$tags = trim($data['tags']);
							if(!empty($tags)) {
								$tags = explode(",", $tags);
								foreach($tags as $tagName) {
									$tagName = trim($tagName);
									$tag = $tagManager->getByName($tagName);
									if(null == $tag) {
										$tag = $tagManager->create($tagName);
									}
									if(!$recipie->getTags()->contains($tag)) {
										$recipie->addTag($tag);
									}
									unset($tagsToDelete[$tag->getId()]);
								}
							}
						}

						foreach($tagsToDelete as $tagId => $tag) {
							$recipie->removeTag($tag);
						}
						// ----- END ----------------------

						// --- Timers -------------------
						$timerManager = $app['timer.manager'];

						$timersToDelete = array();
						foreach($recipie->getTimers() as $timer) {
							$timersToDelete[$timer->getId()] = $timer;
						}

						if(array_key_exists("timers", $data)) {
							foreach($data['timers'] as $timerJson) {
								$timerId = $timerJson["id"];
								$timer = null;
								if("-1" == $timerId) {
									$timer = $timerManager->create($recipie, $timerJson["name"], $timerJson["value"]);
								} else {
									$timer = $timerManager->getById($timerId);
									$timerManager->update($timer, $timerJson["name"], $timerJson["value"]);
									unset($timersToDelete[$timerId]);
								}
								$recipie->addTimer($timer);
							}
						}

						foreach($timersToDelete as $timerId => $timer) {
							$timerManager->remove($timer);
						}
						// ----- END ----------------------

						// --- Ingredient Group -------------------
						$ingredientGroupManager = $app['ingredientGroup.manager'];
						$ingredientManager = $app['ingredient.manager'];

						$ingredientGroupsToDelete = array();
						foreach($recipie->getIngredientGroups() as $ingredientGroup) {
							$ingredientGroupsToDelete[$ingredientGroup->getId()] = $ingredientGroup;
						}

						if(array_key_exists("ingredientGroups", $data)) {
							foreach($data['ingredientGroups'] as $ingredientGroupJson) {
								$ingredientGroupId = $ingredientGroupJson["id"];
								$ingredientGroup = null;
								if("-1" == $ingredientGroupId) {
									$ingredientGroup = $ingredientGroupManager->create($recipie, $ingredientGroupJson["name"]);
								} else {
									$ingredientGroup = $ingredientGroupManager->getById($ingredientGroupId);
									$ingredientGroupManager->update($ingredientGroup, $ingredientGroupJson["name"]);
									unset($ingredientGroupsToDelete[$ingredientGroupId]);
								}

								// --- Ingredient -------------------
								$ingredientsToDelete = array();
								foreach($ingredientGroup->getIngredients() as $ingredient) {
									$ingredientsToDelete[$ingredient->getId()] = $ingredient;
								}

								foreach($ingredientGroupJson['ingredients'] as $ingredientJson) {
									$ingredientId = $ingredientJson["id"];

									$refId = $ingredientJson["ref"];
									$ref = null;
									if(!empty($refId)) {
										$ref = $recipieManager->getById($refId);
									}

									$ingredient = null;
									if("-1" == $ingredientId) {
										$ingredient = $ingredientManager->create($ingredientGroup, $ingredientJson["name"], $ingredientJson["quantity"], $ingredientJson["unit"], $ref);
									} else {
										$ingredient = $ingredientManager->getById($ingredientId);
										$ingredientManager->update($ingredient, $ingredientJson["name"], $ingredientJson["quantity"], $ingredientJson["unit"], $ref);
										unset($ingredientsToDelete[$ingredientId]);
									}

									$ingredientGroup->addIngredient($ingredient);
								}

								foreach($ingredientsToDelete as $ingredientId => $ingredient) {
									$ingredientManager->remove($ingredient);
								}
								// ----- END ----------------------

								$recipie->addIngredientGroup($ingredientGroup);
							}
						}

						foreach($ingredientGroupsToDelete as $ingredientGroupId => $ingredientGroup) {
							echo "Removing $ingredientGroupId";
							$ingredientGroupManager->remove($ingredientGroup);
						}
						// ----- END ----------------------


						// --- Step Group -------------------
						$stepGroupManager = $app['stepGroup.manager'];
						$stepManager = $app['step.manager'];

						$stepGroupsToDelete = array();
						foreach($recipie->getStepGroups() as $stepGroup) {
							$stepGroupsToDelete[$stepGroup->getId()] = $stepGroup;
						}

						if(array_key_exists("stepGroups", $data)) {
							foreach($data['stepGroups'] as $stepGroupJson) {
								$stepGroupId = $stepGroupJson["id"];
								$stepGroup = null;
								if("-1" == $stepGroupId) {
									$stepGroup = $stepGroupManager->create($recipie, $stepGroupJson["name"]);
								} else {
									$stepGroup = $stepGroupManager->getById($stepGroupId);
									$stepGroupManager->update($stepGroup, $stepGroupJson["name"]);
									unset($stepGroupsToDelete[$stepGroupId]);
								}

								// --- Step -------------------
								$stepsToDelete = array();
								foreach($stepGroup->getSteps() as $step) {
									$stepsToDelete[$step->getId()] = $step;
								}

								foreach($stepGroupJson['steps'] as $stepJson) {
									$stepId = $stepJson["id"];
									$step = null;
									if("-1" == $stepId) {
										$step = $stepManager->create($stepGroup, $stepJson["description"]);
									} else {
										$step = $stepManager->getById($stepId);
										$stepManager->update($step, $stepJson["description"]);
										unset($stepsToDelete[$stepId]);
									}

									$stepGroup->addStep($step);
								}

								foreach($stepsToDelete as $stepId => $step) {
									$stepManager->remove($step);
								}
								// ----- END ----------------------

								$recipie->addStepGroup($stepGroup);
							}
						}

						foreach($stepGroupsToDelete as $stepGroupId => $stepGroup) {
							$stepGroupManager->remove($stepGroup);
						}
						// ----- END ----------------------

						$recipieManager->persist($recipie);
						return $app->redirect($app['url_generator']->generate('recipieDetail', array(
								'id' => $recipie->getId(),
								'name' => $recipie->getCleanName(),
						)));
					}

					// display the form
					return $app['twig']->render('recipie.createOrUpdate.twig.html', array(
							'form' => $form->createView(),
							'recipie' => $recipie,
// 							'recipieAsJson' => null == $recipie ? null : $recipie->getAsJson(),
					));
				})
			->bind('recipieCreateOUpdate')
			->value('id', null);

		return $controllers;
	}

}