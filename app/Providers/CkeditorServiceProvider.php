<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Kahusoftware\FilamentCkeditorField\CKEditor;

class CkeditorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure CKEditor default options
        CKEditor::configureUsing(function (CKEditor $field): CKEditor {
            return $field->options([
                'simpleUpload' => [
                    'uploadUrl' => route('ckeditor.upload'),
                ],
                'image' => [
                    'toolbar' => [
                        'imageStyle:inline',
                        'imageStyle:block',
                        'imageStyle:side',
                        '|',
                        'toggleImageCaption',
                        'imageTextAlternative',
                    ],
                    'resizeOptions' => [
                        [
                            'name' => 'resizeImage:original',
                            'value' => null,
                            'label' => 'Original',
                        ],
                        [
                            'name' => 'resizeImage:50',
                            'value' => '50',
                            'label' => '50%',
                        ],
                        [
                            'name' => 'resizeImage:75',
                            'value' => '75',
                            'label' => '75%',
                        ],
                    ],
                ],
                'heading' => [
                    'options' => [
                        ['model' => 'paragraph', 'title' => 'Paragraph', 'class' => 'ck-heading_paragraph'],
                        ['model' => 'heading1', 'view' => 'h1', 'title' => 'Heading 1', 'class' => 'ck-heading_heading1'],
                        ['model' => 'heading2', 'view' => 'h2', 'title' => 'Heading 2', 'class' => 'ck-heading_heading2'],
                        ['model' => 'heading3', 'view' => 'h3', 'title' => 'Heading 3', 'class' => 'ck-heading_heading3'],
                    ],
                ],
                'toolbar' => [
                    'undo', 'redo', '|',
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'link', '|',
                    'alignment', '|',
                    'bulletedList', 'numberedList', '|',
                    'insertTable', '|',
                    'uploadImage', '|',
                    'blockQuote', '|',
                    'sourceEditing',
                ],
            ]);
        });

        // Load any custom plugins or scripts needed
        FilamentAsset::register([
            // Add any custom assets if needed
        ]);
    }
}