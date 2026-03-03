<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Forms\Components\UnlayerEditor;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    public static function getNavigationGroup(): ?string
    {
        return \NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return \NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin::get()->getNavigationIcon() ?? 'heroicon-o-envelope-open';
    }

    public static function getNavigationSort(): ?int
    {
        return \NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin::get()->getNavigationSort();
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('Email Template');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Email Templates');
    }

    public static function getNavigationBadge(): ?string
    {
        if (! \NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin::get()->isNavigationBadgeEnabled()) {
            return null;
        }

        return static::getModel()::count();
    }
    /**
     * Resolve available variables (merge tags).
     */
    public static function getMergeTags(): array
    {
        return [
            ['name' => 'App Name', 'value' => 'app_name', 'sample' => config('app.name')],
            ['name' => 'User Name', 'value' => 'user_name', 'sample' => 'John Doe'],
            ['name' => 'User Email', 'value' => 'user_email', 'sample' => 'john@example.com'],
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('General Information')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->placeholder('Write here...')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Internal name for this template'),

                                TextInput::make('key')
                                    ->label('Template Key')
                                    ->placeholder('Write here...')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Unique programmatic reference (e.g. order.success)'),

                                TextInput::make('subject')
                                    ->label('Subject')
                                    ->placeholder('Write here...')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Blade rendering enabled'),
                            ]),

                        \Filament\Schemas\Components\Section::make()->schema([
                            UnlayerEditor::make('unlayer_state')
                                ->label('Template Design')
                                ->mergeTags(static::getMergeTags())
                                ->columnSpanFull()
                                ->live(),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('SL. No.')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('subject')
                    ->limit(40),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y h:i A'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit'   => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
