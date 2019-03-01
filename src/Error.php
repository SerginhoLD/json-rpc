<?php
declare(strict_types = 1);

namespace SerginhoLD\Json\Rpc;

/**
 * Class Error
 * @package SerginhoLD\Json\Rpc
 */
class Error implements ErrorInterface
{
    /** @var int */
    private $code;

    /** @var string */
    private $message;

    /** @var array */
    private $data = [];

    /**
     * @param int $code
     * @param string $message
     */
    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return !empty($this->data) ? $this->data : null;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $result = [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
        ];

        if ($data = $this->getData())
        {
            $result['data'] = $data;
        }

        return $result;
    }
}
