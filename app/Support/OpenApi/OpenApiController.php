<?php

namespace App\Support\OpenApi;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class OpenApiController
{
    public function __invoke(): Response
    {
        $openApiUrl = url('/openapi.yaml');
        $html = <<<HTML
<!doctype html>
<html>
<head>
  <title>Visatko API Documentation</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
</head>
<body>
  <div id="swagger-ui"></div>
  <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
  <script>SwaggerUIBundle({url: "{$openApiUrl}", dom_id: "#swagger-ui", persistAuthorization: true});</script>
</body>
</html>
HTML;

        if (! File::exists(public_path('openapi.yaml'))) {
            File::copy(base_path('docs/openapi/openapi.yaml'), public_path('openapi.yaml'));
        }

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
