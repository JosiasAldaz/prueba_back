<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\User;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use app\components\AuthComponent;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }
    // Registro de un nuevo usuario
    public function actionRegistro($username, $password)
    {
    if (empty($username) || empty($password)) {
        throw new BadRequestHttpException('Se requiere un nombre de usuario y una contraseña.');
    }

    // Buscar si ya existe un usuario con el mismo nombre de usuario
    $existingUser = User::find()->where(['username' => $username])->one();

    // Si el usuario ya existe, no se permite el registro
    if ($existingUser) {
        
        return ['error' => 'El nombre de usuario ya está en uso.'];
        
    } else {
            // Crear un nuevo usuario si no existe
            $user = new User();
            $user->username = $username;
            $user->setPassword($password);
            $user->generateAuthKey();

            if ($user->save()) {
                return ['message' => 'Usuario creado correctamente.'];
            } else {
                return ['error' => 'Error al crear el usuario.', 'errors' => $user->errors];
            }
        }
    }


    public function actionLogin($username,$password)
    {
        $user = User::findByUsername($username);
        if (!$user || !$user->validatePassword($password)) {
            return ['error' => 'El usuario o la contraseña son incorrectos.'];
        }

        $authComponent = new AuthComponent();

        $token = $authComponent->generateToken($user->id);

        return ['token' => $token];
    }

    public function actionProtected()
    {
        $user = Yii::$app->user->identity; 

        return [
            'message' => 'No tiene acceso a este método si no está autenticado.',
            'user' => $user,
        ];
    }

    
}
