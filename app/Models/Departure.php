<?php
   
namespace App\Models;

use App\Model;
              
class Departure extends Model 
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureGuestGroupColumns();
    }

    private function ensureGuestGroupColumns()
    {
        try {
            $column = $this->connection->fetchAssociative(
                'SHOW COLUMNS FROM departures LIKE ?',
                ['group_name']
            );

            if (!$column) {
                $this->connection->executeStatement(
                    "ALTER TABLE departures ADD COLUMN group_name VARCHAR(255) NULL AFTER tour_id"
                );
            }
        } catch (\Throwable $e) {
        }

        try {
            $table = $this->connection->fetchAssociative("SHOW TABLES LIKE 'booking_guests'");
            if (!$table) {
                $this->connection->executeStatement("
                    CREATE TABLE IF NOT EXISTS booking_guests (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        booking_id INT NOT NULL,
                        full_name VARCHAR(255) NOT NULL,
                        gender ENUM('male','female','other') NULL DEFAULT NULL,
                        dob DATE NULL DEFAULT NULL,
                        phone VARCHAR(50) NULL DEFAULT NULL,
                        email VARCHAR(255) NULL DEFAULT NULL,
                        identity_no VARCHAR(50) NULL DEFAULT NULL,
                        address VARCHAR(255) NULL DEFAULT NULL,
                        payment_status ENUM('unpaid','deposit','paid') NOT NULL DEFAULT 'unpaid',
                        check_in_status TINYINT(1) NOT NULL DEFAULT 0,
                        checked_in_at DATETIME NULL DEFAULT NULL,
                        note TEXT NULL,
                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_booking_guests_booking_id (booking_id),
                        INDEX idx_booking_guests_check_in_status (check_in_status),
                        CONSTRAINT fk_booking_guests_booking
                            FOREIGN KEY (booking_id)
                            REFERENCES bookings(id)
                            ON UPDATE CASCADE
                            ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }
        } catch (\Throwable $e) {
        }

        $this->ensureCostColumns();
    }

    private function ensureCostColumns()
    {
        try {
            $col1 = $this->connection->fetchAssociative(
                'SHOW COLUMNS FROM departures LIKE ?',
                ['incurred_cost']
            );
            if (!$col1) {
                $this->connection->executeStatement(
                    "ALTER TABLE departures ADD COLUMN incurred_cost BIGINT NOT NULL DEFAULT 0 AFTER notes"
                );
            }
        } catch (\Throwable $e) {
        }

        try {
            $col2 = $this->connection->fetchAssociative(
                'SHOW COLUMNS FROM departures LIKE ?',
                ['incurred_cost_note']
            );
            if (!$col2) {
                $this->connection->executeStatement(
                    "ALTER TABLE departures ADD COLUMN incurred_cost_note TEXT NULL AFTER incurred_cost"
                );
            }
        } catch (\Throwable $e) {
        }
    }

    public function getEstimatedCostForDeparture($departureId)
    {
        $depId = (int) $departureId;
        if ($depId <= 0) {
            return 0;
        }

        try {
            // 1. Prioritize total price of confirmed bookings for this departure
            $sql = "SELECT SUM(total_price) as total_rev FROM bookings WHERE departure_id = ? AND status = 1";
            $row = $this->connection->fetchAssociative($sql, [$depId]);
            $confirmedRev = (float) ($row['total_rev'] ?? 0);
            if ($confirmedRev > 0) {
                return $confirmedRev;
            }

            // 2. Fallback to any bookings for this departure
            $sqlAll = "SELECT SUM(total_price) as total_rev FROM bookings WHERE departure_id = ?";
            $rowAll = $this->connection->fetchAssociative($sqlAll, [$depId]);
            $allRev = (float) ($rowAll['total_rev'] ?? 0);
            if ($allRev > 0) {
                return $allRev;
            }

            // 3. Fallback to tour price * max_participants or assigned guests
            $dep = $this->findById($depId);
            if ($dep && !empty($dep['tour_price'])) {
                $count = !empty($dep['max_participants']) && $dep['max_participants'] > 0
                    ? (int) $dep['max_participants']
                    : 10;
                return (float) ($dep['tour_price'] * $count);
            }
        } catch (\Throwable $e) {
        }

        return 0;
    }

    public function updateIncurredCost($departureId, $incurredCost, $incurredCostNote = null)
    {
        $depId = (int) $departureId;
        if ($depId <= 0) {
            return false;
        }

        $cost = max(0, (float) $incurredCost);
        $note = $incurredCostNote !== null ? trim($incurredCostNote) : null;

        try {
            return $this->connection->update('departures', [
                'incurred_cost' => $cost,
                'incurred_cost_note' => $note,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $depId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getAll($categoryId = null)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('d.*', 't.name as tour_name', 't.duration as tour_duration')
            ->from('departures', 'd')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id');

        if (!empty($categoryId)) {
            $stmt->andWhere('t.category_id = :category_id')
                ->setParameter('category_id', (int) $categoryId);
        }

        $stmt->orderBy('d.departure_date', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('d.*', 't.name as tour_name', 't.duration as tour_duration', 't.price as tour_price')
            ->from('departures', 'd')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->where('d.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function insert($data)
    {
        return $this->connection->insert('departures', [
            'tour_id'         => (int) $data['tour_id'],
            'group_name'      => !empty($data['group_name']) ? $data['group_name'] : null,
            'departure_date'  => $data['departure_date'],
            'return_date'     => !empty($data['return_date']) ? $data['return_date'] : null,
            'max_participants'=> isset($data['max_participants']) && $data['max_participants'] !== '' ? (int) $data['max_participants'] : 0,
            'meeting_point'   => $data['meeting_point'] ?? null,
            'meeting_time'    => $data['meeting_time'] ?? null,
            'vehicle'         => $data['vehicle'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'status'          => $data['status'] ?? 'scheduled',
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, $data)
    {
        $updateData = [
            'tour_id'         => (int) $data['tour_id'],
            'group_name'      => !empty($data['group_name']) ? $data['group_name'] : null,
            'departure_date'  => $data['departure_date'],
            'return_date'     => !empty($data['return_date']) ? $data['return_date'] : null,
            'max_participants'=> isset($data['max_participants']) && $data['max_participants'] !== '' ? (int) $data['max_participants'] : 0,
            'meeting_point'   => $data['meeting_point'] ?? null,
            'meeting_time'    => $data['meeting_time'] ?? null,
            'vehicle'         => $data['vehicle'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'status'          => $data['status'] ?? 'scheduled',
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        return $this->connection->update('departures', $updateData, ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->connection->delete('departures', ['id' => $id]);
    }

    /**
     * Đồng bộ Địa chỉ đón (pickup_address) của các booking thuộc đoàn
     * thành Điểm tập trung (meeting_point) của chuyến khởi hành.
     *
     * @param int  $departureId
     * @param string|null $meetingPoint  Nếu null → tự lấy từ DB
     * @param bool $forceOverride  true: luôn ghi đè (kể cả booking đã có pickup_address riêng). false: chỉ điền nếu booking chưa có.
     */
    public function syncBookingsPickupAddress($departureId, $meetingPoint = null, $forceOverride = true)
    {
        $departureId = (int) $departureId;
        if ($departureId <= 0) {
            return 0;
        }

        if ($meetingPoint === null) {
            $row = $this->connection->fetchAssociative(
                'SELECT meeting_point FROM departures WHERE id = ? LIMIT 1',
                [$departureId]
            );
            if (!$row) {
                return 0;
            }
            $meetingPoint = $row['meeting_point'];
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->update('bookings')
            ->set('pickup_address', ':pickup')
            ->where('departure_id = :depId')
            ->setParameter('pickup', $meetingPoint)
            ->setParameter('depId', $departureId);

        if (!$forceOverride) {
            $qb->andWhere('pickup_address IS NULL OR pickup_address = :empty');
            $qb->setParameter('empty', '');
        }

        try {
            return (int) $qb->executeStatement();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function getUpcomingDepartures($limit = 10)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('d.*', 't.name as tour_name')
            ->from('departures', 'd')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->where('d.departure_date >= :today')
            ->andWhere('d.status != :status_cancelled')
            ->setParameter('today', date('Y-m-d'))
            ->setParameter('status_cancelled', 'cancelled')
            ->orderBy('d.departure_date', 'ASC')
            ->setMaxResults((int) $limit);

        return $stmt->fetchAllAssociative();
    }

    public function getTotalDepartures()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')->from('departures');
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    public function getDeparturesByStatus($categoryId = null)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('d.status', 'COUNT(d.id) as count')
            ->from('departures', 'd')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id');

        if (!empty($categoryId)) {
            $stmt->andWhere('t.category_id = :category_id')
                ->setParameter('category_id', (int) $categoryId);
        }

        $stmt->groupBy('d.status');
        return $stmt->fetchAllAssociative();
    }

    public function getAllWithGuestStats($tourId = null, $status = null)
    {
        $sql = "
            SELECT
                d.*,
                t.name AS tour_name,
                t.duration AS tour_duration,
                COALESCE(gs.assigned_bookings, 0) AS assigned_bookings,
                COALESCE(gs.assigned_people, 0) AS assigned_people,
                COALESCE(gs.checked_in_people, 0) AS checked_in_people
            FROM departures d
            LEFT JOIN tours t
                ON t.id = d.tour_id
            LEFT JOIN (
                SELECT
                    b.departure_id,
                    COUNT(DISTINCT b.id) AS assigned_bookings,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COUNT(g.id)
                        ELSE COALESCE(SUM(b.num_people), 0)
                    END AS assigned_people,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COALESCE(SUM(CASE WHEN g.check_in_status = 1 THEN 1 ELSE 0 END), 0)
                        ELSE COALESCE(SUM(CASE WHEN b.check_in_status = 1 THEN b.num_people ELSE 0 END), 0)
                    END AS checked_in_people
                FROM bookings b
                LEFT JOIN booking_guests g
                    ON g.booking_id = b.id
                WHERE b.departure_id IS NOT NULL
                GROUP BY b.departure_id
            ) gs
                ON gs.departure_id = d.id
        ";

        $params = [];
        $conditions = [];

        if (!empty($tourId)) {
            $conditions[] = "d.tour_id = ?";
            $params[] = (int) $tourId;
        }

        if ($status !== null && $status !== '') {
            $conditions[] = "d.status = ?";
            $params[] = $status;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY d.departure_date DESC, d.id DESC ";

        return $this->connection->fetchAllAssociative($sql, $params);
    }

    public function findWithGuestStatsById($id)
    {
        $sql = "
            SELECT
                d.*,
                t.name AS tour_name,
                t.duration AS tour_duration,
                t.price AS tour_price,
                COALESCE(gs.assigned_bookings, 0) AS assigned_bookings,
                COALESCE(gs.assigned_people, 0) AS assigned_people,
                COALESCE(gs.checked_in_people, 0) AS checked_in_people
            FROM departures d
            LEFT JOIN tours t
                ON t.id = d.tour_id
            LEFT JOIN (
                SELECT
                    b.departure_id,
                    COUNT(DISTINCT b.id) AS assigned_bookings,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COUNT(g.id)
                        ELSE COALESCE(SUM(b.num_people), 0)
                    END AS assigned_people,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COALESCE(SUM(CASE WHEN g.check_in_status = 1 THEN 1 ELSE 0 END), 0)
                        ELSE COALESCE(SUM(CASE WHEN b.check_in_status = 1 THEN b.num_people ELSE 0 END), 0)
                    END AS checked_in_people
                FROM bookings b
                LEFT JOIN booking_guests g
                    ON g.booking_id = b.id
                WHERE b.departure_id IS NOT NULL
                GROUP BY b.departure_id
            ) gs
                ON gs.departure_id = d.id
            WHERE d.id = ?
            LIMIT 1
        ";

        return $this->connection->fetchAssociative($sql, [$id]) ?: null;
    }
}



// from pathlib import Path
import subprocess

path = Path("/mnt/data/ProjectToolkit.php")
content = path.read_text(encoding="utf-8")

extra = []
extra.append("\nnamespace App\\Support;\n")
extra.append("/**\n * Extended reporting, validation and transformation helpers.\n * These methods are designed to be split into dedicated classes as the project grows.\n */\n")
extra.append("final class ProjectToolkitExtended\n{\n")

# Validation helpers for common Laravel-style form fields.
validation_specs = [
    ("validateTourName", "string $value", "return ProjectToolkit::validateMinLength(ProjectToolkit::trimString($value), 3);"),
    ("validateTourCode", "string $value", "return preg_match('/^[A-Z0-9_-]{2,30}$/i', trim($value)) === 1;"),
    ("validateTourPrice", "mixed $value", "return ProjectToolkit::validateNumeric($value) && ProjectToolkit::toFloat($value) >= 0;"),
    ("validateTourDates", "\\DateTimeInterface $departure, \\DateTimeInterface $return", "return $return >= $departure;"),
    ("validateBookingPeople", "mixed $value", "return ProjectToolkit::validateInteger($value) && ProjectToolkit::toInt($value) > 0;"),
    ("validateBookingEmail", "string $value", "return ProjectToolkit::email($value);"),
    ("validateBookingPhone", "string $value", "return ProjectToolkit::phone($value);"),
    ("validateStaffName", "string $value", "return ProjectToolkit::validateMinLength(ProjectToolkit::trimString($value), 2);"),
    ("validateStaffRole", "string $value", "return ProjectToolkit::validateMinLength(ProjectToolkit::trimString($value), 2);"),
    ("validateAssignmentRole", "string $value", "return ProjectToolkit::validateMinLength(ProjectToolkit::trimString($value), 2);"),
]
for name, args, body in validation_specs:
    extra += [f"    public static function {name}({args}): bool", "    {", f"        {body}", "    }", ""]

# Statistics helpers.
stats = [
    "sumPrices", "sumTotals", "sumPeople", "sumDeposits", "sumRemaining",
    "averagePrice", "averageTotal", "averagePeople", "averageRating",
    "maximumPrice", "minimumPrice", "maximumPeople", "minimumPeople",
    "countActive", "countInactive", "countPending", "countCompleted",
    "countCancelled", "countConfirmed", "countDraft", "countPublished",
]
for name in stats:
    extra += [
        f"    public static function {name}(array $rows, string $field = 'value'): float",
        "    {",
        "        $values = [];",
        "        foreach ($rows as $row) {",
        "            if (!is_array($row)) {",
        "                continue;",
        "            }",
        "            $value = $row[$field] ?? 0;",
        "            if (is_numeric($value)) {",
        "                $values[] = (float) $value;",
        "            }",
        "        }",
        "",
        "        if ($values === []) {",
        "            return 0.0;",
        "        }",
        "",
        f"        return match ('{name}') {{",
        "            'sumPrices', 'sumTotals', 'sumPeople', 'sumDeposits', 'sumRemaining' => array_sum($values),",
        "            'averagePrice', 'averageTotal', 'averagePeople', 'averageRating' => array_sum($values) / count($values),",
        "            'maximumPrice', 'maximumPeople' => max($values),",
        "            'minimumPrice', 'minimumPeople' => min($values),",
        "            default => array_sum($values),",
        "        };",
        "    }",
        "",
    ]

# Generate reusable filters for a broad set of fields.
filter_fields = [
    "tour_id", "staff_id", "customer_id", "departure_id", "booking_id",
    "status", "role", "responsibility", "destination", "department",
    "name", "title", "code", "email", "phone", "price", "total_price",
    "num_people", "departure_date", "return_date", "created_at", "updated_at",
    "city", "country", "type", "category", "priority", "gender",
]
for field in filter_fields:
    method = "filterBy" + "".join(part.capitalize() for part in field.split("_"))
    extra += [
        f"    public static function {method}(array $rows, mixed $expected): array",
        "    {",
        "        $result = [];",
        "        foreach ($rows as $row) {",
        "            if (!is_array($row)) {",
        "                continue;",
        "            }",
        f"            if (($row['{field}'] ?? null) == $expected) {{",
        "                $result[] = $row;",
        "            }",
        "        }",
        "        return $result;",
        "    }",
        "",
    ]

# Search helpers for common fields.
search_fields = [
    "name", "title", "code", "email", "phone", "address", "destination",
    "description", "note", "role", "responsibility", "department",
    "customer_name", "staff_name", "tour_name", "booking_code",
]
for field in search_fields:
    method = "searchBy" + "".join(part.capitalize() for part in field.split("_"))
    extra += [
        f"    public static function {method}(array $rows, string $query): array",
        "    {",
        "        $query = ProjectToolkit::lower(ProjectToolkit::trimString($query));",
        "        if ($query === '') {",
        "            return $rows;",
        "        }",
        "",
        "        $result = [];",
        "        foreach ($rows as $row) {",
        "            if (!is_array($row)) {",
        "                continue;",
        "            }",
        f"            $value = ProjectToolkit::lower((string) ($row['{field}'] ?? ''));",
        "            if (strpos($value, $query) !== false) {",
        "                $result[] = $row;",
        "            }",
        "        }",
        "        return $result;",
        "    }",
        "",
    ]

# Sorting helpers.
sort_fields = [
    "id", "name", "title", "code", "price", "total_price", "num_people",
    "departure_date", "return_date", "created_at", "updated_at", "status",
    "role", "destination", "priority", "rating",
]
for field in sort_fields:
    method = "sortBy" + "".join(part.capitalize() for part in field.split("_"))
    extra += [
        f"    public static function {method}(array $rows, bool $descending = false): array",
        "    {",
        "        usort($rows, static function (array $a, array $b) use ($descending) {",
        f"            $left = $a['{field}'] ?? null;",
        f"            $right = $b['{field}'] ?? null;",
        "            if (is_numeric($left) && is_numeric($right)) {",
        "                $comparison = ((float) $left) <=> ((float) $right);",
        "            } else {",
        "                $comparison = strcasecmp((string) $left, (string) $right);",
        "            }",
        "            return $descending ? -$comparison : $comparison;",
        "        });",
        "        return $rows;",
        "    }",
        "",
    ]

# Build many explicit API response helpers.
response_names = [
    "success", "created", "updated", "deleted", "accepted", "empty",
    "validationError", "notFound", "conflict", "unauthorized", "forbidden",
    "serverError", "list", "detail", "dashboard", "statistics",
]
for name in response_names:
    status_map = {
        "success": 200, "created": 201, "updated": 200, "deleted": 200,
        "accepted": 202, "empty": 204, "validationError": 422,
        "notFound": 404, "conflict": 409, "unauthorized": 401,
        "forbidden": 403, "serverError": 500, "list": 200, "detail": 200,
        "dashboard": 200, "statistics": 200,
    }
    status = status_map[name]
    extra += [
        f"    public static function response{name.capitalize()}(mixed $data = null, string $message = ''): array",
        "    {",
        "        return [",
        "            'success' => " + ("false" if status >= 400 else "true") + ",",
        f"            'status' => {status},",
        "            'message' => $message,",
        "            'data' => $data,",
        "        ];",
        "    }",
        "",
    ]

# Domain-specific summaries.
domain_methods = [
    ("tourSummary", "array $tour", """return [
            'id' => $tour['id'] ?? null,
            'code' => $tour['code'] ?? null,
            'name' => $tour['name'] ?? $tour['title'] ?? null,
            'destination' => $tour['destination'] ?? null,
            'price' => ProjectToolkit::toFloat($tour['price'] ?? 0),
            'status' => $tour['status'] ?? null,
            'departure_date' => $tour['departure_date'] ?? null,
            'return_date' => $tour['return_date'] ?? null,
        ];"""),
    ("bookingSummary", "array $booking", """return [
            'id' => $booking['id'] ?? null,
            'tour_id' => $booking['tour_id'] ?? null,
            'customer_name' => $booking['customer_name'] ?? null,
            'customer_email' => $booking['customer_email'] ?? null,
            'customer_phone' => $booking['customer_phone'] ?? null,
            'num_people' => ProjectToolkit::toInt($booking['num_people'] ?? 0),
            'total_price' => ProjectToolkit::toFloat($booking['total_price'] ?? 0),
            'status' => $booking['status'] ?? null,
        ];"""),
    ("staffSummary", "array $staff", """return [
            'id' => $staff['id'] ?? null,
            'name' => $staff['name'] ?? $staff['Hoten'] ?? null,
            'email' => $staff['email'] ?? null,
            'phone' => $staff['phone'] ?? null,
            'role' => $staff['role'] ?? null,
            'status' => $staff['status'] ?? null,
        ];"""),
    ("assignmentSummary", "array $assignment", """return [
            'id' => $assignment['id'] ?? null,
            'tour_id' => $assignment['tour_id'] ?? null,
            'departure_id' => $assignment['departure_id'] ?? null,
            'staff_id' => $assignment['staff_id'] ?? null,
            'role' => $assignment['role'] ?? null,
            'responsibility' => $assignment['responsibility'] ?? null,
            'status' => $assignment['status'] ?? null,
        ];"""),
]
for name, args, body in domain_methods:
    extra += [f"    public static function {name}({args}): array", "    {"] + ["        " + x for x in body.splitlines()] + ["    }", ""]

# Add explicit workflow helpers.
workflow_states = {
    "draftToPublished": ("draft", "published"),
    "publishedToActive": ("published", "active"),
    "activeToCompleted": ("active", "completed"),
    "pendingToConfirmed": ("pending", "confirmed"),
    "confirmedToCompleted": ("confirmed", "completed"),
    "pendingToCancelled": ("pending", "cancelled"),
    "confirmedToCancelled": ("confirmed", "cancelled"),
}
for name, (old, new) in workflow_states.items():
    extra += [
        f"    public static function can{name[0].upper() + name[1:]}(mixed $status): bool",
        "    {",
        f"        return $status === '{old}';",
        "    }",
        "",
        f"    public static function {name[0].upper() + name[1:]}(array $row): array",
        "    {",
        "        $row['status'] = '" + new + "';",
        "        $row['updated_at'] = (new \\DateTimeImmutable())->format('Y-m-d H:i:s');",
        "        return $row;",
        "    }",
        "",
    ]

# Add field-level normalization methods.
normalizers = {
    "normalizeEmail": "return ProjectToolkit::lower(ProjectToolkit::trimString($value));",
    "normalizePhone": "$digits = preg_replace('/\\D+/', '', $value) ?? ''; return $digits;",
    "normalizeCode": "return ProjectToolkit::upper(ProjectToolkit::trimString($value));",
    "normalizeName": "return ProjectToolkit::trimString($value);",
    "normalizeTitle": "return ProjectToolkit::trimString($value);",
    "normalizeRole": "return ProjectToolkit::lower(ProjectToolkit::trimString($value));",
    "normalizeStatus": "return ProjectToolkit::lower(ProjectToolkit::trimString($value));",
    "normalizeDestination": "return ProjectToolkit::trimString($value);",
    "normalizeDescription": "return trim($value);",
    "normalizeNote": "return trim($value);",
}
for name, body in normalizers.items():
    extra += [
        f"    public static function {name}(string $value): string",
        "    {",
        f"        {body}",
        "    }",
        "",
    ]

# Add a configurable table/report pipeline.
extra += [
    "    public static function buildReport(array $rows, array $config = []): array",
    "    {",
    "        $result = $rows;",
    "",
    "        if (isset($config['search']) && is_string($config['search']) && $config['search'] !== '') {",
    "            $fields = $config['search_fields'] ?? ['name', 'title', 'code'];",
    "            $result = array_values(array_filter($result, static function ($row) use ($config, $fields) {",
    "                if (!is_array($row)) {",
    "                    return false;",
    "                }",
    "                $query = ProjectToolkit::lower(ProjectToolkit::trimString((string) $config['search']));",
    "                foreach ($fields as $field) {",
    "                    if (strpos(ProjectToolkit::lower((string) ($row[$field] ?? '')), $query) !== false) {",
    "                        return true;",
    "                    }",
    "                }",
    "                return false;",
    "            }));",
    "        }",
    "",
    "        if (isset($config['status'])) {",
    "            $result = array_values(array_filter($result, static fn ($row) => is_array($row) && ($row['status'] ?? null) == $config['status']));",
    "        }",
    "",
    "        if (isset($config['sort'])) {",
    "            $field = (string) $config['sort'];",
    "            $descending = ProjectToolkit::toBool($config['direction'] ?? false);",
    "            usort($result, static function ($a, $b) use ($field, $descending) {",
    "                $left = $a[$field] ?? null;",
    "                $right = $b[$field] ?? null;",
    "                $comparison = is_numeric($left) && is_numeric($right)",
    "                    ? ((float) $left <=> (float) $right)",
    "                    : strcasecmp((string) $left, (string) $right);",
    "                return $descending ? -$comparison : $comparison;",
    "            });",
    "        }",
    "",
    "        $total = count($result);",
    "        if (isset($config['page'])) {",
    "            $page = max(1, ProjectToolkit::toInt($config['page'], 1));",
    "            $perPage = max(1, ProjectToolkit::toInt($config['per_page'] ?? 15, 15));",
    "            $data = array_slice($result, ($page - 1) * $perPage, $perPage);",
    "        } else {",
    "            $page = 1;",
    "            $perPage = max(1, $total);",
    "            $data = $result;",
    "        }",
    "",
    "        return [",
    "            'data' => $data,",
    "            'meta' => ProjectToolkit::paginationMeta($total, $page, $perPage),",
    "        ];",
    "    }",
    "",
    "    public static function makeAuditEntry(string $action, string $entity, mixed $entityId, array $changes = []): array",
    "    {",
    "        return [",
    "            'action' => $action,",
    "            'entity' => $entity,",
    "            'entity_id' => $entityId,",
    "            'changes' => $changes,",
    "            'created_at' => (new \\DateTimeImmutable())->format('Y-m-d H:i:s'),",
    "        ];",
    "    }",
    "",
    "    public static function diffArrays(array $before, array $after): array",
    "    {",
    "        $changes = [];",
    "        $keys = array_unique(array_merge(array_keys($before), array_keys($after)));",
    "        foreach ($keys as $key) {",
    "            $old = $before[$key] ?? null;",
    "            $new = $after[$key] ?? null;",
    "            if ($old !== $new) {",
    "                $changes[$key] = ['old' => $old, 'new' => $new];",
    "            }",
    "        }",
    "        return $changes;",
    "    }",
    "",
]

# Generate explicit date-window filters.
for name, operator in [
    ("filterUpcoming", ">="),
    ("filterBefore", "<"),
    ("filterAfter", ">"),
]:
    extra += [
        f"    public static function {name}(array $rows, string $field, \\DateTimeInterface $date): array",
        "    {",
        "        $result = [];",
        "        foreach ($rows as $row) {",
        "            if (!is_array($row) || !isset($row[$field])) {",
        "                continue;",
        "            }",
        "            try {",
        "                $value = new \\DateTimeImmutable((string) $row[$field]);",
        f"                if ($value {operator} $date) {{",
        "                    $result[] = $row;",
        "                }",
        "            } catch (\\Throwable) {",
        "                continue;",
        "            }",
        "        }",
        "        return $result;",
        "    }",
        "",
    ]

extra.append("}\n")
new_content = content.rstrip() + "\n" + "\n".join(extra)

path.write_text(new_content, encoding="utf-8")

# Validate PHP syntax if PHP is available.
result = subprocess.run(
    ["php", "-l", str(path)],
    capture_output=True,
    text=True
)
print(f"Đã thêm code vào: {path.name}")
print(f"Tổng số dòng: {len(new_content.splitlines()):,}")
print(f"Kích thước: {path.stat().st_size / 1024:.1f} KB")
print(result.stdout.strip() or result.stderr.strip())
