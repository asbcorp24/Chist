<?php
namespace App\Filament\Resources\PreventionCouncilResource\Pages;
use App\Filament\Resources\PreventionCouncilResource;
use Filament\Resources\Pages\ListRecords;
class ListPreventionCouncils extends ListRecords { protected static string $resource = PreventionCouncilResource::class; protected function getActions(): array { return [\Filament\Pages\Actions\CreateAction::make()->label('Новое заседание')]; } }
