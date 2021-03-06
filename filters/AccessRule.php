<?php

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

namespace claudejanz\contextAccessFilter\filters;

use Yii;

/**
 * Description of AccessRule
 *
 * @author Claude Janz <claude.janz@gmail.com>
 */
class AccessRule extends \yii\filters\AccessRule {

    private $params;

    protected function matchRole($user) {
        if (isset(Yii::$app->controller->model)) {
            $this->params = ['model' => Yii::$app->controller->model];
            foreach ($this->roles as $role) {
                if ($user->can($role, $this->params)) {
                    return true;
                }
            }
        }
        if (parent::matchRole($user))
            return true;
        return false;
    }

}
