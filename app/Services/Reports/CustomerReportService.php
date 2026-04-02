<?php

namespace App\Services\Reports;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomerReportService
{
    public function count(int $days): int
    {
        // summary (1) + top buyers (5) + inactive (5)
        return 11;
    }

    public function generate(int $days, callable $progress): array
    {
        $summary = [
            'Total Customers' => User::count(),
            'New (last 30 days)' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'Inactive (>=30 days no orders)' => $this->inactiveCount(30),
        ];

        $progress();

        $topBuyers = $this->topBuyers($progress);
        $inactive = $this->inactiveCustomers(30, $progress);

        return [
            'meta' => [
                'title' => 'Customer Report',
                'generated_at' => now()->toDateTimeString(),
                'filters' => 'Inactive >= 30 days',
            ],
            'summary_headers' => array_keys($summary),
            'summary_rows' => [array_values($summary)],
            'details_headers' => ['Section', 'Data'],
            'details_rows' => [
                ['Top Buyers', json_encode($topBuyers)],
                ['Inactive Customers', json_encode($inactive)],
            ],
        ];
    }

    private function topBuyers(callable $progress): array
    {
        $rows = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->groupBy('orders.user_id', 'users.name')
            ->select('users.name', DB::raw('SUM(orders.total_amount) as total'))
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $data = [];
        foreach ($rows as $row) {
            $progress();
            $data[] = [
                'name' => $row->name,
                'total' => (float) $row->total,
            ];
        }

        return $data;
    }

    private function inactiveCount(int $days): int
    {
        $since = now()->subDays($days);
        return User::whereDoesntHave('orders', function ($q) use ($since) {
            $q->where('created_at', '>=', $since);
        })->count();
    }

    private function inactiveCustomers(int $days, callable $progress): array
    {
        $since = now()->subDays($days);

        $users = User::whereDoesntHave('orders', function ($q) use ($since) {
            $q->where('created_at', '>=', $since);
        })
            ->select('name', 'email')
            ->limit(5)
            ->get();

        $data = [];
        foreach ($users as $u) {
            $progress();
            $data[] = [
                'name' => $u->name,
                'email' => $u->email,
            ];
        }

        return $data;
    }
}
