# yii2-onion-url

Url rule class to make github-like urls.

## Layers

Uri is composite of three layers like ogre :)  

First layer is user name. Example: `zabachok`  
Second layer is project name. Example: `toolka`  
Third layer is free route. Example: `task/108`

All layers are collected in one line in uri:  
First layer: `/zabachok`  
Second layer: `/zabachok/toolka`  
Third layer: `/zabachok/toolka/task/108`  

You can make route to each layer independently.

## Configure

Add to rules section in UrlManager configuration:
```php
[
    'class' => zabachok/onionUrl/UrlRule::class,
    'provider' => YourProvider::class,
    'userRoute' => 'user/view',
    'projectRoute' => 'project/view',
    'rules' => [
        'settings' => 'project/settings',
        'tasks' => 'task/index',
        'task/<id:\d+>' => 'task/view',
        'task/<id:\d+>/update' => 'task/update',
    ],
],
```
**provider** - class implements ProviderInterface. It is needs to rule can to check exists that user or project.  
**userRoute** - first layer: field with route to user page.  
**projectRoute** - second layer: field with route to project page.  
**rules** - third layer: array with your custom routes. In this section you can use standard yii2 routes.  

Example with full UrlManager configuration:
```php
'urlManager' => [
    'cache' => YII_ENV_DEV ? false : 'cache',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => true,
    'rules' => [
        '' => 'site/index',
        'login' => 'user/login',
        'logout' => 'user/logout',
        [
            'class' => zabachok/onionUrl/UrlRule::class,
            'provider' => YourProvider::class,
            'userRoute' => 'user/view',
            'projectRoute' => 'project/view',
            'rules' => [
                'settings' => 'project/settings',
                'tasks' => 'task/index',
                'task/<id:\d+>' => 'task/view',
                'task/<id:\d+>/update' => 'task/update',
            ],
        ],
    ],
],
```
