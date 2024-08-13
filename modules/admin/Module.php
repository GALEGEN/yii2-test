<?php

namespace app\modules\admin;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // путь к директории layouts модуля
        \Yii::$app->setLayoutPath('@app/modules/admin/views/layouts');
        // файл "admin.php"
        \Yii::$app->layout = 'admin';
        
        // custom initialization code goes here
    }
}
