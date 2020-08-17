<?php

namespace App\Core\Layout;

use Illuminate\Support\Str;

class Asset
{
    /**
     * 路径别名.
     *
     * @var array
     */
    protected $pathAlias = [
        // 静态资源路径别名
        '@admin' => 'core/admin',
    ];

    /**
     * 别名.
     *
     * @var array
     */
    protected $alias = [
        '@bootstarp' => [
            'css' => '@admin/bootstarp/css/bootstrap.min.css',
            'js'  => '@admin/bootstarp/js/bootstrap.min.js'
        ]
    ];

    /**
     * js代码.
     *
     * @var array
     */
    public $script = [];

    /**
     * @var array
     */
    public $directScript = [];

    /**
     * css代码.
     *
     * @var array
     */
    public $style = [];

    /**
     * css脚本路径.
     *
     * @var array
     */
    public $css = [];

    /**
     * js脚本路径.
     *
     * @var array
     */
    public $js = [];

    /**
     * 在head标签内加载的js脚本.
     *
     * @var array
     */
    public $headerJs = [

    ];

    /**
     * 基础css.
     *
     * @var array
     */
    public $baseCss = [
        'bootstarp' => '@bootstarp',
    ];

    /**
     * 基础js.
     *
     * @var array
     */
    public $baseJs = [
        'bootstarp' => '@bootstarp',
    ];


    /**
     * 设置需要载入的css脚本.
     *
     * @param string|array $css
     */
    public function css(string $css)
    {
        if (!$css) {
            return;
        }
        $this->css = array_merge(
            $this->css,
            (array)$css
        );
    }

    /**
     * @see
     * @param array $css
     */
    public function baseCss(array $css): void
    {
        $this->baseCss = $css;
    }

    /**
     * 设置需要载入的js脚本.
     *
     * @param string|array $js
     */
    public function js(string $js): void
    {
        if (!$js) {
            return;
        }
        $this->js = array_merge(
            $this->js,
            (array)$js
        );
    }

    /**
     * 根据别名获取资源路径.
     *
     * @param string $path
     * @param string $type
     *
     * @return string|array|null
     */
    public function get(string $path, string $type = 'js')
    {
        if (empty($this->alias[$path])) {
            return $this->url($path);
        }

        $paths = isset($this->alias[$path][$type]) ? (array)$this->alias[$path][$type] : null;
        if (!$paths) {
            return $paths;
        }

        foreach ($paths as &$value) {
            $value = $this->url($value);
        }
        return $paths;
    }

    /**
     * 获取静态资源完整URL.
     *
     * @param string $path
     *
     * @return string
     */
    public function url(string $path): string
    {
        if (!$path) {
            return $path;
        }
        $path = $this->getRealPath($path);
        return config('core.https') ? secure_asset($path) : asset($path);
    }

    /**
     * 获取真实路径.
     * 递归 通过切割"/"把字符切割为一个数组 把第一个为"@"的换为真实路径
     * @param string|null $path
     * @return string|null
     */
    public function getRealPath(?string $path): ?string
    {
        if (!$this->hasAlias($path)) {
            return $path;
        }
        return implode(
            '/',
            array_map(
                function ($v) {
                    $v = $this->pathAlias[$v] ?? $v;

                    if (!$this->hasAlias($v)) {
                        return $v;
                    }

                    return $this->getRealPath($v);
                },
                explode('/', $path)
            )
        );
    }

    /**
     * 判断是否含有别名.
     *
     * @param string $value
     *
     * @return bool
     */
    protected function hasAlias(string $value): bool
    {
        return $value && mb_strpos($value, '@') === 0;
    }


    /**
     * 设置基础js脚本.
     *
     * @param array $js
     */
    public function baseJs(array $js): void
    {
        $this->baseJs = $js;
    }

    /**
     * 设置js代码.
     *
     * @param string|array $script
     * @param bool $direct
     */
    public function script($script, bool $direct = false): void
    {
        if (!$script) {
            return;
        }
        if ($direct) {
            $this->directScript = array_merge($this->directScript, (array)$script);
        } else {
            $this->script = array_merge($this->script, (array)$script);
        }
    }

    /**
     * 设置css代码.
     *
     * @param string $style
     */
    public function style($style): void
    {
        if (!$style) {
            return;
        }
        $this->style = array_merge($this->style, (array)$style);
    }


    /**
     * 合并基础css脚本.
     */
    protected function mergeBaseCss(): void
    {
        $this->css = array_merge($this->baseCss, $this->css);
    }

    /**
     * @return string
     */
    public function cssToHtml(): string
    {
        $this->mergeBaseCss();

        $html = '';

        foreach (array_unique($this->css) as &$v) {
            if (!$paths = $this->get($v, 'css')) {
                continue;
            }

            foreach ((array)$paths as $path) {
                $html .= "<link rel=\"stylesheet\" href=\"{$this->withVersionQuery($path)}\">";
            }
        }

        return $html;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function withVersionQuery(string $url): string
    {
        if (!Str::contains($url, '?')) {
            $url .= '?';
        }

        $ver = 'v' . config('core.version');

        return Str::endsWith($url, '?') ? $url . $ver : $url . '&' . $ver;
    }

    /**
     * 合并基础js脚本.
     */
    protected function mergeBaseJs(): void
    {
        $this->js = array_merge($this->baseJs, $this->js);
    }

    /**
     * @return string
     */
    public function jsToHtml(): string
    {
        $this->mergeBaseJs();

        $html = '';

        foreach (array_unique($this->js) as &$v) {
            if (!$paths = $this->get($v, 'js')) {
                continue;
            }

            foreach ((array)$paths as $path) {
                $html .= "<script src=\"{$this->withVersionQuery($path)}\"></script>";
            }
        }

        return $html;
    }


    /**
     * @return string
     */
    public function styleToHtml(): string
    {
        $style = implode('', array_unique($this->style));

        return "<style>$style</style>";
    }

    public function scriptToHtml()
    {
        $script = implode(';', array_unique($this->script));
        $directScript = implode(';', array_unique($this->directScript));

        return <<<HTML
<script>
$(function () { 
    try {
        {$script}
    } catch (e) {
        console.error(e)
    }
});
(function () {
    try {
        {$directScript}
    } catch (e) {
        console.error(e)
    }
})()
</script>
HTML;
    }

    /**
     * Create a Asset instance.
     *
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params): self
    {
        return new static(...$params);
    }
}
