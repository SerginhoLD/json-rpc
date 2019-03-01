<?php
namespace SerginhoLD\Json\Rpc;

/**
 * Interface RequestInterface
 * @package SerginhoLD\Json\Rpc
 */
interface RequestInterface
{
    const JSON_RPC_2 = '2.0';

    /**
     * @return string
     */
    public function getJsonRpc(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return array|null
     */
    public function getParams(): ?array;

    /**
     * @return int|string|null
     */
    public function getId();

    /**
     * @return bool
     */
    public function isNotification(): bool;
}
