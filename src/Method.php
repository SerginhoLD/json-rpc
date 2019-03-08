<?php
declare(strict_types = 1);

namespace SerginhoLD\Json\Rpc;

/**
 * Class Method
 * @package SerginhoLD\Json\Rpc
 */
abstract class Method implements MethodInterface
{
    /** @var string */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     * @throws JsonRpcException
     */
    public function run(RequestInterface $request): ?ResponseInterface
    {
        $isNotification = $request->isNotification();
        $response = $this->execute($request, $isNotification ? null : $this->createResponse($request));

        if (!$isNotification && !$response)
            throw new JsonRpcException('No response', JsonRpcException::CODE_INTERNAL_ERROR);

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    protected function createResponse(RequestInterface $request): ?ResponseInterface
    {
        return new Response($request->getId());
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @return ResponseInterface|null
     */
    abstract protected function execute(RequestInterface $request, ResponseInterface $response = null): ?ResponseInterface;
}
