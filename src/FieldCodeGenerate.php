<?php

declare(strict_types=1);

namespace Kaxiluo\TextHelper;

use Kaxiluo\TextHelper\Exception\FieldCodeGenerateException;

class FieldCodeGenerate
{
    public static function make(string $input, string $firstRowReference): string
    {
        $rows = explode("\n", $input);
        if (count($rows) <= 1) {
            return $firstRowReference;
        }

        $firstRowWords = preg_split('/\s/', $rows[0], -1, PREG_SPLIT_NO_EMPTY);
        if (empty($firstRowWords)) {
            throw new FieldCodeGenerateException('first row words is empty');
        }

        // row mode
        $rowMode = $firstRowReference;
        foreach ($firstRowWords as $key => $value) {
            if (strpos($firstRowReference, $value) === false) {
                throw new FieldCodeGenerateException('no support');
            }
            $rowMode = str_replace($value, '{$' . $key . '}', $rowMode);
        }

        $wordsCount = count($firstRowWords);
        $search = [];
        for ($i = 0; $i < $wordsCount; $i++) {
            $search[] = '{$' . $i . '}';
        }

        $output = '';
        foreach ($rows as $row) {
            $words = preg_split('/\s/', $row, -1, PREG_SPLIT_NO_EMPTY);
            if (empty($words)) {
                continue;
            }

            if (count($words) != $wordsCount) {
                // TODO
                $output .= $row;
            } else {
                $replace = [];
                for ($i = 0; $i < $wordsCount; $i++) {
                    $replace[] = $words[$i];
                }
                $output .= str_replace($search, $replace, $rowMode);
            }
            $output .= "\n";
        }
        return rtrim($output,"\n");
    }
}
