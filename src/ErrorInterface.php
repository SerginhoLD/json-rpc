<?php
namespace SerginhoLD\Json\Rpc;

/**
 * Interface ErrorInterface
 * @package SerginhoLD\Json\Rpc
 */
interface ErrorInterface extends \JsonSerializable
{
    /**
     * @return int
     */
    public function getCode(): int;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return array|null
     */
    public function getData(): ?array;

    /**
     * @param array $data
     */
    public function setData(array $data);
}
