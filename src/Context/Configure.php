<?php

namespace Bavix\Context;

use Bavix\Exceptions\Invalid;
use Bavix\Exceptions\Runtime;
use Bavix\Slice\Slice;
use Carbon\Carbon;

/**
 * Class Configure
 *
 * @package       Bavix\Context
 *
 * @property-read int         $expire
 * @property-read string|null $domain
 * @property-read bool        $secure
 * @property-read string      $path
 * @property-read bool        $httpOnly
 */
class Configure
{

    /**
     * default value two week
     *
     * @var int
     */
    protected $expire = 1209600;

    /**
     * @var ?string
     */
    protected $domain = null;

    /**
     * @var bool
     */
    protected $secure = false;

    /**
     * @var string
     */
    protected $path = '/';

    /**
     * @var bool
     */
    protected $httpOnly = false;

    /**
     * Configure constructor.
     *
     * @param Slice|array $slice
     */
    public function __construct($slice = null)
    {
        if ($slice)
        {
            $this->update($slice);
        }
    }

    /**
     * @param Slice|array $slice
     *
     * @return self
     */
    protected function update($slice): self
    {
        if (is_array($slice))
        {
            $slice = new Slice($slice);
        }

        $this->expire   = $slice->getData('expire', $this->expire);
        $this->domain   = $slice->getData('domain', $this->domain);
        $this->secure   = $slice->getData('secure', $this->secure);
        $this->path     = $slice->getData('path', $this->path);
        $this->httpOnly = $slice->getData('httpOnly', $this->httpOnly);

        if ($this->expire instanceof Carbon)
        {
            $this->expire = $this->expire->timestamp;
        }

        if ($this->expire > time())
        {
            $this->expire -= time();
        }

        return $this;
    }

    /**
     * @param Slice|array $slice
     *
     * @return Configure
     */
    public function modify($slice): self
    {
        return (clone $this)->update($slice);
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws Invalid
     */
    public function __get(string $name)
    {
        if (property_exists($this, $name))
        {
            return $this->{$name};
        }

        throw new Invalid('Property not found');
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws Invalid
     * @throws Runtime
     */
    public function __set(string $name, $value)
    {
        if (!property_exists($this, $name))
        {
            throw new Invalid('Property not found!');
        }

        throw new Runtime('Property is readOnly!');
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return property_exists($this, $name);
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return [
            // expire
            time() + $this->expire,

            // path
            $this->path,

            // domain
            $this->domain,

            // secure
            $this->secure,

            // httpOnly
            $this->httpOnly,
        ];
    }

}
