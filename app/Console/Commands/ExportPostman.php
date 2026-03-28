<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;

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
        if (! is_dir($postmanDir)) {
            mkdir($postmanDir, 0755, true);
        }

        $outputPath = $postmanDir.'/'.$this->option('output');
        file_put_contents($outputPath, json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info("Postman collection exported successfully to: {$outputPath}");
        $this->line('You can now import this file into Postman.');

        return 0;
    }

    /**
     * Get all API routes
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
                $methods = array_filter($methods, function ($method) {
                    return ! in_array($method, ['HEAD', 'OPTIONS']);
                });

                if (empty($methods)) {
                    continue;
                }

                $middleware = $route->middleware();
                $isProtected = in_array('auth:sanctum', $middleware);
                $action = $route->getActionName();
                $controller = 'Other';

                // Extract Controller Name
                if (str_contains($action, '@')) {
                    $actionParts = explode('@', $action);
                    $fullController = $actionParts[0];
                    if (class_exists($fullController)) {
                        $reflection = new ReflectionClass($fullController);
                        // Get short name without 'Controller' suffix if possible, or just class name
                        $shortName = $reflection->getShortName();
                        $controller = str_replace('Controller', '', $shortName);
                        
                        // Handle specific cases for better naming
                        if ($controller === 'AuthSession') $controller = 'Auth';
                        if ($controller === 'GoogleAuth') $controller = 'Auth';
                    }
                }

                foreach ($methods as $method) {
                    $routes[] = [
                        'uri' => $uri,
                        'method' => $method,
                        'name' => $route->getName() ?? '',
                        'action' => $action,
                        'controller' => $controller,
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
     */
    protected function generatePostmanCollection(array $routes): array
    {
        $baseUrl = config('app.url');
        $grouped = $this->groupRoutes($routes);

        $items = $this->buildNestedFolders($grouped, $baseUrl);

        return [
            'info' => [
                'name' => config('app.name').' API',
                'description' => 'API Collection for '.config('app.name'),
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => $items,
            'auth' => [
                'type' => 'bearer',
                'bearer' => [
                    [
                        'key' => 'token',
                        'value' => '{{access_token}}',
                        'type' => 'string',
                    ],
                ],
            ],
            'variable' => [
                [
                    'key' => 'base_url',
                    'value' => $baseUrl,
                    'type' => 'string',
                ],
                [
                    'key' => 'access_token',
                    'value' => '',
                    'type' => 'string',
                ],
            ],
        ];
    }

    /**
     * Build nested folder structure for Postman
     */
    protected function buildNestedFolders(array $grouped, string $baseUrl): array
    {
        $folders = [];

        foreach ($grouped as $folderName => $groupRoutes) {
            $folderItems = [];
            foreach ($groupRoutes as $route) {
                $folderItems[] = $this->createPostmanRequest($route, $baseUrl);
            }

            $folders[] = [
                'name' => $folderName,
                'item' => $folderItems,
            ];
        }

        return $folders;
    }

    /**
     * Group routes by controller
     */
    protected function groupRoutes(array $routes): array
    {
        $grouped = [];

        foreach ($routes as $route) {
            $controller = $route['controller'];
            
            if (! isset($grouped[$controller])) {
                $grouped[$controller] = [];
            }

            $grouped[$controller][] = $route;
        }

        // Sort groups alphabetically
        ksort($grouped);

        return $grouped;
    }

    /**
     * Create a Postman request item
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
            'raw' => '{{base_url}}/'.$uri,
            'host' => ['{{base_url}}'],
            'path' => explode('/', $uri),
        ];

        // Build headers
        $headers = [
            [
                'key' => 'Accept',
                'value' => 'application/json',
                'type' => 'text',
            ],
            [
                'key' => 'Content-Type',
                'value' => 'application/json',
                'type' => 'text',
            ],
        ];

        // Build body based on method and route
        $body = $this->generateRequestBody($route);

        $request = [
            'name' => $name,
            'request' => [
                'method' => $method,
                'header' => $headers,
                'url' => $url,
            ],
            'response' => [],
        ];

        if ($body) {
            $request['request']['body'] = $body;
        }

        return $request;
    }

    /**
     * Generate request name
     */
    protected function generateRequestName(array $route): string
    {
        $uri = $route['uri'];
        
        // Remove api/ prefix
        $cleanUri = str_replace('api/', '', $uri);

        // Replace parameters with readable names
        $cleanUri = preg_replace('/\{(\w+)\}/', ':$1', $cleanUri);

        // If it's the root of a resource (e.g. "reminders"), name it "List" or "Create"
        $parts = explode('/', $cleanUri);
        $lastPart = end($parts);
        
        if ($lastPart === '' || str_starts_with($lastPart, ':')) {
             $methodMap = [
                'GET' => 'List / Show',
                'POST' => 'Create',
                'PUT' => 'Update',
                'PATCH' => 'Update',
                'DELETE' => 'Delete',
            ];
            return $methodMap[$route['method']] ?? $cleanUri;
        }

        // Convert to readable name
        $name = str_replace(['/', '-', '_'], ' ', $cleanUri);
        $name = ucwords($name);

        return $name;
    }

    /**
     * Generate request body based on route
     */
    protected function generateRequestBody(array $route): ?array
    {
        $method = $route['method'];
        $uri = $route['uri'];
        $controller = $route['controller'];

        // Only POST, PUT, PATCH need body
        if (! in_array($method, ['POST', 'PUT', 'PATCH'])) {
            return null;
        }

        $bodyData = [];

        // Define bodies based on Controller and URI keywords
        if ($controller === 'Auth') {
            if (str_contains($uri, 'login')) {
                $bodyData = [
                    'email' => 'admin@gmail.com',
                    'password' => '123456789',
                    'device_name' => 'postman',
                ];
            } elseif (str_contains($uri, 'google')) {
                $bodyData = ['token' => 'google_oauth_token'];
            }
        } elseif ($controller === 'Reminder') {
            if (str_contains($uri, 'toggle')) {
                $bodyData = []; // No body needed usually, or maybe status
            } elseif (str_contains($uri, 'exceptions')) {
                 $bodyData = ['date' => '2025-12-05', 'reason' => 'Doctor appointment'];
            } else {
                $bodyData = [
                    'type' => 'medication',
                    'title' => 'Take antibiotic',
                    'description' => 'After food',
                    'reminder_date' => date('Y-m-d'),
                    'reminder_time' => '09:00',
                    'frequency' => 'daily',
                ];
            }
        } elseif ($controller === 'Assessment') {
             $bodyData = [
                'image_path' => ['path/to/img1.jpg'], // Or handle file upload structure if needed
                'risk_percentage' => 50,
                'recommendation' => 'See a doctor',
                'report_text' => 'Analysis results...',
            ];
        } elseif ($controller === 'Chat') {
             $bodyData = [
                'receiver_id' => 1,
                'message' => 'Hello doctor',
            ];
        } elseif ($controller === 'ContactUs') {
             $bodyData = [
                'name' => 'User Name',
                'email' => 'user@example.com',
                'message' => 'My inquiry...',
            ];
        } else {
            // Generic fallback
            $bodyData = [
                'field' => 'value',
            ];
        }

        return [
            'mode' => 'raw',
            'raw' => json_encode($bodyData, JSON_PRETTY_PRINT),
            'options' => [
                'raw' => [
                    'language' => 'json',
                ],
            ],
        ];
    }
}
