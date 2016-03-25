<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use App\Form\Type\StepType;

class StepGroupType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add(
				$builder->create('steps', StepType::class, array(
						'label' => "Liste des étapes",
				))
		);

	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'App\Domain\StepGroup',
		));
	}

	public function getName()
	{
		return 'stepGroup';
	}
}