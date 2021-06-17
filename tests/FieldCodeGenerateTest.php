<?php

declare(strict_types=1);

namespace Tests;

use Kaxiluo\TextHelper\Exception\FieldCodeGenerateException;
use Kaxiluo\TextHelper\FieldCodeGenerate;

class FieldCodeGenerateTest extends TestCase
{
    public function testMake()
    {
        $svc = new FieldCodeGenerate();

        // 单列
        $input = <<<TXT
1
2
3
TXT;
        $res = $svc->make($input, '"1",');
        $out = <<<TXT
"1",
"2",
"3",
TXT;
        $this->assertEquals($out, $res);

        // 多列
        $input = <<<TXT
1  a
2  b
3   c
TXT;
        $res = $svc->make($input, '"a" => "1",');
        $out = <<<TXT
"a" => "1",
"b" => "2",
"c" => "3",
TXT;
        $this->assertEquals($out, $res);

        // 多列 - 单行多字符
        $input = <<<TXT
1  A
2  B
3  C
TXT;
        $res = $svc->make($input, 'const A = 1;');
        $out = <<<TXT
const A = 1;
const B = 2;
const C = 3;
TXT;
        $this->assertEquals($out, $res);
    }

    public function testUnalignedTxt()
    {
        $svc = new FieldCodeGenerate();

        // 多列 未对齐
        $input = <<<TXT
1  a
2  b b
3   c
TXT;
        $res = $svc->make($input, '"a" => "1",');
        $out = <<<TXT
"a" => "1",
2  b b
"c" => "3",
TXT;
        $this->assertEquals($out, $res);
    }

    public function testBadText()
    {
        $svc = new FieldCodeGenerate();

        // 多列 未对齐
        $input = <<<TXT
1  a  y
2  b
3  c
TXT;
        $this->expectException(FieldCodeGenerateException::class);
        $svc->make($input, '"a" => "1",');
    }
}
