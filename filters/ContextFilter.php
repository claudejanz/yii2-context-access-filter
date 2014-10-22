<?php

namespace claudejanz\contextAccessFilter\filters;

use Yii;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/*
 * Copyright (C) 2014 Claude Janz <claude.janz@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of ContextFilter
 *
 * @author Claude Janz <claude.janz@gmail.com>
 */
class ContextFilter extends ActionFilter {

    public $modelName;
    public $target;

    public function beforeAction($action) {
        // controlle params
        if(!isset($this->modelName))throw new InvalidConfigException(Yii::t('app','the "modelName" must be set for "{class}".',['class'=>__CLASS__]));
        if(!isset($this->target))throw new InvalidConfigException(Yii::t('app','the "target" must be set for "{class}".',['class'=>__CLASS__]));
        
        //get request params
        $queryParams = Yii::$app->getRequest()->getQueryParams();
        
        // load model
        $model = call_user_func([$this->modelName, 'findOne'], $queryParams[join(',', call_user_func([$this->modelName, 'primaryKey']))]);
        if ($model !== null) {
            // return model to controller
            $action->controller->{$this->target} = $model;
            return true;
        } else {
            throw new NotFoundHttpException(Yii::t('app','The requested page does not exists.'));
        }
    }
}
