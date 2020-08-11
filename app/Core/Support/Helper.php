<?php

namespace App\Core\Support;

use App\Core\Layout\SectionManager;
use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class Helper
{
    /**
     * 把给定的值转化为数组.
     *
     * @param $value
     * @param bool $filter
     *
     * @return array
     */
    public static function array($value, bool $filter = true): array
    {
        if (!$value) {
            return [];
        }

        if ($value instanceof \Closure) {
            $value = $value();
        }

        if (is_array($value)) {
        } elseif ($value instanceof Jsonable) {
            $value = json_decode($value->toJson(), true);
        } elseif ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif (is_string($value)) {
            $array = null;

            try {
                $array = json_decode($value, true);
            } catch (\Throwable $e) {
            }

            $value = is_array($array) ? $array : explode(',', $value);
        } else {
            $value = (array)$value;
        }

        return $filter ? array_filter($value, function ($v) {
            return $v !== '' && $v !== null;
        }) : $value;
    }

    /**
     * 把给定的值转化为字符串.
     *
     * @param string|Grid|\Closure|Renderable|Htmlable $value
     * @param array $params
     * @param object $newThis
     *
     * @return string
     */
    public static function render($value, $params = [], $newThis = null): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof \Closure) {
            $newThis && ($value = $value->bindTo($newThis));
            $value = $value(...(array)$params);
        }

        if ($value instanceof Grid) {
            return (string)$value->render();
        }

        if ($value instanceof Renderable) {
            return (string)$value->render();
        }

        if ($value instanceof Htmlable) {
            return (string)$value->toHtml();
        }

        return (string)$value;
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    public static function buildHtmlAttributes($attributes)
    {
        $html = '';

        foreach ((array)$attributes as $key => &$value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            if (is_numeric($key)) {
                $key = $value;
            }

            $element = '';

            if ($value !== null) {
                $element = $key . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8') . '"';
            }

            $html .= $element;
        }

        return $html;
    }

    /**
     * @param string $url
     * @param array $query
     *
     * @return string
     */
    public static function urlWithQuery(?string $url, array $query = [])
    {
        if (!$url || !$query) {
            return $url;
        }

        $array = explode('?', $url);

        $url = $array[0];

        parse_str($array[1] ?? '', $originalQuery);

        return $url . '?' . http_build_query(array_merge($originalQuery, $query));
    }

    /**
     * @param string $url
     * @param string|array|Arrayable $keys
     *
     * @return string
     */
    public static function urlWithoutQuery($url, $keys)
    {
        if (!Str::contains($url, '?') || !$keys) {
            return $url;
        }

        if ($keys instanceof Arrayable) {
            $keys = $keys->toArray();
        }

        $keys = (array)$keys;

        $urlInfo = parse_url($url);

        parse_str($urlInfo['query'], $query);

        Arr::forget($query, $keys);

        $baseUrl = explode('?', $url)[0];

        return $query
            ? $baseUrl . '?' . http_build_query($query)
            : $baseUrl;
    }

    /**
     * @param Arrayable|array|string $keys
     *
     * @return string
     */
    public static function fullUrlWithoutQuery($keys)
    {
        return static::urlWithoutQuery(request()->fullUrl(), $keys);
    }

    /**
     * @param string $url
     * @param string|array $keys
     *
     * @return bool
     */
    public static function urlHasQuery(string $url, $keys)
    {
        $value = explode('?', $url);

        if (empty($value[1])) {
            return false;
        }

        parse_str($value[1], $query);

        foreach ((array)$keys as $key) {
            if (Arr::has($query, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 匹配请求路径.
     *
     * @example
     *      Helper::matchRequestPath(admin_base_path('auth/user'))
     *      Helper::matchRequestPath(admin_base_path('auth/user*'))
     *      Helper::matchRequestPath(admin_base_path('auth/user/* /edit'))
     *      Helper::matchRequestPath('GET,POST:auth/user')
     *
     * @param string $path
     * @param null|string $current
     *
     * @return bool
     */
    public static function matchRequestPath($path, ?string $current = null)
    {
        $request = request();
        $current = $current ?: $request->decodedPath();

        if (Str::contains($path, ':')) {
            [$methods, $path] = explode(':', $path);

            $methods = array_map('strtoupper', explode(',', $methods));

            if (!empty($methods) && !in_array($request->method(), $methods)) {
                return false;
            }
        }

        // 判断路由名称
        if ($request->routeIs($path)) {
            return true;
        }

        if (!Str::contains($path, '*')) {
            return $path === $current;
        }

        $path = str_replace(['*', '/'], ['([0-9a-z-_,])*', "\/"], $path);

        return preg_match("/$path/i", $current);
    }

    /**
     * 生成层级数据.
     *
     * @param array $nodes
     * @param int $parentId
     * @param string|null $primaryKeyName
     * @param string|null $parentKeyName
     * @param string|null $childrenKeyName
     *
     * @return array
     */
    public static function buildNestedArray(
        $nodes = [],
        $parentId = 0,
        ?string $primaryKeyName = null,
        ?string $parentKeyName = null,
        ?string $childrenKeyName = null
    )
    {
        $branch          = [];
        $primaryKeyName  = $primaryKeyName ?: 'id';
        $parentKeyName   = $parentKeyName ?: 'parent_id';
        $childrenKeyName = $childrenKeyName ?: 'children';

        $parentId = is_numeric($parentId) ? (int)$parentId : $parentId;

        foreach ($nodes as $node) {
            $pk = Arr::get($node, $parentKeyName);
            $pk = is_numeric($pk) ? (int)$pk : $pk;

            if ($pk === $parentId) {
                $children = static::buildNestedArray(
                    $nodes,
                    Arr::get($node, $primaryKeyName),
                    $primaryKeyName,
                    $parentKeyName,
                    $childrenKeyName
                );

                if ($children) {
                    $node[$childrenKeyName] = $children;
                }
                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * @param string $section
     * @param null $content
     * @param bool $append
     * @param int $priority
     */
    public static function adminInjectSection(string $section, $content = null, bool $append = true, int $priority = 10): void
    {
        self::getSections()->inject($section, $content, $append, $priority);
    }

    /**
     * @param string $section
     * @param null $default
     * @param array $options
     * @return string
     */
    public static function adminSection(string $section, $default = null, array $options = []): string
    {
        return self::getSections()->yieldContent($section, $default, $options);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|core.context
     * @see 获取上下文
     */
    public static function getContext(): Fluent
    {
        return app('core.context');
    }

    /**
     * @return SectionManager
     */
    public static function getSections(): SectionManager
    {
        return app('core.sections');
    }

}
