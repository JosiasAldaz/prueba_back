<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\mongodb\Exception as MongoDBException;
use app\models\User;

class HelloController extends Controller
{
    public function actionInit($username, $password)
    {
        $user = new User();
        $user->username = $username;
        $user->setPassword($password);
        $user->generateAuthKey();

        if ($user->save()) {
            echo "Usuario creado exitosamente.\n";
        } else {
            echo "Error al crear el usuario: " . json_encode($user->errors) . "\n";
        }
    }
}
