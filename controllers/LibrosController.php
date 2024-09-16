<?php

namespace app\controllers;
use app\models\Libros;
use Yii;

use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth; // Importa esto si estás usando autenticación bearer token

class LibrosController extends ActiveController
{
    public $modelClass = 'app\models\Libros';


    public function behaviors()
    {
        //VALIDACIÓN DEL TOKEN PARA INGRESAR A LOS MÉTODOS
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }

    public function actionMostrarLibros()
    {
        // Retornar todos los libros
        return Libros::find()->all();
    }

    // Método para encontrar un libro por su ID y lanzar una excepción si no se encuentra
    protected function findModel($id)
    {
        $model = Libros::findOne($id);
        if ($model !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('El libro solicitado no existe.');
        }
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException("Object not found: $id");
        }
    }

    public function actionCrearLibro()
    {
        $model = new Libros();
        $model->load(Yii::$app->request->getBodyParams(), '');
        if ($model->save()) {
            return $model;
        } else {
            return $model->getErrors();
        }
    }

    public function actionActualizarLibro($id)
    {
        $model = Libros::find()->where(['_id'=>$id])->one();
        if ($model !== null) {
            $model->load(Yii::$app->request->getBodyParams(), '');
            if ($model->save()) {
                error_log('Libro actualizado con éxito: ' . $model->_id);
                return $model;
            } else {
                error_log('Error al actualizar el libro: ' . print_r($model->getErrors(), true));
                return $model->getErrors();
            }
        } else {
            throw new \yii\web\NotFoundHttpException('Libro no encontrado');
        }
    }

    public function actionBorrarLibro($id)
    {
        $model = Libros::findOne($id);
        if ($model !== null) {
            $model->delete();
            return 'Libro eliminado correctamente.';
        } else {
            return 'Libro no encontrado.';
        }
    }

    // Método adicional para agregar un autor a un libro
    public function actionAddAuthor($id)
    {
        $libro = $this->findModel($id);
        $autorId = Yii::$app->request->bodyParams['autor_id'];

        $autores = is_array($libro->autores) ? $libro->autores : [];
        if (!in_array($autorId, $autores)) {
            $autores[] = $autorId;
            $libro->autores = $autores;
        }

        // Establece el escenario de actualización antes de guardar
        $libro->scenario = Libros::SCENARIO_UPDATE;

        if ($libro->save()) {
            return $libro;
        } else {
            return ['errors' => $libro->errors];
        }
    }


}
