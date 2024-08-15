<?php

namespace app\modules\admin;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

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
    
    public function behaviors()
    {
        return [
            'access'    =>  [
                'class' =>  AccessControl::className(),
                'denyCallback'  =>  function($rule, $action)
                {
                    throw new NotFoundHttpException();
                },
                'rules' =>  [
                    [
                        'allow' =>  true,
                        'matchCallback' =>  function($rule, $action)
                        {
                            if(Yii::$app->user->isGuest) {
                                throw new NotFoundHttpException();
                            }
                            return Yii::$app->user->identity->isAdmin;
                        }
                    ]
                ]
            ]
        ];
    }
}
