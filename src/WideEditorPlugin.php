<?php

namespace Lisak\WideEditor;

use Filament\Contracts\Plugin;
use Filament\Panel;

class WideEditorPlugin implements Plugin
{
    public function getId(): string
    {
        return 'wide-editor';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
