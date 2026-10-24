<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PreventionCouncilResource\Pages;use App\Filament\Resources\PreventionCouncilResource\RelationManagers\CasesRelationManager;
use App\Models\PreventionCouncil;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PreventionCouncilResource extends Resource
{
    protected static ?string $model = PreventionCouncil::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationLabel = 'Совет профилактики';
    protected static ?string $modelLabel = 'заседание';
    protected static ?string $pluralModelLabel = 'Совет профилактики';
    protected static ?string $navigationGroup = 'Воспитательная работа';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('protocol_number')->label('Протокол №')->required()->maxLength(100),
            Forms\Components\DatePicker::make('meeting_on')->label('Дата заседания')->required(),
            Forms\Components\Textarea::make('agenda')->label('Повестка')->rows(5)->columnSpan('full'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('protocol_number')->label('Протокол')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('meeting_on')->label('Дата')->date('d.m.Y')->sortable(),
                Tables\Columns\TextColumn::make('agenda')->label('Повестка')->limit(80)->wrap()->searchable(),
            ])
            ->defaultSort('meeting_on','desc')
            ->actions([
                Tables\Actions\EditAction::make()->label('Изменить'),
                Tables\Actions\Action::make('archive')->label('В архив')->color('danger')->requiresConfirmation()
                    ->action(fn (PreventionCouncil $record) => $record->update(['archived_at'=>now()])),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNull('archived_at');
    }

    public static function getRelations(): array
    {
        return [CasesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPreventionCouncils::route('/'),
            'create' => Pages\CreatePreventionCouncil::route('/create'),
            'edit' => Pages\EditPreventionCouncil::route('/{record}/edit'),
        ];
    }
}
