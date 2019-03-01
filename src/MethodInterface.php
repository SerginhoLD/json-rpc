<?php
namespace SerginhoLD\Json\Rpc;

/**
 * Interface MethodInterface
 * @package SerginhoLD\Json\Rpc
 */
interface MethodInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null
     */
    public function run(RequestInterface $request): ?ResponseInterface;
}
