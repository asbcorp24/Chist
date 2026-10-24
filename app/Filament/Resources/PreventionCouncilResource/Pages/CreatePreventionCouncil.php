<?php
namespace App\Filament\Resources\PreventionCouncilResource\Pages;
use App\Filament\Resources\PreventionCouncilResource;
use App\Services\PeriodGuard;
use Filament\Resources\Pages\CreateRecord;
class CreatePreventionCouncil extends CreateRecord { protected static string $resource = PreventionCouncilResource::class; protected function mutateFormDataBeforeCreate(array $data): array { $p=request()->attributes->get('academic_period'); if($p){PeriodGuard::writable($p->id);$data['academic_period_id']=$p->id;} $data['created_by']=auth()->id(); return $data; } }
