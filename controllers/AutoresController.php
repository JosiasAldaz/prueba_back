<?php

namespace app\controllers;

use Yii;
use app\models\Autor;
use yii\rest\ActiveController;
use yii\web\Response;

class AutoresController extends ActiveController
{
    public $modelClass = 'app\models\Autor';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    protected function findModel($id)
    {
        if (($model = Autor::findOne($id)) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('Autor no encontrado');
    }


    public function actionMostrarAutores()
    {
        return Autor::find()->all();
    }

    public function actionBuscarAutor($id)
    {
        try {
            $model = $this->findModel($id);
            return $model;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException('El autor solicitado no se encuentra disponible.');
        }
    }

    public function actionCrearAutor()    {
        $model = new Autor();
        $data = Yii::$app->request->getBodyParams();
        $model->setAttributes($data);

        // Validar y guardar
        if ($model->validate() && $model->save()) {
            return $model;
        } else {
            return ['errors' => $model->errors];
        }
    }


    public function actionActualizarAutor($id){
        $model = $this->findModel($id);
        
        if ($model === null) {
            throw ('Autor no encontrado');
        }

        // Asignar directamente los datos del request al modelo
        $data = Yii::$app->request->getBodyParams();
        $model->setAttributes($data);

        // Validar y guardar usando un operador ternario
        return ($model->validate() && $model->save()) ? $model : ['errors' => $model->errors];
    }


    public function actionBorrarAutor($id)
    {
        // Usar el método findOne() directamente
        $model = Autor::findOne($id);
        
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException("Autor con ID $id no encontrado.");
        }

        // Eliminar el modelo y verificar si la eliminación fue exitosa
        if ($model->delete() !== false) {
            return ['status' => 'Autor eliminado correctamente.'];
        } else {
            return ['status' => 'Error al intentar eliminar el autor.'];
        }
    }

    
}
