<?php

namespace nordsoftware\yii_account\components;

use nordsoftware\yii_account\AccountModule;

class UserIdentity extends \CUserIdentity
{
    /**
     * @var integer
     */
    private $_id;

    /**
     * @inheritDoc
     */
    public function authenticate()
    {
        $account = $this->loadModel();

        if ($account === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if (!$account->verifyPassword($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->errorCode = self::ERROR_NONE;
        }

        // No errors occurred, set user identity.
        if ($this->errorCode === self::ERROR_NONE) {
            $this->_id = $account->id;
            $this->username = $account->username;
        }

        return !$this->errorCode;
    }

    /**
     * @return \nordsoftware\yii_account\models\ar\Account|\YiiPassword\Behavior
     */
    protected function loadModel()
    {
        /** @var \nordsoftware\yii_account\AccountModule $module */
        $module = \Yii::app()->getModule(AccountModule::MODULE_ID);

        return \CActiveRecord::model($module->modelClass)->find(
            array(
                'condition' => 'username=:username',
                'params' => array(':username' => strtolower($this->username)),
            )
        );
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }
}