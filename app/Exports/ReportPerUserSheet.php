<?php

namespace App\Exports;

use App\DailyActivity;
use Carbon\Carbon;
use App\Helpers;
use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportPerUserSheet implements FromQuery, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $user_id;
    protected $users_array;

    public function __construct(int $user_id)
    {
      $this->user_id = $user_id;
      $this->users_array = Helpers::getUserArray(User::all());
    }

    public function query()
    {
      return DailyActivity::query()
                          ->where('user_id', $this->user_id)
                          ->where('for_date', Carbon::now()->format('Y-m-d'))
                          ->select(['time_slot', 'activity']);

    }

    public function title(): string
    {
      return $this->users_array[$this->user_id];
    }

    public function headings(): array
    {
      return [
        [$this->users_array[$this->user_id]],
        ['Time',
        'Activity']
      ];
    }

    public function registerEvents(): array
    {
      return [
        AfterSheet::class    => function(AfterSheet $event) {
          $event->sheet->getStyle('A1')->applyFromArray([
            'font' => [
              'bold' => true
            ],
            'fill'  => [
              'fillType'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
              'startColor'  => [
                'argb'  => 'FFFF00'
              ]
            ]
          ]);

          $event->sheet->getStyle('A2:B2')->applyFromArray([
            'font' => [
              'bold' => true
            ]
          ]);
        },
      ];
    }
}
