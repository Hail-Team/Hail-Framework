<?php

namespace Hail\Image\Commands;

use Closure;

class RectangleCommand extends \Hail\Image\Commands\AbstractCommand
{
    /**
     * Draws rectangle on given image
     *
     * @param  \Hail\Image\Image $image
     *
     * @return bool
     */
    public function execute($image)
    {
        $x1 = $this->argument(0)->type('numeric')->required()->value();
        $y1 = $this->argument(1)->type('numeric')->required()->value();
        $x2 = $this->argument(2)->type('numeric')->required()->value();
        $y2 = $this->argument(3)->type('numeric')->required()->value();
        $callback = $this->argument(4)->type('closure')->value();

        $namespace = $image->getDriver()->getNamespace();
        $rectangle_classname = "\{$namespace}\Shapes\RectangleShape";

        $rectangle = new $rectangle_classname($x1, $y1, $x2, $y2);

        if ($callback instanceof Closure) {
            $callback($rectangle);
        }

        $rectangle->applyToImage($image, $x1, $y1);

        return true;
    }
}
