<?php
namespace App\Filament\Resources\PreventionCouncilResource\Pages;
use App\Filament\Resources\PreventionCouncilResource;
use App\Services\PeriodGuard;
use Filament\Resources\Pages\EditRecord;
class EditPreventionCouncil extends EditRecord { protected static string $resource = PreventionCouncilResource::class; protected function mutateFormDataBeforeSave(array $data): array { PeriodGuard::writable($this->record->academic_period_id); return $data; } protected function getActions(): array { return []; } }
