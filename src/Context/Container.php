<?php

namespace Bavix\Context;

use Bavix\Helpers\JSON;
use Bavix\Security\Security;

abstract class Container implements Content, \ArrayAccess, \Iterator
{

    /**
     * unpack array store
     *
     * @var array
     */
    protected $rows = [];

    /**
     * packed array store
     *
     * @var array
     */
    protected $store;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var Security
     */
    protected $security;

    /**
     * Container constructor.
     *
     * @param string $password
     */
    public function __construct(?string $password = null)
    {
        $this->password = $password;
    }

    /**
     * @return Security
     */
    protected function security(): Security
    {
        if (!$this->security)
        {
            $this->security = new Security($this->password);
        }

        return $this->security;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function encrypt($data): string
    {
        if (!$this->password)
        {
            return JSON::encode($data);
        }

        return $this->security()
            ->encrypt(JSON::encode($data));
    }

    /**
     * @param string $data
     *
     * @return mixed
     */
    public function decrypt(string $data)
    {
        if (!$this->password)
        {
            return JSON::decode($data);
        }

        $decrypt = $this->security()
            ->decrypt($data);

        return JSON::decode($decrypt);
    }

    /**
     * cleanup content from container
     */
    public function cleanup(): void
    {
        foreach ($this->getStore() as $key => $value)
        {
            $this->remove($key);
        }
    }

    /**
     * @param string $name
     */
    public function remove(string $name): void
    {
        unset($this->rows[$name], $this->store[$name]);
    }

    /**
     * @param string $name
     * @param mixed  $data
     */
    public function set(string $name, $data): void
    {
        $this->store[$name] = $this->encrypt($data);
        $this->rows[$name]  = $data;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        if (isset($this->rows[$name]))
        {
            return $this->rows[$name];
        }

        if (isset($this->store[$name]))
        {
            $this->rows[$name] = $this->decrypt($this->store[$name]);

            return $this->rows[$name];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function export(): array
    {
        return \iterator_to_array($this->asGenerator());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return JSON::encode($this->asGenerator(), $this->jsonOptions());
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->store[$name]);
    }

    /**
     * @param string $name
     */
    public function __unset(string $name): void
    {
        $this->remove($name);
    }

    /**
     * @param string $name
     * @param mixed  $data
     */
    public function __set(string $name, $data): void
    {
        $this->set($name, $data);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * @return \Generator
     */
    protected function asGenerator(): ?\Generator
    {
        foreach ($this->getStore() as $name => $value)
        {
            yield $name => $this->get($name);
        }
    }

    /**
     * @return int
     */
    protected function jsonOptions(): int
    {
        return JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK;
    }

    /**
     * @return array
     */
    public function getStore(): array
    {
        return $this->store;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->get($this->key());
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        next($this->store);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return \key($this->store);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return null !== $this->key();
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        reset($this->store);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return isset($this->store[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

}
