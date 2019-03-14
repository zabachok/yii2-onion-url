<?php

namespace zabachok\onionUrl;

use Yii;
use yii\web\Request;
use yii\base\BaseObject;
use yii\web\UrlRule as BaseUrlRule;
use yii\web\UrlRuleInterface;

class UrlRule extends BaseObject implements UrlRuleInterface
{
    /**
     * @var string[]
     */
    public $rules = [];

    /**
     * @var string 
     */
    public $defaultRoute = '';

    public $userRoute;
    public $projectRoute;

    /**
     * @var Request
     */
    private $request;

    public function init()
    {
        parent::init();
        $this->request = Yii::$container->get(Request::class);
    }

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        if (!isset($params['username'], $params['slug'])) {
            return false;
        }
        if ($route === $this->defaultRoute) {
            return $params['username'] . '/' . $params['slug'];
        }

        foreach ($this->rules as $pattern => $rou) {
            if ($rou != $route) {
                continue;
            }
            $toCreate = $params;
            unset($toCreate['username']);
            unset($toCreate['slug']);
            if ($result = $this->create($pattern, $route, $toCreate)) {
                return $params['username'] . '/' . $params['slug'] . '/' . $result;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();

        if (preg_match('%^(\w+)(/(\w+))(/(.*))?$%', $pathInfo, $matches)) {

            $model = Project::findOne(['fullSlug' => $matches[1] . '/' . $matches[3]]);
            if ($model) {
                $this->request->setUrl($matches[5] ?? '');
                foreach ($this->rules as $pattern => $route) {
                    if ($result = $this->pr($pattern, $route)) {
                        $result[1] = array_merge($result[1], ['projectId' => $model->id, 'fullSlug' => $matches[0]]);

                        return $result;
                    }
                }
            }
        }

        return false;
    }

    private function pr($pattern, $route)
    {
        $rule = new BaseUrlRule([
            'pattern' => $pattern,
            'route' => $route,
        ]);

        return $rule->parseRequest(Yii::$app->urlManager, $this->request);
    }

    private function create($pattern, $route, $params)
    {
        $rule = new BaseUrlRule([
            'pattern' => $pattern,
            'route' => $route,
        ]);

        return $rule->createUrl(Yii::$app->urlManager, $route, $params);
    }
}
