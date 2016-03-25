<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IngredientType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text', array(
				'label' => 'Nom',
		));

		$builder->add('unit', 'text', array(
				'label' => 'Unité',
		));

		$builder->add('quantity', 'text', array(
				'label' => 'Quantité',
		));

	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Domain\Ingredient',
		));
	}

	public function getName()
	{
		return 'ingredient';
	}
}