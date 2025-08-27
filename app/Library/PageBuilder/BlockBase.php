<?php

namespace App\Library\PageBuilder;

abstract class BlockBase
{
    public string $id;
    public string $name;
    public string $category = 'Other Block';

    public function __construct()
    {
        if (!isset($this->id)) {
            $this->id = static::class;
        }
        if (!isset($this->name)) {
            $this->name = class_basename(static::class);
        }
    }

    // Metadata for editor: settings schema + defaults
    abstract public function getOptions(): array;

    // Server-side render output for page view
    abstract public function content(array $model = []): string;

    // Optional: preview HTML for the editor
    public function preview(array $model = []): string
    {
        return $this->content($model);
    }
}
