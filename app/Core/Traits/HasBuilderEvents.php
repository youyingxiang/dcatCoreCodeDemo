<?php

namespace App\Core\Traits;

use App\Core\Support\Helper;

trait HasBuilderEvents
{
    /**
     * Register a resolving event.
     *
     * @param callable $callback
     * @param bool     $once
     * @see 通过resolving方法设置的回调函数会在类被实例化时触发
     */
    public static function resolving(callable $callback, bool $once = false)
    {
        static::addBuilderListeners('builder.resolving', $callback, $once);
    }

    /**
     * Call the resolving callbacks.
     *
     * @param array ...$params
     * @
     */
    protected function callResolving(...$params)
    {
        $this->fireBuilderEvent('builder.resolving', ...$params);
    }

    /**
     * Register a composing event.
     *
     * @param callable $callback
     * @param bool     $once
     * @see 通过composing方法设置的回调函数会在类render方法被调用时触发；
     */
    public static function composing(callable $callback, bool $once = false)
    {
        static::addBuilderListeners('builder.composing', $callback, $once);
    }

    /**
     * Call the composing callbacks.
     *
     * @param array ...$params
     */
    protected function callComposing(...$params)
    {
        $this->fireBuilderEvent('builder.composing', ...$params);
    }

    /**
     * @param $listeners
     * @param array ...$params
     */
    protected function fireBuilderEvent($key, ...$params)
    {
        $storage = Helper::getContext();

        $key = static::formatBuilderEventKey($key);

        $listeners = $storage->get($key) ?: [];

        foreach ($listeners as $k => $listener) {
            [$callback, $once] = $listener;

            if ($once) {
                unset($listeners[$k]);
            }
            call_user_func($callback, $this, ...$params);
        }

        $storage[$key] = $listeners;
    }

    /**
     * @param string   $key
     * @param callable $callback
     * @param bool     $once
     */
    protected static function addBuilderListeners($key, $callback, $once)
    {
        // Illuminate\Support\Fluent
        $storage = Helper::getContext();

        $key = static::formatBuilderEventKey($key);
        // App\Core\Layout\Content::builder.composing

        $listeners = $storage->get($key) ?: [];

        $listeners[] = [$callback, $once];

        $storage[$key] = $listeners;
    }

    protected static function formatBuilderEventKey($key)
    {
        return static::class.'::'.$key;
    }
}
