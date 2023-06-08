<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 22.04.18
 * Time: 18:48
 */

namespace backend\components\ProcessOrder\Status;

use Yii;

trait StatusAccess
{
    public function canChangeStatusPermission($status)
    {
        return \Yii::$app->user->can('set_status_' . $this->statusToKey($status));
    }
    private function canChangeAllPermission()
    {
        return \Yii::$app->user->can('all_change_status');
    }
    public function canChangeStatus($status)
    {
        /*
         * Надо будет разобраться с RBAC.file
         * Как я понял, тут проверяются полномочия пользователя на смену статуса заказа.
         * т.е. имеет ли право пользователь менять статус заказа.
         */
        if (!$this->canChangeStatusPermission($status)) {
            return false;
        }
        if ($status == 1) {
            if (($this->manager_id == Yii::$app->user->id || $this->canChangeAllPermission()) && $this->status == 0) {
                return true;
            }
        } elseif ($status == 2) {
            if (($this->collector_id == Yii::$app->user->id || $this->canChangeAllPermission()) && $this->status == 1) {
                return true;
            }
        } elseif ($status == 3) {
            if (($this->manager_id == Yii::$app->user->id || $this->collector_id == Yii::$app->user->id || $this->canChangeAllPermission()) && (in_array($this->status, [0, 1, 2]))) {
                return true;
            }
        } elseif ($status == 4) {
			
            if (($this->collector_id == Yii::$app->user->id || $this->canChangeAllPermission()) && $this->status == 3) {
                return true;
            }
        } elseif ($status == 5) {
		//	return false;
            if ((
                    $this->manager_id == Yii::$app->user->id
                    ||
                    $this->driver_id == Yii::$app->user->id
                    ||
                    $this->canChangeAllPermission()
                    ||
                    \Yii::$app->user->can('force_set_status_success')
                ) &&
                ($this->status == 3 || $this->status == 4)) {
                return true;
            }
        } elseif ($status == 6) {
            if ((
                    $this->manager_id == Yii::$app->user->id
                    ||
                    $this->driver_id == Yii::$app->user->id
                    ||
                    $this->canChangeAllPermission()
                ) &&
                $this->status == 4) {
                return true;
            }
        } elseif ($status == 7) {
            //TODO проверить где используеться этот статус
        } elseif ($status == 8) {
            if (($this->manager_id == Yii::$app->user->id || $this->canChangeAllPermission()) && in_array($this->status, [0, 1, 2, 3])) {
                return true;
            }
        } elseif ($status == 9) {
            if ((
                    $this->manager_id == Yii::$app->user->id
                    ||
                    $this->driver_id == Yii::$app->user->id
                    ||
                    $this->canChangeAllPermission()
                ) && in_array($this->status, [0, 1, 2, 3])) {
                return true;
            }
        } elseif ($status == 10) {
            if ((
                    $this->manager_id == Yii::$app->user->id
                    ||
                    $this->canChangeAllPermission()
                ) && in_array($this->status, [8, 9])) {
                return true;
            }
        }
        return false;
    }
}