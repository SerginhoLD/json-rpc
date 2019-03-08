# JSON-RPC 2.0

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = new \SerginhoLD\Json\Rpc\Application();

try
{
    $app->addMethodsFromDirectory(__DIR__ . '/src/Example');

    $request = [
        'jsonrpc' => '2.0',
        'method' => 'first',
        'params' => [
            'number' => 1.1,
            'url' => '/test',
        ],
        'id' => 123,
    ];

    $app->runData($request);
}
catch (\Throwable $e)
{
    $response = new \SerginhoLD\Json\Rpc\Response(null);
    $response->withError(\SerginhoLD\Json\Rpc\JsonRpcException::CODE_SERVER_ERROR, $e->getMessage());

    echo $app->toJson($response);
}
```
