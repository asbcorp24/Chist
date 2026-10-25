<?php
namespace App\Filament\Resources\StudentMovementResource\Pages;
use App\Filament\Resources\StudentMovementResource;
use App\Services\PeriodGuard;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Illuminate\Support\Facades\DB;
class CreateStudentMovement extends CreateRecord
{
 protected static string $resource=StudentMovementResource::class;
 protected function getFormSchema():array{return[
  Forms\Components\Select::make('student_id')->label('Студент')->options(fn()=>DB::table('students')->where('status','active')->orderBy('last_name')->get()->mapWithKeys(fn($s)=>[$s->id=>trim($s->last_name.' '.$s->first_name.' '.$s->middle_name)]))->searchable()->required(),
  Forms\Components\Select::make('type')->label('Операция')->options(['enrollment'=>'Зачисление','transfer'=>'Перевод','academic_leave'=>'Академический отпуск','return_from_leave'=>'Возврат из отпуска','expulsion'=>'Отчисление','graduation'=>'Выпуск','reinstatement'=>'Восстановление'])->required(),
  Forms\Components\Select::make('to_group_id')->label('Новая группа')->options(fn()=>DB::table('study_groups')->where('active',1)->orderBy('name')->pluck('name','id'))->searchable(),
  Forms\Components\DatePicker::make('effective_on')->label('Дата действия')->required(),
  Forms\Components\Select::make('academic_order_id')->label('Приказ')->options(function(){$p=request()->attributes->get('academic_period');return DB::table('academic_orders')->when($p,fn($q)=>$q->where('academic_period_id',$p->id))->orderByDesc('order_date')->limit(300)->get()->mapWithKeys(fn($o)=>[$o->id=>'№'.$o->number.' · '.$o->order_date.' · '.$o->title]);})->searchable(),
  Forms\Components\TextInput::make('order_number')->label('Номер приказа'),
  Forms\Components\DatePicker::make('order_date')->label('Дата приказа'),
  Forms\Components\Textarea::make('basis')->label('Основание')->columnSpan('full')
 ];}
 protected function handleRecordCreation(array $data):\Illuminate\Database\Eloquent\Model{$p=request()->attributes->get('academic_period');abort_unless($p,422);PeriodGuard::writable($p->id);$s=DB::table('students')->find($data['student_id']);abort_if(!$s,404);if(in_array($data['type'],['enrollment','transfer','return_from_leave','reinstatement']))abort_unless($data['to_group_id']??null,422,'Нужно выбрать группу.');if(!empty($data['academic_order_id'])){$o=DB::table('academic_orders')->where('academic_period_id',$p->id)->find($data['academic_order_id']);abort_if(!$o,422);$data['order_number']=$o->number;$data['order_date']=$o->order_date;$data['basis']=$data['basis']??$o->basis;}else abort_if(empty($data['order_number'])||empty($data['order_date']),422,'Выберите приказ или укажите номер и дату.');
 return DB::transaction(function()use($data,$p,$s){$data['academic_period_id']=$p->id;$data['from_group_id']=$s->current_group_id;$data['created_by']=auth()->id();$m=\App\Models\StudentMovement::create($data);$status=match($data['type']){'academic_leave'=>'academic_leave','expulsion'=>'expelled','graduation'=>'graduated',default=>'active'};$group=in_array($data['type'],['enrollment','transfer','return_from_leave','reinstatement'])?$data['to_group_id']:$s->current_group_id;if($data['type']==='transfer'&&$s->current_group_id!=$group)DB::table('student_group_memberships')->where('student_id',$s->id)->where('status','active')->update(['status'=>'inactive','ends_on'=>$data['effective_on'],'updated_at'=>now()]);DB::table('students')->where('id',$s->id)->update(['current_group_id'=>$group,'status'=>$status,'left_on'=>in_array($status,['expelled','graduated'])?$data['effective_on']:null,'updated_at'=>now()]);if($group&&$status==='active')DB::table('student_group_memberships')->updateOrInsert(['student_id'=>$s->id,'academic_period_id'=>$p->id],['study_group_id'=>$group,'starts_on'=>$data['effective_on'],'ends_on'=>null,'status'=>'active','created_at'=>now(),'updated_at'=>now()]);return $m;});}
}