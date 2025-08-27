<?php

namespace App\Library\PageBuilder\Blocks;

use App\Library\PageBuilder\BlockBase;

class RootBlock extends BlockBase
{
    public string $id = 'root';
    public string $name = 'Root';
    public string $category = 'Container';

    public function getOptions(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category,
            'is_container' => true,
            'component' => 'RootBlock',
            'settings' => [],
            'model' => new \stdClass(),
        ];
    }

    public function content(array $model = []): string
    {
        // Client-side rendering only
        return '';
    }
}
