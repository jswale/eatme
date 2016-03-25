<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use App\Form\Type\IngredientType;

class IngredientGroupType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add(
				$builder->create('ingredients', IngredientType::class, array(
						'label' => "Liste des ingrédients",
				))
		);

	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Domain\IngredientGroup',
		));
	}

	public function getName()
	{
		return 'ingredientGroup';
	}
}