<?php

namespace App\Exports;

use App\Models\Route;
use App\Models\Vehicle;
use App\Enums\RouteStatusEnum;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UsersExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate)->startOfMonth();
        $this->endDate = Carbon::parse($endDate)->endOfMonth();
    }

    public function hasData(): bool
    {
        return Route::where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::Completed))
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->exists();
    }

    public function sheets(): array
    {
        $sheets = [];
        $currentDate = $this->startDate->copy();

        while ($currentDate <= $this->endDate) {
            $hasData = Route::where('route_status_id', RouteStatusEnum::getId(RouteStatusEnum::Completed))
                ->whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->exists();

            if ($hasData) {
                $monthName = $currentDate->locale('pt_BR')->isoFormat('MMMM');
                $sheets[] = new UsersSheet($currentDate->copy(), $monthName);
            }

            $currentDate->addMonth();
        }

        return $sheets;
    }
}

class UsersSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $date;
    protected $sheetName;

    public function __construct($date, $sheetName)
    {
        $this->date = $date;
        $this->sheetName = $sheetName;
    }

    public function collection(): Collection
    {
        $data = Route::select(
            'users.user_name',
            'users.email',
            DB::raw('COALESCE(COUNT(routes.id), 0) as total_routes'),
            DB::raw('COALESCE(SUM(routes.distance_km), 0) as total_distance'),
            DB::raw('COALESCE(SUM(routes.carbon_footprint), 0) as total_carbon_footprint'),
            DB::raw('COALESCE(SUM(routes.carbon_footprint), 0) as total_carbon_saved'),
            DB::raw('COALESCE(SUM(routes.points), 0) as total_points'),
            DB::raw('COALESCE(ROUND(AVG(routes.carbon_footprint), 3), 0) as average_carbon_footprint'),
            DB::raw('COALESCE(ROUND(AVG(routes.distance_km), 3), 0) as average_distance'),
            DB::raw('COALESCE(SUM(CASE WHEN routes.vehicle_id IN (' . Vehicle::VEHICLES['gasoline_car'] . ', ' . Vehicle::VEHICLES['diesel_car'] . ') THEN 1 ELSE 0 END), 0) as total_car_routes'),
            DB::raw('COALESCE(SUM(CASE WHEN routes.vehicle_id = ' . Vehicle::VEHICLES['walking'] . ' THEN 1 ELSE 0 END), 0) as total_walking_routes'),
            DB::raw('COALESCE(SUM(CASE WHEN routes.vehicle_id = ' . Vehicle::VEHICLES['bicycle'] . ' THEN 1 ELSE 0 END), 0) as total_bicycle_routes'),
            DB::raw('COALESCE(SUM(CASE WHEN routes.vehicle_id = ' . Vehicle::VEHICLES['bus'] . ' THEN 1 ELSE 0 END), 0) as total_bus_routes')
        )
        ->join('users', 'routes.user_id', '=', 'users.id')
        ->whereMonth('routes.created_at', $this->date->month)
        ->whereYear('routes.created_at', $this->date->year)
        ->where('routes.route_status_id', RouteStatusEnum::getId(RouteStatusEnum::Completed))
        ->groupBy('users.user_name', 'users.email')
        ->get();

        $fields = [
            'Nome do Usuário' => 'user_name',
            'Email' => 'email',
            'Total de Rotas' => 'total_routes',
            'Distância Total (KM)' => 'total_distance',
            'Total de Carbono Gerado' => 'total_carbon_footprint',
            'Total de Carbono Economizado' => 'total_carbon_saved',
            'Total de Pontos' => 'total_points',
            'Média de Carbono por Rota' => 'average_carbon_footprint',
            'Média de Distância por Rota' => 'average_distance',
            'Total de Rotas de Carro' => 'total_car_routes',
            'Total de Rotas a Pé' => 'total_walking_routes',
            'Total de Rotas de Bicicleta' => 'total_bicycle_routes',
            'Total de Rotas de Ônibus' => 'total_bus_routes',
        ];

        $numericFields = [
            'total_routes',
            'total_distance',
            'total_carbon_footprint',
            'total_carbon_saved',
            'total_points',
            'average_carbon_footprint',
            'average_distance',
            'total_car_routes',
            'total_walking_routes',
            'total_bicycle_routes',
            'total_bus_routes',
        ];

        $final = [];
        foreach ($fields as $label => $field) {
            $row = [$label];
            foreach ($data as $user) {
                $value = isset($user->$field) ? $user->$field : '';
                if (in_array($field, $numericFields)) {
                    $row[] = ($value === null || $value === '' || $value == 0) ? '0' : $value;
                } else {
                    $row[] = $value;
                }
            }
            $final[] = $row;
        }
        return collect($final);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return $this->sheetName;
    }
} 