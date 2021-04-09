<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension
{
	public function getFilters()
	{
		return [
			new TwigFilter('amount', [$this, 'amount'])
		];
	}

	// Le filtre 'amount' accepte alors 3 paramètres, le symbole, le séparateur des dizaines et
	// le séparateur des milliers.
	public function amount($value, string $symbol = '€', string $decsep = ',', string
	$thousandsep = ' ')
	{
		// On a  '19229' => On veut '192,29 €'
		$finalValue = $value / 100;
		// On obtient alors: 192.29

		$finalValue = number_format($finalValue, 2, $decsep, $thousandsep);
		// On obtient alors 192,29

		return $finalValue . ' ' . $symbol;
	}
}
