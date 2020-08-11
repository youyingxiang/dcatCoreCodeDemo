<?php

namespace App\Core\Layout;

use App\Core\Support\Helper;
use App\Core\Traits\HasBuilderEvents;
use Illuminate\Contracts\Support\Renderable;

class Content implements Renderable
{
    use HasBuilderEvents;
    /**
     * @var string
     */
    protected $view = 'core.layouts.content';
    /**
     * Content title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Content description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a content instance.
     *
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params): self
    {
        return new static(...$params);
    }

    /**
     * @param string $header
     *
     * @return $this
     */
    public function header($header = ''): self
    {
        return $this->title($header);
    }

    /**
     * Set title of content.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Alias of method row.
     *
     * @param mixed $content
     *
     * @return Content
     */
    public function body($content): self
    {
        return $this;
//        return $this->row($content);
    }


    /**
     * Set content view.
     *
     * @param null|string $view
     *
     * @return $this
     */
    public function view(?string $view): self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Set description of content.
     *
     * @param string $description
     *
     * @return $this
     */
    public function description($description = ''): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     * @throws \Throwable
     * @see 返回渲染好的界面
     */
    public function render(): string
    {
        $this->callComposing();
        $variables = $this->variables();
        return view($this->view, $variables)->render();
    }

    /**
     * @return array
     */
    protected function variables(): array
    {
        return [
            'header'          => $this->title,
            'description'     => $this->description,
//            'content'         => $this->build(),
        ];
    }
}
