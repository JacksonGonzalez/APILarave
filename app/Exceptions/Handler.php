<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if($exception instanceof ModelNotFoundException){
            $modelo = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse('No existe ningun registro con el id ingresado en el modelo '.$modelo , 404);
        }

        if($exception instanceof AuthenticationException){
            return $this->unauthenticated($request, $exception);
        }

        if($exception instanceof AuthorizationException){
            return $this->errorResponse('No posee permisos para ejecutar esta acción', 403);
        }

        if($exception instanceof NotFoundHttpException){
            return $this->errorResponse('No se encontro la URL Especificada', 404);
        }

        if($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse('El metodo especificado en la petición no es valido', 405);
        }

        if($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        if($exception instanceof QueryException){
            $codigo = $exception->errorInfo[1];

            if($codigo == 1451){
                return $this->errorResponse('No se puede eliminar el recurso porque esta relacionado con otro recurso.', 409);
            }
        }

        if(config('app.debug')){
            return $this->errorResponse('Falla Inesperada. Intente luego', 500);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception){
        return $this->errorResponse('No autenticado', 401);
    }


    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $this->prepareException($e);
        $errors = $errors->getResponse();
        return $this->errorResponse($errors, 422);
    }
}
