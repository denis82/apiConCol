<?php
namespace app\behaviors;

use yii\base\Behavior;

class MyBehavior extends Behavior
{
//       public function events()
//     {
//         return [
//             \yii\web\Application::EVENT_BEFORE_REQUEST => 'beforeValidate',
//         ];
//     }

    public function getDate()
    {
        return date("Y-d-m");
        //die();
    }
}