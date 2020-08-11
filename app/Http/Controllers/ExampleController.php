<?php

namespace App\Http\Controllers;

use App\Core\Layout\Content;

class ExampleController extends Controller
{
    /**
     * @see content布局gird测试的例子
     * @param Content $content
     * @return Content
     */
    public function gridExample(Content $content): Content
    {
        return $content->title('表格')->description("表格内容")->body("不错");
    }
}
