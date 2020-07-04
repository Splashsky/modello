<?php

/**
 * Modello
 * 
 * A simple, ultra-lightweight template engine in PHP, for
 * small projects
 * 
 * @author Skylear "Splashsky" Johnson
 */

class Modello
{
    private string $directory;

    public static function new(string $directory)
    {
        return new Modello($directory);
    }

    public function __construct(string $directory)
    {
        if (!is_dir($directory)) {
            throw new Exception('Tried to instantiate Modello without a directory!');
        }

        $this->directory = $directory;

        return $this;
    }

    private function find(string $template, string $directory = null)
    {
        $path = str_replace('.', '/', $template);
        $dir = !is_null($directory) ? $directory : $this->directory;

        if (!is_readable($dir . $path . '.html')) {
            throw new Exception('Unable to find() template with given path.');
        }

        return $this->read($dir . $path . '.html');
    }

    private function read(string $path)
    {
        if (!is_readable($path)) {
            throw new Exception('Unable to read() given path.');
        }

        return file_get_contents($path);
    }

    public function bake(string $template, array $values = [])
    {
        $template = $this->find($template);

        return preg_replace_callback(
            '/{{\s*([A-Za-z0-9_-]+)\s*}}/',
            function($match) use ($values) { return $values[$match[1]]; },
            $template
        );
    }

    public static function quick(string $template, array $values = [])
    {
        $path = str_replace('.', '/', $template);

        if (!is_readable($path . '.html')) {
            throw new Exception('Unable to quick() template with given path.');
        }

        $template = file_get_contents($path . '.html');

        $toReplace = array_keys($values);
        foreach ($toReplace as $i => $val) {
            $toReplace[$i] = '{{ '.$val.' }}';
        }
        $values = array_values($values);

        return str_replace($toReplace, $values, $template);
    }
}

?>