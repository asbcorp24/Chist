<?php

namespace App\Filament\Pages;

use App\Services\PeriodGuard;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\Actions\Action;
use Illuminate\Support\Facades\DB;

class AttestationDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';
    protected static ?string $navigationLabel = 'Ведомости и пересдачи';
    protected static ?string $navigationGroup = 'Учебная часть';
    protected static string $view = 'filament.pages.attestation-dashboard';

    public array $sheets = [];
    public array $stats = [];

    public function mount(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        $p = request()->attributes->get('academic_period');
        if (! $p) return;

        $q = DB::table('attestation_sheets as a')
            ->join('study_groups as g', 'g.id', '=', 'a.study_group_id')
            ->join('subjects as s', 's.id', '=', 'a.subject_id')
            ->leftJoin('teachers as t', 't.id', '=', 'a.teacher_id')
            ->where('a.academic_period_id', $p->id)
            ->select('a.*', 'g.name as group_name', 's.name as subject_name', 't.last_name as teacher_last_name')
            ->orderByDesc('a.id');

        $this->sheets = $q->get()->map(function ($x) {
            $latest = DB::table('attestation_attempts')
                ->where('attestation_sheet_id', $x->id)
                ->orderBy('student_id')
                ->orderByDesc('attempt_no')
                ->get()
                ->unique('student_id');
            $x->debts = $latest->where('passed', 0)->whereNotNull('completed_at')->count();
            $x->unfilled = DB::table('attestation_attempts')
                ->where(['attestation_sheet_id' => $x->id, 'attempt_no' => 0])
                ->whereNull('completed_at')->count();
            return (array) $x;
        })->all();

        $this->stats = [
            'open' => collect($this->sheets)->where('status', 'open')->count(),
            'closed' => collect($this->sheets)->where('status', 'closed')->count(),
            'debts' => collect($this->sheets)->sum('debts'),
            'unfilled' => collect($this->sheets)->sum('unfilled'),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('create')
                ->label('Новая ведомость')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    Select::make('study_group_id')->label('Группа')
                        ->options(fn () => DB::table('study_groups')->where('active', 1)->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()->required(),
                    Select::make('subject_id')->label('Дисциплина')
                        ->options(fn () => DB::table('subjects')->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()->required(),
                    Select::make('teacher_id')->label('Преподаватель')
                        ->options(fn () => DB::table('teachers')->where('active', 1)->orderBy('last_name')->get()->mapWithKeys(fn ($t) => [$t->id => trim($t->last_name.' '.$t->first_name.' '.$t->middle_name)])->all())
                        ->searchable(),
                    Select::make('assessment_type')->label('Форма контроля')
                        ->options(['exam'=>'Экзамен','credit'=>'Зачёт','differentiated_credit'=>'Дифференцированный зачёт','coursework'=>'Курсовая работа','other'=>'Иное'])
                        ->default('exam')->required(),
                    DatePicker::make('assessment_date')->label('Дата'),
                    TextInput::make('number')->label('Номер ведомости')->maxLength(100),
                ])
                ->action(function (array $data): void {
                    $p = request()->attributes->get('academic_period');
                    abort_unless($p, 422, 'Не выбран учебный период.');
                    PeriodGuard::writable($p->id);
                    $id = DB::transaction(function () use ($data, $p) {
                        $id = DB::table('attestation_sheets')->insertGetId($data + [
                            'academic_period_id' => $p->id,
                            'status' => 'open',
                            'created_by' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $students = DB::table('student_group_memberships')->where([
                            'academic_period_id' => $p->id,
                            'study_group_id' => $data['study_group_id'],
                            'status' => 'active',
                        ])->pluck('student_id');
                        if (! $students->count()) {
                            $students = DB::table('students')->where([
                                'current_group_id' => $data['study_group_id'],
                                'status' => 'active',
                            ])->pluck('id');
                        }
                        foreach ($students as $sid) {
                            DB::table('attestation_attempts')->insert([
                                'attestation_sheet_id' => $id,
                                'student_id' => $sid,
                                'attempt_no' => 0,
                                'kind' => 'primary',
                                'scheduled_on' => $data['assessment_date'] ?? null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                        return $id;
                    });
                    Notification::make()->title('Ведомость создана')->success()->send();
                    redirect('/admin/attestation-workspace?sheet='.$id);
                }),
        ];
    }

    public static function canAccess(): bool
    {
        $u = auth()->user();
        return $u && ($u->isSuperAdmin() || $u->hasRole('admin') || $u->hasRole('head_academic') || $u->hasRole('academic_head') || $u->canDo('semester.mark'));
    }
}
