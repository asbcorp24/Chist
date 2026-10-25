<?php

namespace App\Filament\Pages;

use App\Services\CurriculumScheduleBinder;
use App\Services\LessonOccurrenceGenerator;
use App\Services\RectorXmlImporter;
use App\Services\ScheduleReplacementApplier;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ScheduleImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-upload';
    protected static ?string $navigationLabel = 'Импорт расписания';
    protected static ?string $navigationGroup = 'Учебная часть';
    protected static string $view = 'filament.pages.schedule-import';

    public ?string $lesson_unit_mode = null;
    public $xml = null;
    public array $runs = [];

    public function mount(): void
    {
        $this->form->fill();
        $this->loadRuns();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('lesson_unit_mode')
                ->label('Система занятий')
                ->options([
                    'pair' => 'Парная — 1+2 = 1 пара, 3+4 = 2 пара',
                    'lesson' => 'Поурочная — каждый урок отдельно',
                ])
                ->placeholder('Использовать настройку учебного периода')
                ->helperText('При еженедельной загрузке оставляйте настройку периода. Режим задавайте явно при первом импорте или при его изменении.'),
            FileUpload::make('xml')
                ->label('Файл XML Rector-College')
                ->acceptedFileTypes(['application/xml', 'text/xml', 'application/octet-stream'])
                ->maxSize(30720)
                ->disk('local')
                ->directory('schedule-imports')
                ->preserveFilenames()
                ->required(),
        ];
    }

    public function import(): void
    {
        $data = $this->form->getState();
        $stored = $data['xml'] ?? null;
        if (! $stored) {
            Notification::make()->title('Выберите XML-файл')->danger()->send();
            return;
        }

        $path = Storage::disk('local')->path($stored);
        $filename = basename($stored);

        try {
            $result = app(RectorXmlImporter::class)->import(
                $path,
                $filename,
                auth()->id(),
                $data['lesson_unit_mode'] ?: null
            );
            $lessons = app(LessonOccurrenceGenerator::class)->generate($result['period_id']);
            $rep = app(ScheduleReplacementApplier::class)->apply($result['period_id']);
            $bind = app(CurriculumScheduleBinder::class)->bindPeriod($result['period_id'], true);

            DB::table('rector_exchange_runs')->insert([
                'academic_period_id' => $result['period_id'],
                'user_id' => auth()->id(),
                'direction' => 'import',
                'kind' => 'schedule',
                'filename' => $filename,
                'sha256' => hash_file('sha256', $path),
                'status' => 'done',
                'stats' => json_encode([
                    'schedule_entries' => $result['schedule_entries'],
                    'lessons' => $lessons,
                    'replacements' => $rep['applied'],
                    'curriculum_bound' => $bind['bound'],
                ], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            session()->put('academic_period_id', $result['period_id']);
            $this->xml = null;
            $this->form->fill(['lesson_unit_mode' => $data['lesson_unit_mode'] ?: null, 'xml' => null]);
            $this->loadRuns();

            Notification::make()
                ->title('Расписание импортировано')
                ->body("{$result['year']}, ".($result['period'] === 1 ? 'I' : 'II')." полугодие. Строк: {$result['schedule_entries']}; занятий: {$lessons}; замен: {$rep['applied']}; КТП: {$bind['bound']}.")
                ->success()
                ->duration(10000)
                ->send();
        } catch (Throwable $e) {
            report($e);
            Notification::make()
                ->title('Ошибка импорта')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        } finally {
            if (is_file($path)) @unlink($path);
        }
    }

    protected function loadRuns(): void
    {
        $this->runs = DB::table('import_runs as i')
            ->leftJoin('academic_periods as p', 'p.id', '=', 'i.academic_period_id')
            ->leftJoin('academic_years as y', 'y.id', '=', 'p.academic_year_id')
            ->select('i.*', 'p.name as period_name', 'y.name as year_name')
            ->orderByDesc('i.id')
            ->limit(30)
            ->get()
            ->map(fn ($x) => (array) $x)
            ->all();
    }

    public static function canAccess(): bool
    {
        $u = auth()->user();
        return $u && ($u->isSuperAdmin() || $u->hasRole('admin') || $u->canDo('schedule.import'));
    }
}
