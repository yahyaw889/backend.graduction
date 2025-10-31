<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ExportPostman extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:postman {--output=postman_collection.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export API routes to Postman collection format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating Postman collection...');

        $routes = $this->getApiRoutes();
        $collection = $this->generatePostmanCollection($routes);
        
        // Create postman folder if it doesn't exist
        $postmanDir = base_path('postman');
        if (!is_dir($postmanDir)) {
            mkdir($postmanDir, 0755, true);
        }
        
        $outputPath = $postmanDir . '/' . $this->option('output');
        file_put_contents($outputPath, json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info("Postman collection exported successfully to: {$outputPath}");
        $this->line("You can now import this file into Postman.");

        return 0;
    }

    /**
     * Get all API routes
     *
     * @return array
     */
    protected function getApiRoutes(): array
    {
        $routes = [];
        $routeCollection = Route::getRoutes();

        foreach ($routeCollection as $route) {
            $uri = $route->uri();
            
            // Only include API routes
            if (Str::startsWith($uri, 'api/')) {
                $methods = $route->methods();
                
                // Exclude HEAD and OPTIONS
                $methods = array_filter($methods, function($method) {
                    return !in_array($method, ['HEAD', 'OPTIONS']);
                });

                if (empty($methods)) {
                    continue;
                }

                $middleware = $route->middleware();
                $isProtected = in_array('auth:sanctum', $middleware);

                foreach ($methods as $method) {
                    $routes[] = [
                        'uri' => $uri,
                        'method' => $method,
                        'name' => $route->getName() ?? '',
                        'action' => $route->getActionName(),
                        'middleware' => $middleware,
                        'isProtected' => $isProtected,
                    ];
                }
            }
        }

        return $routes;
    }

    /**
     * Generate Postman collection structure
     *
     * @param array $routes
     * @return array
     */
    protected function generatePostmanCollection(array $routes): array
    {
        $baseUrl = config('app.url');
        $grouped = $this->groupRoutes($routes);

        $items = $this->buildNestedFolders($grouped, $baseUrl);

        return [
            'info' => [
                'name' => config('app.name') . ' API',
                'description' => 'API Collection for ' . config('app.name'),
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => $items,
            'auth' => [
                'type' => 'bearer',
                'bearer' => [
                    [
                        'key' => 'token',
                        'value' => '{{access_token}}',
                        'type' => 'string'
                    ]
                ]
            ],
            'variable' => [
                [
                    'key' => 'base_url',
                    'value' => $baseUrl,
                    'type' => 'string'
                ],
                [
                    'key' => 'access_token',
                    'value' => '',
                    'type' => 'string'
                ]
            ]
        ];
    }

    /**
     * Build nested folder structure for Postman
     *
     * @param array $grouped
     * @param string $baseUrl
     * @return array
     */
    protected function buildNestedFolders(array $grouped, string $baseUrl): array
    {
        $versionFolders = [];

        foreach ($grouped as $groupKey => $groupRoutes) {
            // Split "V1|Auth" into version and resource
            [$version, $resource] = explode('|', $groupKey);

            // Create version folder if not exists
            if (!isset($versionFolders[$version])) {
                $versionFolders[$version] = [
                    'name' => $version,
                    'item' => []
                ];
            }

            // Create resource subfolder
            $resourceItems = [];
            foreach ($groupRoutes as $route) {
                $resourceItems[] = $this->createPostmanRequest($route, $baseUrl);
            }

            $versionFolders[$version]['item'][] = [
                'name' => $resource,
                'item' => $resourceItems
            ];
        }

        return array_values($versionFolders);
    }

    /**
     * Group routes by version and controller
     *
     * @param array $routes
     * @return array
     */
    protected function groupRoutes(array $routes): array
    {
        $grouped = [];

        foreach ($routes as $route) {
            $uri = $route['uri'];
            
            // Extract version and resource
            if (preg_match('#api/(v\d+)/([^/]+)#', $uri, $matches)) {
                $version = strtoupper($matches[1]);
                $resource = ucfirst($matches[2]);
                $groupKey = "{$version}|{$resource}";
            } else {
                $groupKey = 'Other|General';
            }

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [];
            }

            $grouped[$groupKey][] = $route;
        }

        // Sort groups
        ksort($grouped);

        return $grouped;
    }

    /**
     * Create a Postman request item
     *
     * @param array $route
     * @param string $baseUrl
     * @return array
     */
    protected function createPostmanRequest(array $route, string $baseUrl): array
    {
        $method = $route['method'];
        $uri = $route['uri'];
        $isProtected = $route['isProtected'];

        // Generate request name
        $name = $this->generateRequestName($route);

        // Build URL
        $url = [
            'raw' => '{{base_url}}/' . $uri,
            'host' => ['{{base_url}}'],
            'path' => explode('/', $uri),
        ];

        // Build headers
        $headers = [
            [
                'key' => 'Accept',
                'value' => 'application/json',
                'type' => 'text'
            ],
            [
                'key' => 'Content-Type',
                'value' => 'application/json',
                'type' => 'text'
            ]
        ];

        if ($isProtected) {
            $headers[] = [
                'key' => 'Authorization',
                'value' => 'Bearer {{access_token}}',
                'type' => 'text'
            ];
        }

        // Build body based on method and route
        $body = $this->generateRequestBody($route);

        $request = [
            'name' => $name,
            'request' => [
                'method' => $method,
                'header' => $headers,
                'url' => $url,
            ],
            'response' => []
        ];

        if ($body) {
            $request['request']['body'] = $body;
        }

        return $request;
    }

    /**
     * Generate request name
     *
     * @param array $route
     * @return string
     */
    protected function generateRequestName(array $route): string
    {
        $method = $route['method'];
        $uri = $route['uri'];

        // Remove api/v1 prefix
        $cleanUri = preg_replace('#^api/v\d+/#', '', $uri);
        
        // Convert to readable name
        $name = str_replace(['/', '-', '_'], ' ', $cleanUri);
        $name = ucwords($name);
        
        return "{$method} - {$name}";
    }

    /**
     * Generate request body based on route
     *
     * @param array $route
     * @return array|null
     */
    protected function generateRequestBody(array $route): ?array
    {
        $method = $route['method'];
        $uri = $route['uri'];

        // Only POST, PUT, PATCH need body
        if (!in_array($method, ['POST', 'PUT', 'PATCH'])) {
            return null;
        }

        $bodyData = [];

        // Generate body based on endpoint
        if (Str::contains($uri, 'register')) {
            $bodyData = [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'device_name' => 'postman'
            ];
        } elseif (Str::contains($uri, 'login')) {
            $bodyData = [
                'email' => 'john@example.com',
                'password' => 'password123',
                'device_name' => 'postman'
            ];
        } elseif (Str::contains($uri, 'profile')) {
            $bodyData = [
                'name' => 'John Updated',
                'email' => 'john.updated@example.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123'
            ];
        } else {
            // Generic body
            $bodyData = [
                'field1' => 'value1',
                'field2' => 'value2'
            ];
        }

        return [
            'mode' => 'raw',
            'raw' => json_encode($bodyData, JSON_PRETTY_PRINT),
            'options' => [
                'raw' => [
                    'language' => 'json'
                ]
            ]
        ];
    }
}
