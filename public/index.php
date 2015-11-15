<?php

require __DIR__ . '/../vendor/autoload.php';

interface Rule {
    public function getRoute();
    public function getSavePath();
    public function getLoadPath();
}

class ibe30 implements Rule {

    private $route;
    private $savePath;
    private $loadPath;

    /**
     * ibe30 constructor.
     * @param $route
     * @param $savePath
     * @param $loadPath
     */
    public function __construct($route, $savePath, $loadPath)
    {
        $this->route = $route;
        $this->savePath = $savePath;
        $this->loadPath = $loadPath;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return mixed
     */
    public function getSavePath()
    {
        return $this->savePath;
    }

    /**
     * @return mixed
     */
    public function getLoadPath()
    {
        return $this->loadPath;
    }

}

use Imagine\Image\Box;

$app = new Silex\Application();
$app['debug'] = true;


$rule = new ibe30(
    'img/airlines/logos/',
    'img/airlines/logos/',
    __DIR__ . '/images/'
);

$app->get($rule->getRoute() . '{width}x{height}/{name}.{extension}',
    function ($width, $height, $name, $extension) use ($rule) {

        saveImage(
            $rule,
            $width,
            $height,
            $name,
            $extension
        );

        exit(0);
    }
)
    ->assert('width', '\d{1,3}')
    ->assert('height', '\d{1,3}')
    ->assert('extension', 'png|jpg');

$app->run();

function saveImage(Rule $rule, $width, $height, $name, $extension)
{
    $sizePath = $width . 'x' . $height . '/';

    if (! file_exists(($rule->getSavePath() . $sizePath ))) {
        mkdir($rule->getSavePath() . $sizePath, 0777, true);
    }

    $imagine = new Imagine\Gd\Imagine();
    $imagine->open($rule->getLoadPath() . $name . '.' . $extension)
        ->thumbnail(new Box($width, $height), \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND)
        ->save($rule->getSavePath() . $sizePath  . $name . '.' . $extension)
        ->show($extension);
}

