<?php

namespace Lisak\WideEditor\Providers;

use App\Filament\Server\Resources\Files\Pages\EditFiles;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WideEditorPluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn () => Blade::render(<<<'HTML'
            <style>
                html.wide-editor-on main.fi-main {
                    max-width: none !important;
                }

                html.wide-editor-on .fme-wrapper,
                html.wide-editor-on .fme-container,
                html.wide-editor-on .fme-element-wrapper,
                html.wide-editor-on .fme-element {
                    height: calc(100dvh - 270px) !important;
                    max-height: none !important;
                }

                html.wide-editor-on .fi-form-actions {
                    position: sticky;
                    bottom: 0;
                    z-index: 20;
                    padding-block: .5rem;
                }

                #wide-editor-toggle {
                    position: fixed;
                    bottom: 1rem;
                    right: 1rem;
                    z-index: 50;
                    width: 2.5rem;
                    height: 2.5rem;
                    border-radius: 9999px;
                    border: none;
                    background: #2563eb;
                    color: #fff;
                    font-size: 1.1rem;
                    line-height: 1;
                    cursor: pointer;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, .3);
                }
            </style>

            <script>
            (function () {
                function applyState() {
                    document.documentElement.classList.toggle('wide-editor-on', localStorage.getItem('wide-editor') === '1');
                }

                function relayout() {
                    window.dispatchEvent(new Event('resize'));
                    window.monaco?.editor?.getEditors?.().forEach((editor) => editor.layout());
                }

                function ensureButton() {
                    if (document.getElementById('wide-editor-toggle')) {
                        return;
                    }

                    var btn = document.createElement('button');
                    btn.id = 'wide-editor-toggle';
                    btn.type = 'button';
                    btn.title = 'Toggle wide editor';
                    btn.textContent = '⛶';
                    btn.addEventListener('click', function () {
                        var on = document.documentElement.classList.toggle('wide-editor-on');
                        localStorage.setItem('wide-editor', on ? '1' : '0');
                        relayout();
                    });
                    document.body.appendChild(btn);
                }

                applyState();
                ensureButton();
                relayout();

                document.addEventListener('livewire:navigated', function () {
                    applyState();
                    ensureButton();
                    relayout();
                });
            })();
            </script>
            HTML),
            scopes: EditFiles::class,
        );
    }
}
