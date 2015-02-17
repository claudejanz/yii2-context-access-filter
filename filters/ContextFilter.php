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
    private $_model;

    public function beforeAction($action) {
        // controlle params
        if(!isset($this->modelName))throw new InvalidConfigException(Yii::t('app','the "modelName" must be set for "{class}".',['class'=>__CLASS__]));
        
        //get request params
        $queryParams = Yii::$app->getRequest()->getQueryParams();
        // load model
        $model = call_user_func([$this->modelName, 'findOne'], $queryParams['id']);
        if ($model !== null) {
            // return model to controller
            $this->_model = $model;
            return true;
        } else {
            $arr = preg_split('@\\\\@', $this->modelName ,-1, PREG_SPLIT_NO_EMPTY);
            $modelName = end($arr);
            throw new NotFoundHttpException(Yii::t('app','The requested {modelName} does not exists.',['modelName'=>$modelName]));
        }
    }
    public function getModel(){
        return $this->_model;
    }
}
