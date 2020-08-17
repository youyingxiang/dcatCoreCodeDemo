<?php

namespace App\Core\Layout;

use App\Core\Support\AdminSection;
use App\Core\Support\Helper;
use Lang;

class Menu
{
    /**
     * @var array
     */
    protected static $Nodes = [
        [
            'id'        => 1,
            'title'     => '首页',
            'icon'      => 'fa fa-keyboard-o',
            'uri'       => '',
            'parent_id' => 0,
        ],
        [
            'id'        => 2,
            'title'     => '用户',
            'icon'      => '',
            'uri'       => 'example/grid',
            'parent_id' => 1,
        ],
    ];

    /**
     * @var string
     */
    protected $view = 'core.partials.menu';

    /**
     * Register menu.
     */
    public function register()
    {
       $this->add(self::$Nodes,20);
    }

    /**
     * @param array $nodes
     * @param int $priority
     *
     * @return void
     */
    public function add(array $nodes = [], int $priority = 10)
    {
        Helper::adminInjectSection(AdminSection::LEFT_SIDEBAR_MENU, function () use (&$nodes) {
            return $this->toHtml($nodes);
        }, true, $priority);
    }

    /**
     * Build html.
     *
     * @param array $nodes
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function toHtml(array $nodes)
    {
        $html = '';
        foreach (Helper::buildNestedArray($nodes) as $item) {
            $html .= $this->render($item);
        }

        return $html;
    }

    /**
     * @param string $view
     *
     * @return $this
     */
    public function view(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    public function render(array $item)
    {
        return view($this->view, ['item' => &$item, 'builder' => $this])->render();
    }

    /**
     * @param array $item
     * @param null|string $path
     *
     * @return bool
     */
    public function isActive(array $item, ?string $path = null)
    {
        if (empty($path)) {
            $path = request()->path();
        }

        if (empty($item['children'])) {
            if (empty($item['uri'])) {
                return false;
            }

            return trim($this->getPath($item['uri']), '/') == $path;
        }

        foreach ($item['children'] as $v) {
            if ($path == trim($this->getPath($v['uri']), '/')) {
                return true;
            }
            if (!empty($v['children'])) {
                if ($this->isActive($v, $path)) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * @param string $uri
     *
     * @return string
     */
    public function getPath($uri)
    {
        return $uri
            ? (url()->isValidUrl($uri) ? $uri : admin_base_path($uri))
            : $uri;
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    public function getUrl($uri)
    {
        return $uri ? admin_url($uri) : $uri;
    }

    /**
     * Create a Menu instance.
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
