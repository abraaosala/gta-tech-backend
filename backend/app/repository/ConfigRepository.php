<?php

declare(strict_types=1);

namespace app\repository;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class ConfigRepository implements ConfigContract {
    protected $items = [];

    public function __construct(array $items = []) {
        $this->items = $items;
    }

    public function has($key) {
        return isset($this->items[$key]);
    }

    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->items;

        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        return $value;
    }

    public function set($key, $value=null) {
        // opcional
        $this->items[$key] = $value;
    }

    public function all()
    {
    }

    public function prepend($key, $value) {}
    public function push($key, $value) {}
}