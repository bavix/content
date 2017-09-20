<?php

namespace Bavix\Context;

interface Content
{
    public function export(): array;
    public function getStore(): array;

    public function encrypt($data): string;
    public function decrypt(string $data);
    public function cleanup();

    public function remove(string $name): void;
    public function set(string $name, $data): void;
    public function get(string $name, $default = null);

    public function __toString(): string;
    public function __isset(string $name): bool;
    public function __unset(string $name): void;
    public function __set(string $name, $data): void;
    public function __get(string $name);
}
