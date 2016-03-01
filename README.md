Context and Access Filters for yii2 framework
=============================================

[![Latest Stable Version](https://poser.pugx.org/claudejanz/yii2-context-access-filter/v/stable.svg)](https://packagist.org/packages/claudejanz/yii2-context-access-filter) [![Total Downloads](https://poser.pugx.org/claudejanz/yii2-context-access-filter/downloads.svg)](https://packagist.org/packages/claudejanz/yii2-context-access-filter) [![Latest Unstable Version](https://poser.pugx.org/claudejanz/yii2-context-access-filter/v/unstable.svg)](https://packagist.org/packages/claudejanz/yii2-context-access-filter) [![License](https://poser.pugx.org/claudejanz/yii2-context-access-filter/license.svg)](https://packagist.org/packages/claudejanz/yii2-context-access-filter)


This shows how to implement Context and Access Filter for Yii2 framework

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require "claudejanz/yii2-context-access-filter": "dev-master"
```

or add

```
"claudejanz/yii2-context-access-filter": "dev-master"
```

to the ```require``` section of your `composer.json` file.

install rbac as in doc [Role based access control (RBAC) ](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#rbac)

## Usage

###in RbacController

```php
class RbacController extends Controller {
    public function actionIndex() {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        // add "view" permission
        $view = $auth->createPermission('view');
        $view->description = 'view';
        $auth->add($view);

        // add "create" permission
        $create = $auth->createPermission('create');
        $create->description = 'create';
        $auth->add($create);

        // add the rule
        $rule = new \claudejanz\contextAccessFilter\rules\OwnRule();
        $auth->add($rule);
        
        // add "update" permission
        $update = $auth->createPermission('update');
        $update->description = 'update';
        $auth->add($update);

        // add the "updateOwn" permission and associate the rule with it.
        $updateOwn = $auth->createPermission('updateOwn');
        $updateOwn->description = 'update own';
        $updateOwn->ruleName = $rule->name;
        $auth->add($updateOwn);

        // make "updateOwn" child from "update"
        $auth->addChild($update,$updateOwn);
        
        // add "delete" permission
        $delete = $auth->createPermission('delete');
        $delete->description = 'delete';
        $auth->add($delete);
        
        // add the "deleteOwn" permission and associate the rule with it.
        $deleteOwn = $auth->createPermission('deleteOwn');
        $deleteOwn->description = 'delete own';
        $deleteOwn->ruleName = $rule->name;
        $auth->add($deleteOwn);

        // make "deleteOwn" child from "delete"
        $auth->addChild($delete,$deleteOwn);
        
        


        // add "reader" role and give this role the "view" permission
        $reader = $auth->createRole('reader');
        $auth->add($reader);
        $auth->addChild($reader, $view);

        // add "moderator" role and give this role the "create" permission
        // as well as the permissions of the "updateOwn" and "deleteOwn" role
        // and the permissions of the "reader" role
        $moderator = $auth->createRole('moderator');
        $auth->add($moderator);
        $auth->addChild($moderator, $create);
        $auth->addChild($moderator, $updateOwn);
        $auth->addChild($moderator, $deleteOwn);
        $auth->addChild($moderator, $reader);

        // add "admin" role and give this role the "update" and "delete" permission
        // as well as the permissions of the "moderator" role
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $update);
        $auth->addChild($admin, $delete);
        $auth->addChild($admin, $moderator);

        // Assign roles to users. 1, 2 and 3 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        $auth->assign($admin, 1);
        $auth->assign($moderator, 2);
        $auth->assign($normal, 3);
    }

}
```

###in controller

```php
    public function behaviors() {
        return [
            'context' =>[
                'class' => \claudejanz\contextAccessFilter\filters\ContextFilter::className(),
                'only' => ['update','delete'],
                // model to load
                'modelName' => Vin::className(),
                
            ],
            'access' => [
                'class' => \claudejanz\contextAccessFilter\filters\AccessControl::className(),
                'only' => ['create', 'update','delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['create'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['update'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['delete'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    // update function
    public function actionUpdate($id) {
        $model = $this->model;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }
    // delete function 
    public function actionDelete($id) {
        $this->model->delete();

        return $this->redirect(['index']);
    }
```

