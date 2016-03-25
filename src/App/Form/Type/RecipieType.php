<?php

namespace App\Form\Type;

use Silex\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use App\Form\Type\CategoryType;
use App\Form\Type\TagType;

class RecipieType extends AbstractType {

	private $app;

	public function __construct (Application $app) {
		$this->app = $app;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
			$builder->add('name', 'text', array(
					'label' => 'Nom',
			));
			$builder->add('description', 'textarea', array(
					'label' => 'Description',
			));
			$builder->add('quantity', 'text', array(
					'label' => 'Quantité',
			));
			$builder->add('tags', 'collection', array(
					'label' => 'Tags',
					'type' => new TagType(),
					'required' => false,
					'allow_add'    => true,
					'em' => $this->app['orm.em'],
			));
			$builder->add('categories', 'collection', array(
					'label' => 'Catégories',
					'type' => new CategoryType(),
					'required' => false,
					'allow_add'    => true,
					'em' => $this->app['orm.em'],
			));

	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Domain\Recipie',
		));
	}

	public function getName()
	{
		return 'recipie';
	}
}