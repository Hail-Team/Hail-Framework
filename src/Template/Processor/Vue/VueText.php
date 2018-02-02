<?php
namespace Hail\Template\Processor\Vue;

use Hail\Template\Expression\Expression;
use Hail\Template\Tokenizer\Token\Element;
use Hail\Template\Processor\Helpers;
use Hail\Template\Processor\ProcessorInterface;

final class VueText implements ProcessorInterface
{
    public static function process(Element $element): bool
    {
        $expression = $element->getAttribute('v-text');
        if ($expression === null) {
            return false;
        }

        $expression = Expression::parse($expression);

        Helpers::text($element, 'echo \htmlspecialchars(' . $expression . ', ENT_HTML5)');

        $element->removeAttribute('v-text');

        return false;
    }
}