<?php

namespace App\Http\Controllers;

use App\Core\Grid;
use App\Core\Layout\Column;
use App\Core\Layout\Content;
use App\Core\Layout\Row;
use App\User;

class ExampleController extends Controller
{
    /**
     * @see content布局gird测试的例子
     * @param Content $content
     * @return Content
     */
    public function gridExample(Content $content): Content
    {
        return $content->title('表格')->description("表格内容")->row("非常不错")->row(function (Row $row){
            $row->column(3, "测试1");
            $row->column(3, "测试2");
            $row->column(3, "测试3");
            $row->column(3, function (Column $column){
                $column->row('测试4-1');
                $column->row('测试4-2');
                $column->row(function (Row $row) {
                    $row->column(3, "测试4-3-1");
                    $row->column(3, "测试4-3-2");
                });
            });

        })->body('不错');
    }

    protected function grid()
    {
        return Grid::make();
    }
}
