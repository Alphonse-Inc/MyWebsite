<?php

declare(strict_types=1);

namespace App\Core;

use App\Services\PortfolioService;

final class Router
{
    /** @var list<array{method:string, pattern:string, handler:array{0:class-string,1:string}}> */
    private array $routes = [];

    /** @param array{0:class-string,1:string} $handler */
    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    /** @param array{0:class-string,1:string} $handler */
    public function post(string $pattern, array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    /** @param array{0:class-string,1:string} $handler */
    private function add(string $method, string $pattern, array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern === '' ? '/' : $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) ? $path : '/';
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/') ?: '/';
        }
        $method = strtoupper($method);
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && !($method === 'HEAD' && $route['method'] === 'GET')) {
                continue;
            }
            $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $route['pattern']);
            if (!is_string($regex) || preg_match('#^' . $regex . '$#', $path, $matches) !== 1) {
                continue;
            }
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[] = $value;
                }
            }
            [$class, $action] = $route['handler'];
            $controller = new $class();
            $controller->{$action}(...$params);
            return;
        }
        http_response_code(404);
        $this->missing();
    }

    private function missing(): void
    {
        try {
            if (PortfolioService::installed()) {
                $data = PortfolioService::compose('missing');
                $data['title'] = ($data['settings']['error_404_title'] ?? 'Page not found');
                View::render('errors/404', $data);
                return;
            }
        } catch (\Throwable) {
            // Fall through to a static response.
        }
        echo fallback_html(404, 'Page not found', 'That page is not on this site.');
    }
}
