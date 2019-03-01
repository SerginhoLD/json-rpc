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
     */
    public function run(RequestInterface $request): ?ResponseInterface
    {
        return $this->execute($request, $this->createResponse($request));
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    protected function createResponse(RequestInterface $request): ?ResponseInterface
    {
        if ($request->isNotification())
            return null;

        return new Response($request->getId());
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @return ResponseInterface|null
     */
    abstract protected function execute(RequestInterface $request, ResponseInterface $response = null): ?ResponseInterface;
}
