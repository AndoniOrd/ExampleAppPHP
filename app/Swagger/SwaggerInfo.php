<?php
namespace App\Swagger;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Example App API",
 *         version="1.0.0",
 *         description="API documentation for Example App"
 *     ),
 *     @OA\Server(
 *         url=L5_SWAGGER_CONST_HOST,
 *         description="API Server"
 *     ),
 *     @OA\PathItem(path="/api")
 * )
 */
class SwaggerInfo {}