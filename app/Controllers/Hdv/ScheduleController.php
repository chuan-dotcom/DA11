<?php

namespace App\Controllers\Hdv;

use App\Controller;
use App\Models\Staff;
use App\Models\StaffAssignment;
use App\Support\Auth;
use App\Support\ResolvesActiveHdv;

class ScheduleController extends Controller
{
    use ResolvesActiveHdv;

    private $modelAssignment;

    public function __construct()
    {
        $this->modelAssignment = new StaffAssignment();
    }

    public function index()
    {
        $hdvId = $this->resolveActiveHdvId();
        $activeHdv = $this->resolveActiveHdv();
        $allHdv = $this->resolveAllViewableHdv();

        $schedules = $this->modelAssignment->getByStaffIdWithDeparture($hdvId, true);
        usort($schedules, static function (array $a, array $b): int {
            $da = strtotime((string)($a['departure_date'] ?? '')) ?: 0;
            $db = strtotime((string)($b['departure_date'] ?? '')) ?: 0;
            return $da <=> $db;
        });
        foreach ($schedules as &$row) {
            $db = (new \App\Model())->getConnection();
            $totalGuests = (int)$db->fetchOne("
                SELECT COUNT(g.id) FROM booking_guests g
                INNER JOIN bookings b ON b.id = g.booking_id
                WHERE b.departure_id = ?
            ", [(int)$row['departure_id']]);
            $row['total_guests'] = $totalGuests;
        }
        unset($row);

        $title = 'Lịch trình tour';

        return view('hdv.schedule.index', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'schedules'
        ));
    }
}
