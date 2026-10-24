<?php
namespace App\Filament\Resources;
use App\Filament\Resources\StudentMeritActionResource\Pages;
use App\Models\StudentMeritAction;use Illuminate\Support\Facades\DB;
use Filament\Forms;use Filament\Resources\Form;use Filament\Resources\Resource;use Filament\Resources\Table;use Filament\Tables;
class StudentMeritActionResource extends Resource{
protected static ?string $model=StudentMeritAction::class;protected static ?string $navigationIcon='heroicon-o-star';protected static ?string $navigationLabel='Поощрения и взыскания';protected static ?string $navigationGroup='Воспитательная работа';protected static ?string $modelLabel='запись';protected static ?string $pluralModelLabel='Поощрения и взыскания';
public static function form(Form $form):Form{return $form->schema([
Forms\Components\Select::make('student_id')->label('Студент')->options(fn()=>DB::table('students')->where('status','active')->orderBy('last_name')->get()->mapWithKeys(fn($s)=>[$s->id=>$s->last_name.' '.$s->first_name]))->searchable()->required(),
Forms\Components\Select::make('type')->label('Тип')->options(['reward'=>'Поощрение','discipline'=>'Взыскание'])->required(),
Forms\Components\DatePicker::make('action_on')->label('Дата')->required(),Forms\Components\TextInput::make('kind')->label('Вид')->required(),
Forms\Components\Textarea::make('reason')->label('Основание')->required()->columnSpan('full'),Forms\Components\TextInput::make('order_number')->label('Приказ №'),Forms\Components\DatePicker::make('order_on')->label('Дата приказа'),Forms\Components\TextInput::make('rating_points')->label('Баллы рейтинга')->numeric()->required()
])->columns(2);}
public static function table(Table $table):Table{return $table->columns([Tables\Columns\TextColumn::make('action_on')->label('Дата')->date('d.m.Y')->sortable(),Tables\Columns\TextColumn::make('student_id')->label('Студент')->formatStateUsing(fn($state)=>($s=DB::table('students')->where('id',$state)->first()) ? trim($s->last_name.' '.$s->first_name) : '—'),Tables\Columns\BadgeColumn::make('type')->label('Тип')->enum(['reward'=>'Поощрение','discipline'=>'Взыскание']),Tables\Columns\TextColumn::make('kind')->label('Вид'),Tables\Columns\TextColumn::make('reason')->label('Основание')->limit(50),Tables\Columns\TextColumn::make('rating_points')->label('Баллы')])->defaultSort('action_on','desc')->actions([Tables\Actions\EditAction::make(),Tables\Actions\Action::make('cancel')->label('Отменить')->color('danger')->requiresConfirmation()->visible(fn($record)=>$record->status==='active')->action(fn($record)=>$record->update(['status'=>'cancelled']))]);}
public static function getPages():array{return['index'=>Pages\ListStudentMeritActions::route('/'),'create'=>Pages\CreateStudentMeritAction::route('/create'),'edit'=>Pages\EditStudentMeritAction::route('/{record}/edit')];}}
