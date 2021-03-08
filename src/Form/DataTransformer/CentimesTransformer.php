<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CentimesTransformer implements DataTransformerInterface
{

	//	On retrouve la fameuse méthode 'transform', qui agit AVANT d'afficher la valeur dans le formulaire.
	public function transform($value)
	{
		if (null === $value) {
			return;
		}
		return $value / 100;
	}

	//	Et la méthode 'reverseTransform', qui agit au moment où on a soumis une valeur dans le formulaire et on veut travailler avec.
	public function reverseTransform($value)
	{
		if (null === $value) {
			return;
		}
		return $value * 100;
	}
}