<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 22.04.18
 * Time: 18:48
 */

namespace backend\components\ProcessOrder\Appointments;

use common\components\Debugger as d;

trait AppointmentAccess
{
    public function canLockPermission()
    {
        return \Yii::$app->user->can('appointment_' . $this->statusToKey());
    }
    /**
     * @return bool
     */
    public function canLock()
    {
        if ($this->status == 0) {
            if (!$this->manager_id) {
                return true;
            }
        } elseif (($this->status == 1 || $this->status == 3)) {
            if (!$this->collector_id) {
                return true;
            }
        } elseif ($this->status == 3) {
            if (!$this->driver_id) {
                return true;
            }
        }
        return false;
    }
    public function canUnLock()
    {
        if ($this->status == 0) {
            if ($this->manager_id == \Yii::$app->user->id) {
                return true;
            }
        } elseif ($this->status == 1) {
            if ($this->collector_id == \Yii::$app->user->id) {
                return true;
            }
        } elseif (in_array($this->status, [3, 4])) {
            if ($this->driver_id == \Yii::$app->user->id) {
                return true;
            }
        }
        return false;
    }
}