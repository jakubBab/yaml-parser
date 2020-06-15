<?php


function textToArrayConverter($fileName): ?array
{
    if (!is_file($fileName)) {
        return null;
    }

    function convertStringToMultiDimensionArray($path, $arrayValue, $separator)
    {
        $pos = strpos($path, $separator);

        if (false === $pos) {
            return [$path => $arrayValue];
        }

        $parent = substr($path, 0, $pos);
        $path = substr($path, $pos + 1);

        $result = convertStringToMultiDimensionArray($path, $arrayValue, $separator);

        return [
            $parent => $result
        ];

    }

    $file = file($fileName);
    $fileToArray = [];
    foreach ($file as $fileContent) {
        $trimmedContent = preg_replace('/\s+/', '', $fileContent);
        $isCommentBlock = '#' === substr($trimmedContent, 0, 1);

        if ($isCommentBlock) {
            continue;
        }

        $matchResults = [];
        preg_match('/(.+?(?==))=(.*)/', $trimmedContent, $matchResults);

        if (empty($matchResults) ||  count($matchResults) < 2) {
            continue;
        }

        $arrayKeys = $matchResults[1];
        $lastItemValue = str_replace('"', "", $matchResults[2]);

        $fileToArray = array_merge_recursive($fileToArray, convertStringToMultiDimensionArray($arrayKeys, $lastItemValue, '.'));

    }

    return $fileToArray;
}


var_dump(textToArrayConverter("config.txt"));