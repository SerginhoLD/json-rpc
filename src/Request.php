<?php
declare(strict_types = 1);

namespace SerginhoLD\Json\Rpc;

/**
 * Class Request
 * @package SerginhoLD\Json\Rpc
 */
class Request implements RequestInterface
{
    /** @var string */
    private $method;

    /** @var array|null */
    private $params;

    /** @var int|string|null */
    private $id;

    /** @var bool */
    private $isNotification = false;

    /**
     * @param string $method
     * @param array|null $params
     * @param int|string|null $id
     * @param bool $isNotification
     */
    public function __construct(string $method, array $params = null, $id = null, bool $isNotification = false)
    {
        $this->method = $method;
        $this->params = $params;
        $this->id = $id;
        $this->isNotification = $isNotification;
    }

    /**
     * @return string
     */
    public function getJsonRpc(): string
    {
        return self::JSON_RPC_2;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isNotification(): bool
    {
        return $this->isNotification;
    }
}
