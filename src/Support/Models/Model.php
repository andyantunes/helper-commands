<?php

namespace AndyAntunes\HelperCommands\Support\Models;

class Model
{
    public static function all(): array
    {
        $index = 0;
        $modelList = [];
        $path = app_path() . "/Models";
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $result;

            if (is_dir($filename)) {
                $modelList = array_merge($modelList, getModels($filename));
            } else {
                $modelList[$index] = substr($filename, 0, -4);
            }

            $index++;
        }

        return $modelList;
    }
}
