<?php
declare(strict_types = 1);

namespace SerginhoLD\Json\Rpc;

/**
 * Class Application
 * @package SerginhoLD\Json\Rpc
 */
class Application
{
    /** @var MethodInterface[] */
    private $methods = [];

    /**
     * @param MethodInterface $method
     */
    public function addMethod(MethodInterface $method)
    {
        try
        {
            $this->methods[$method->getName()] = $method;
        }
        catch (\Throwable $e) {} // Method not found
    }

    /**
     * @param string $dirname
     */
    public function addMethodsFromDirectory(string $dirname)
    {
        try
        {
            $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirname));
            $phpFiles = new \RegexIterator($allFiles, '/\.php$/');

            foreach ($phpFiles as $phpFile)
            {
                $content = file_get_contents($phpFile->getRealPath());
                $tokens = token_get_all($content);
                $namespace = '';

                for ($index = 0; isset($tokens[$index]); $index++)
                {
                    if (!isset($tokens[$index][0]))
                        continue;

                    if (T_NAMESPACE === $tokens[$index][0])
                    {
                        $index += 2; // Skip namespace keyword and whitespace

                        while (isset($tokens[$index]) && is_array($tokens[$index]))
                        {
                            $namespace .= $tokens[$index++][1];
                        }
                    }

                    if (T_CLASS === $tokens[$index][0] && T_WHITESPACE === $tokens[$index + 1][0] && T_STRING === $tokens[$index + 2][0])
                    {
                        $index += 2; // Skip class keyword and whitespace
                        $className = $namespace.'\\'.$tokens[$index][1];

                        if (is_a($className, MethodInterface::class, true))
                        {
                            $this->addMethod(new $className());
                        }

                        # break if you have one class per file (psr-4 compliant)
                        # otherwise you'll need to handle class constants (Foo::class)
                        break;
                    }
                }
            }
        }
        catch (\Throwable $e) {} // Method not found
    }

    /**
     * @param string $json
     */
    public function run(string $json)
    {
        $data = @json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            $response = $this->createResponseWithError(JsonRpcException::CODE_PARSE_ERROR, JsonRpcException::MESSAGE_PARSE_ERROR);
            echo $this->toJson($response);
            return;
        }

        $this->runData($data);
    }

    /**
     * @param array $data
     */
    public function runData(array $data)
    {
        if (!isset($data[0]))
        {
            $this->runDataItem($data);
        }
        else
        {
            $this->runDataBatch($data);
        }
    }

    /**
     * @param array $data
     */
    protected function runDataItem(array $data)
    {
        $response = $this->execute($data);

        if ($response !== null)
            echo $this->toJson($response);
    }

    /**
     * @param array $data
     */
    protected function runDataBatch(array $data)
    {
        $responses = [];

        foreach ($data as $item)
        {
            $response = $this->execute($item);

            if ($response)
                $responses[] = $response;
        }

        if (!empty($responses))
            echo $this->toJson($responses);
    }

    /**
     * @param RequestInterface|array $request
     * @return ResponseInterface|null
     */
    public function execute($request): ?ResponseInterface
    {
        $response = null;

        try
        {
            $requestObj = $request;

            if (!$requestObj instanceof RequestInterface)
                $requestObj = $this->createRequestFromArray($request);

            $method = $requestObj->getMethod();

            if (!isset($this->methods[$method]))
                throw new JsonRpcException(JsonRpcException::MESSAGE_METHOD_NOT_FOUND, JsonRpcException::CODE_METHOD_NOT_FOUND);

            $response = $this->methods[$method]->run($requestObj);
        }
        catch (JsonRpcException $e)
        {
            if (isset($data['id']))
            {
                $response = $this->createResponseWithError($e->getCode(), $e->getMessage());
            }
        }
        catch (\Exception $e)
        {
            if (isset($data['id']))
            {
                $response = $this->createResponseWithError(JsonRpcException::CODE_INTERNAL_ERROR, $e->getMessage());
            }
        }
        catch (\Error $e)
        {
            if (isset($data['id']))
            {
                $response = $this->createResponseWithError(JsonRpcException::CODE_SERVER_ERROR, $e->getMessage());
            }
        }

        return $response;
    }

    /**
     * @param array $request
     * @return RequestInterface
     * @throws JsonRpcException
     */
    protected function createRequestFromArray(array $request): RequestInterface
    {
        $invalidKeys = array_filter($request, function ($key) {
            return !in_array($key, [
                'jsonrpc',
                'method',
                'params',
                'id',
            ], true);
        }, ARRAY_FILTER_USE_KEY);

        if (!empty($invalidKeys))
            throw new JsonRpcException(JsonRpcException::MESSAGE_INVALID_REQUEST, JsonRpcException::CODE_INVALID_REQUEST);

        $jsonrpc = $request['jsonrpc'] ?? null;

        if ($jsonrpc !== RequestInterface::JSON_RPC_2)
            throw new JsonRpcException(JsonRpcException::MESSAGE_INVALID_REQUEST, JsonRpcException::CODE_INVALID_REQUEST);

        $method = $request['method'] ?? '';
        $method = trim($method);

        if (empty($method))
            throw new JsonRpcException(JsonRpcException::MESSAGE_INVALID_REQUEST, JsonRpcException::CODE_INVALID_REQUEST);

        $params = $request['params'] ?? null;

        if ($params !== null && !is_array($params))
            throw new JsonRpcException(JsonRpcException::MESSAGE_INVALID_REQUEST, JsonRpcException::CODE_INVALID_REQUEST);

        $id = null;
        $isNotification = !isset($request['id']);

        if (!$isNotification)
        {
            $id = $request['id'] ?? null;

            if ($id !== null && !is_int($id) && !is_string($id))
                throw new JsonRpcException(JsonRpcException::MESSAGE_INVALID_REQUEST, JsonRpcException::CODE_INVALID_REQUEST);
        }

        return $this->createRequest($method, $params, $id, $isNotification);
    }

    /**
     * @param string $method
     * @param array|null $params
     * @param null $id
     * @param bool $isNotification
     * @return RequestInterface
     */
    protected function createRequest(string $method, array $params = null, $id = null, bool $isNotification = false): RequestInterface
    {
        return new Request($method, $params, $id, $isNotification);
    }

    /**
     * @param int $code
     * @param string $message
     * @return ResponseInterface
     */
    protected function createResponseWithError(int $code, string $message): ResponseInterface
    {
        $response = new Response(null);
        $response->withError($code, $message);
        return $response;
    }

    /**
     * @param $response
     * @return string
     */
    public function toJson($response): string
    {
        return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
