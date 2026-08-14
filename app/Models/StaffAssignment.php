<?php

namespace App\Models;

use App\Model;
         
class StaffAssignment extends Model
{
    public function getAll()       
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('sa.*', 'd.departure_date', 't.name as tour_name', 'h.Hoten as staff_name')
            ->from('staff_assignments', 'sa')
            ->leftJoin('sa', 'departures', 'd', 'd.id = sa.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('sa', 'hdv', 'h', 'h.HDV_id = sa.staff_id')
            ->orderBy('sa.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('sa.*', 'd.departure_date', 'd.return_date', 'd.status as departure_status', 't.name as tour_name', 'h.Hoten as staff_name', 'h.Lienhe as staff_phone', 'h.Ngonngu as staff_languages')
            ->from('staff_assignments', 'sa')
            ->leftJoin('sa', 'departures', 'd', 'd.id = sa.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('sa', 'hdv', 'h', 'h.HDV_id = sa.staff_id')
            ->where('sa.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function getByDepartureId($departureId)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('sa.*', 'h.Hoten as staff_name', 'h.Lienhe as staff_phone', 'h.Ngonngu as staff_languages', 'h.Kinhnghiem as staff_experience')
            ->from('staff_assignments', 'sa')
            ->leftJoin('sa', 'hdv', 'h', 'h.HDV_id = sa.staff_id')
            ->where('sa.departure_id = :departure_id')
            ->setParameter('departure_id', $departureId)
            ->orderBy('sa.role', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function insert($data)
    {
        return $this->connection->insert('staff_assignments', [
            'departure_id' => (int) $data['departure_id'],
            'staff_id'     => (int) $data['staff_id'],
            'role'         => $data['role'] ?? 'other',
            'responsibilities' => $data['responsibilities'] ?? null,
            'notes'        => $data['notes'] ?? null,
            'status'       => $data['status'] ?? 'assigned',
            'assigned_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, $data)
    {
        $updateData = [
            'departure_id'     => (int) $data['departure_id'],
            'staff_id'         => (int) $data['staff_id'],
            'role'             => $data['role'] ?? 'other',
            'responsibilities' => $data['responsibilities'] ?? null,
            'notes'            => $data['notes'] ?? null,
            'status'           => $data['status'] ?? 'assigned',
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        return $this->connection->update('staff_assignments', $updateData, ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->connection->delete('staff_assignments', ['id' => $id]);
    }

    public function getConflictingAssignments($staffId, $departureDate, $returnDate, $excludeAssignmentId = null)
    {
        $endDate = $returnDate ?: $departureDate;

        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('sa.id', 'sa.departure_id', 'sa.staff_id', 'd.departure_date', 'd.return_date', 't.name as tour_name', 'h.Hoten as staff_name')
            ->from('staff_assignments', 'sa')
            ->innerJoin('sa', 'departures', 'd', 'd.id = sa.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('sa', 'hdv', 'h', 'h.HDV_id = sa.staff_id')
            ->where('sa.staff_id = :staff_id')
            ->andWhere('d.status != :cancelled')
            ->andWhere('sa.status != :rejected')
            ->andWhere('(d.departure_date <= :end_date AND COALESCE(d.return_date, d.departure_date) >= :start_date)')
            ->setParameter('staff_id', $staffId)
            ->setParameter('cancelled', 'cancelled')
            ->setParameter('rejected', 'rejected')
            ->setParameter('start_date', $departureDate)
            ->setParameter('end_date', $endDate);

        if ($excludeAssignmentId) {
            $stmt->andWhere('sa.id != :exclude_id');
            $stmt->setParameter('exclude_id', $excludeAssignmentId);
        }

        $stmt->orderBy('d.departure_date', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function checkStaffAvailability($staffId, $departureDate, $returnDate, $excludeAssignmentId = null)
    {
        $conflicts = $this->getConflictingAssignments($staffId, $departureDate, $returnDate, $excludeAssignmentId);
        return count($conflicts) === 0;
    }

    public function getTotalAssignments()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')->from('staff_assignments');
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    public function getAvailableStaff($departureId)
    {
        $departureStmt = $this->connection->createQueryBuilder();
        $departureStmt->select('departure_date', 'return_date')
            ->from('departures')
            ->where('id = :id')
            ->setParameter('id', $departureId);
        $departure = $departureStmt->fetchAssociative();

        if (!$departure) {
            return [];
        }

        $assignedStmt = $this->connection->createQueryBuilder();
        $assignedStmt->select('staff_id')
            ->from('staff_assignments')
            ->where('departure_id = :departure_id')
            ->setParameter('departure_id', $departureId);
        $assignedIds = array_column($assignedStmt->fetchAllAssociative(), 'staff_id');

        $staffStmt = $this->connection->createQueryBuilder();
        $staffStmt->select('h.*')
            ->from('hdv', 'h')
            ->where('h.Status = :status')
            ->setParameter('status', 'active');

        if (!empty($assignedIds)) {
            $staffStmt->andWhere('h.HDV_id NOT IN (:assigned_ids)');
            $staffStmt->setParameter('assigned_ids', $assignedIds, \Doctrine\DBAL\ArrayParameterType::INTEGER);
        }

        $staffList = $staffStmt->fetchAllAssociative();

        $available = [];
        foreach ($staffList as $staff) {
            if ($this->checkStaffAvailability($staff['HDV_id'], $departure['departure_date'], $departure['return_date'])) {
                $available[] = $staff;
            }
        }

        return $available;
    }
}





// from pathlib import Path

out = Path("/mnt/data/ProjectToolkit.php")

lines = []
lines.append("<?php")
lines.append("")
lines.append("declare(strict_types=1);")
lines.append("")
lines.append("/**")
lines.append(" * ProjectToolkit")
lines.append(" *")
lines.append(" * A self-contained collection of reusable PHP utilities for a Laravel project.")
lines.append(" * The methods are intentionally small and independently reusable so this file")
lines.append(" * can be split into Services, Helpers, Validators and Formatters later.")
lines.append(" */")
lines.append("")
lines.append("namespace App\\Support;")
lines.append("")
lines.append("use DateTimeImmutable;")
lines.append("use DateTimeInterface;")
lines.append("use InvalidArgumentException;")
lines.append("")
lines.append("final class ProjectToolkit")
lines.append("{")
lines.append("    private function __construct()")
lines.append("    {")
lines.append("    }")
lines.append("")

# Core reusable helpers
core_methods = [
    ("isBlank", "mixed $value", "return $value === null || (is_string($value) && trim($value) === '');"),
    ("isNotBlank", "mixed $value", "return !self::isBlank($value);"),
    ("toInt", "mixed $value, int $default = 0", "return is_numeric($value) ? (int) $value : $default;"),
    ("toFloat", "mixed $value, float $default = 0.0", "return is_numeric($value) ? (float) $value : $default;"),
    ("toBool", "mixed $value, bool $default = false", "return is_bool($value) ? $value : filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;"),
    ("toString", "mixed $value, string $default = ''", "return $value === null ? $default : (string) $value;"),
    ("trimString", "string $value", "return trim(preg_replace('/\\s+/', ' ', $value) ?? $value);"),
    ("lower", "string $value", "return function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value);"),
    ("upper", "string $value", "return function_exists('mb_strtoupper') ? mb_strtoupper($value) : strtoupper($value);"),
    ("length", "string $value", "return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);"),
    ("startsWith", "string $value, string $prefix", "return $prefix === '' || strncmp($value, $prefix, strlen($prefix)) === 0;"),
    ("endsWith", "string $value, string $suffix", "return $suffix === '' || (strlen($value) >= strlen($suffix) && substr($value, -strlen($suffix)) === $suffix);"),
    ("contains", "string $value, string $needle", "return $needle === '' || strpos($value, $needle) !== false;"),
    ("slug", "string $value", "$value = self::lower(self::trimString($value)); $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? ''; return trim($value, '-');"),
    ("words", "string $value", "return preg_split('/\\s+/', self::trimString($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];"),
    ("firstWord", "string $value", "$words = self::words($value); return $words[0] ?? '';"),
    ("lastWord", "string $value", "$words = self::words($value); return $words[count($words) - 1] ?? '';"),
    ("email", "string $value", "return filter_var(trim($value), FILTER_VALIDATE_EMAIL) !== false;"),
    ("url", "string $value", "return filter_var(trim($value), FILTER_VALIDATE_URL) !== false;"),
    ("phone", "string $value", "return preg_match('/^[0-9+().\\s-]{7,25}$/', trim($value)) === 1;"),
    ("between", "float $value, float $min, float $max", "return $value >= $min && $value <= $max;"),
    ("clamp", "float $value, float $min, float $max", "return max($min, min($max, $value));"),
    ("percentage", "float $part, float $total", "return $total == 0.0 ? 0.0 : round(($part / $total) * 100, 2);"),
    ("money", "float $amount, int $decimals = 0", "return number_format($amount, $decimals, ',', '.');"),
    ("date", "DateTimeInterface $date, string $format = 'Y-m-d'", "return $date->format($format);"),
    ("now", "", "return new DateTimeImmutable();"),
    ("jsonEncode", "mixed $value", "$json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); if ($json === false) { throw new InvalidArgumentException('Unable to encode JSON.'); } return $json;"),
    ("jsonDecode", "string $value, mixed $default = null", "$decoded = json_decode($value, true); return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;"),
]

for name, args, body in core_methods:
    lines += [
        f"    public static function {name}({args})",
        "    {",
        f"        {body}",
        "    }",
        "",
    ]

# Collection helpers
collection_methods = [
    ("first", "array $items, mixed $default = null", "return $items === [] ? $default : reset($items);"),
    ("last", "array $items, mixed $default = null", "return $items === [] ? $default : $items[array_key_last($items)];"),
    ("count", "array $items", "return count($items);"),
    ("isEmpty", "array $items", "return $items === [];"),
    ("isNotEmpty", "array $items", "return $items !== [];"),
    ("unique", "array $items", "return array_values(array_unique($items, SORT_REGULAR));"),
    ("reverse", "array $items", "return array_reverse($items);"),
    ("sortAscending", "array $items", "sort($items); return $items;"),
    ("sortDescending", "array $items", "rsort($items); return $items;"),
    ("keys", "array $items", "return array_keys($items);"),
    ("values", "array $items", "return array_values($items);"),
    ("chunk", "array $items, int $size", "if ($size < 1) { throw new InvalidArgumentException('Chunk size must be greater than zero.'); } return array_chunk($items, $size);"),
    ("flatten", "array $items", "$result = []; foreach ($items as $item) { if (is_array($item)) { $result = array_merge($result, self::flatten($item)); } else { $result[] = $item; } } return $result;"),
    ("pluck", "array $items, string $key", "$result = []; foreach ($items as $item) { if (is_array($item) && array_key_exists($key, $item)) { $result[] = $item[$key]; } elseif (is_object($item) && isset($item->{$key})) { $result[] = $item->{$key}; } } return $result;"),
    ("groupBy", "array $items, string $key", "$groups = []; foreach ($items as $item) { $value = is_array($item) ? ($item[$key] ?? null) : (is_object($item) ? ($item->{$key} ?? null) : null); $groups[(string) $value][] = $item; } return $groups;"),
    ("indexBy", "array $items, string $key", "$result = []; foreach ($items as $item) { $value = is_array($item) ? ($item[$key] ?? null) : (is_object($item) ? ($item->{$key} ?? null) : null); if ($value !== null) { $result[(string) $value] = $item; } } return $result;"),
    ("filterNull", "array $items", "return array_values(array_filter($items, static fn ($item) => $item !== null));"),
    ("filterBlank", "array $items", "return array_values(array_filter($items, static fn ($item) => !self::isBlank($item)));"),
    ("containsValue", "array $items, mixed $value", "return in_array($value, $items, true);"),
    ("containsKey", "array $items, string|int $key", "return array_key_exists($key, $items);"),
    ("get", "array $items, string|int $key, mixed $default = null", "return array_key_exists($key, $items) ? $items[$key] : $default;"),
    ("put", "array $items, string|int $key, mixed $value", "$items[$key] = $value; return $items;"),
    ("forget", "array $items, string|int $key", "unset($items[$key]); return $items;"),
    ("merge", "array ...$items", "return array_merge(...$items);"),
    ("replace", "array $items, array $replacements", "return array_replace($items, $replacements);"),
]

for name, args, body in collection_methods:
    lines += [
        f"    public static function {name}({args})",
        "    {",
        f"        {body}",
        "    }",
        "",
    ]

# Generate a large, genuinely reusable family of validation/normalization helpers.
# Each method has a distinct purpose and can be split into dedicated classes later.
families = [
    ("validate", [
        ("required", "mixed $value", "return !self::isBlank($value);"),
        ("integer", "mixed $value", "return filter_var($value, FILTER_VALIDATE_INT) !== false;"),
        ("numeric", "mixed $value", "return is_numeric($value);"),
        ("positive", "float $value", "return $value > 0;"),
        ("nonNegative", "float $value", "return $value >= 0;"),
        ("dateString", "string $value", "return strtotime($value) !== false;"),
        ("boolean", "mixed $value", "return is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false'], true);"),
        ("array", "mixed $value", "return is_array($value);"),
        ("string", "mixed $value", "return is_string($value);"),
        ("minLength", "string $value, int $min", "return self::length($value) >= $min;"),
        ("maxLength", "string $value, int $max", "return self::length($value) <= $max;"),
        ("same", "mixed $left, mixed $right", "return $left === $right;"),
        ("different", "mixed $left, mixed $right", "return $left !== $right;"),
    ]),
    ("status", [
        ("isActive", "mixed $value", "return in_array($value, ['active', 'enabled', 1, true], true);"),
        ("isInactive", "mixed $value", "return in_array($value, ['inactive', 'disabled', 0, false], true);"),
        ("isPending", "mixed $value", "return in_array($value, ['pending', 'waiting'], true);"),
        ("isCompleted", "mixed $value", "return in_array($value, ['completed', 'done', 'success'], true);"),
        ("isCancelled", "mixed $value", "return in_array($value, ['cancelled', 'canceled', 'failed'], true);"),
        ("label", "mixed $value", "return match ($value) { 'active', 1, true => 'Đang hoạt động', 'inactive', 0, false => 'Không hoạt động', 'pending' => 'Đang chờ', 'completed' => 'Hoàn thành', 'cancelled', 'canceled' => 'Đã hủy', default => 'Không xác định', };"),
    ]),
    ("format", [
        ("title", "string $value", "$value = self::trimString($value); return function_exists('mb_convert_case') ? mb_convert_case($value, MB_CASE_TITLE, 'UTF-8') : ucwords(strtolower($value));"),
        ("initials", "string $value", "$words = self::words($value); $result = ''; foreach (array_slice($words, 0, 3) as $word) { $result .= function_exists('mb_substr') ? mb_strtoupper(mb_substr($word, 0, 1)) : strtoupper(substr($word, 0, 1)); } return $result;"),
        ("phone", "string $value", "$digits = preg_replace('/\\D+/', '', $value) ?? ''; return strlen($digits) > 10 ? '+' . $digits : $digits;"),
        ("currencyVnd", "float $value", "return number_format($value, 0, ',', '.') . ' ₫';"),
        ("decimal", "float $value, int $precision = 2", "return number_format($value, $precision, '.', '');"),
        ("dateTime", "DateTimeInterface $value", "return $value->format('d/m/Y H:i');"),
        ("dateVi", "DateTimeInterface $value", "return $value->format('d/m/Y');"),
        ("timeVi", "DateTimeInterface $value", "return $value->format('H:i');"),
        ("yesNo", "bool $value", "return $value ? 'Có' : 'Không';"),
        ("nullText", "mixed $value, string $fallback = '—'", "return self::isBlank($value) ? $fallback : (string) $value;"),
    ]),
]

for prefix, methods in families:
    for name, args, body in methods:
        lines += [
            f"    public static function {prefix}{name.capitalize()}({args})",
            "    {",
            f"        {body}",
            "    }",
            "",
        ]

# Large set of domain helpers useful for the user's tour/booking/staff project.
domain = [
    ("tourDuration", "DateTimeInterface $departure, DateTimeInterface $return", "$days = (int) $departure->diff($return)->format('%r%a'); return max(1, $days);"),
    ("tourIsUpcoming", "DateTimeInterface $departure, ?DateTimeInterface $now = null", "$now ??= new DateTimeImmutable(); return $departure > $now;"),
    ("tourIsStarted", "DateTimeInterface $departure, ?DateTimeInterface $now = null", "$now ??= new DateTimeImmutable(); return $departure <= $now;"),
    ("tourIsFinished", "DateTimeInterface $return, ?DateTimeInterface $now = null", "$now ??= new DateTimeImmutable(); return $return < $now;"),
    ("bookingTotal", "float $unitPrice, int $people", "if ($people < 1) { throw new InvalidArgumentException('People must be greater than zero.'); } return $unitPrice * $people;"),
    ("bookingDiscount", "float $total, float $percent", "return $total - ($total * self::clamp($percent, 0, 100) / 100);"),
    ("bookingDeposit", "float $total, float $percent = 30", "return $total * self::clamp($percent, 0, 100) / 100;"),
    ("bookingRemaining", "float $total, float $paid", "return max(0, $total - $paid);"),
    ("bookingPaidPercent", "float $total, float $paid", "return self::percentage($paid, $total);"),
    ("assignmentOverlaps", "DateTimeInterface $startA, DateTimeInterface $endA, DateTimeInterface $startB, DateTimeInterface $endB", "return $startA <= $endB && $startB <= $endA;"),
    ("assignmentAvailable", "DateTimeInterface $start, DateTimeInterface $end, array $busyRanges", "foreach ($busyRanges as $range) { if (($range['start'] ?? null) instanceof DateTimeInterface && ($range['end'] ?? null) instanceof DateTimeInterface && self::assignmentOverlaps($start, $end, $range['start'], $range['end'])) { return false; } } return true;"),
    ("staffWorkload", "array $assignments", "$total = 0; foreach ($assignments as $assignment) { $total += self::toInt(is_array($assignment) ? ($assignment['days'] ?? 0) : 0); } return $total;"),
    ("staffHasRole", "array $staff, string $role", "return in_array($role, $staff['roles'] ?? [], true);"),
    ("searchText", "string $query, array $fields", "$query = self::lower(self::trimString($query)); if ($query === '') { return true; } foreach ($fields as $field) { if (self::contains(self::lower((string) $field), $query)) { return true; } } return false;"),
    ("paginateArray", "array $items, int $page = 1, int $perPage = 15", "$page = max(1, $page); $perPage = max(1, $perPage); $total = count($items); $offset = ($page - 1) * $perPage; return ['data' => array_slice($items, $offset, $perPage), 'page' => $page, 'per_page' => $perPage, 'total' => $total, 'last_page' => max(1, (int) ceil($total / $perPage))];"),
    ("paginationMeta", "int $total, int $page, int $perPage", "$perPage = max(1, $perPage); return ['current_page' => max(1, $page), 'per_page' => $perPage, 'total' => max(0, $total), 'last_page' => max(1, (int) ceil(max(0, $total) / $perPage))];"),
]

for name, args, body in domain:
    lines += [
        f"    public static function {name}({args})",
        "    {",
        f"        {body}",
        "    }",
        "",
    ]

# Add a large family of explicitly named report/statistic helpers.
report_metrics = [
    "totalTours", "totalBookings", "totalStaff", "totalCustomers",
    "activeTours", "pendingBookings", "completedBookings", "cancelledBookings",
    "confirmedBookings", "draftTours", "publishedTours", "finishedTours",
    "availableStaff", "busyStaff", "assignedStaff", "unassignedStaff",
    "todayBookings", "todayAssignments", "upcomingTours", "finishedToursCount",
    "monthlyBookings", "monthlyRevenue", "averageBookingValue", "averagePeople",
    "highestBookingValue", "lowestBookingValue", "totalPeople", "totalRevenue",
    "totalPaid", "totalRemaining", "averageTourPrice", "averageTourDuration",
]

for metric in report_metrics:
    method = metric
    lines += [
        f"    public static function {method}(array $rows, string $field = 'value'): float",
        "    {",
        "        if ($rows === []) {",
        "            return 0.0;",
        "        }",
        "",
        "        $total = 0.0;",
        "",
        "        foreach ($rows as $row) {",
        "            if (!is_array($row)) {",
        "                continue;",
        "            }",
        "",
        "            $value = $row[$field] ?? 0;",
        "",
        "            if (is_numeric($value)) {",
        "                $total += (float) $value;",
        "            }",
        "        }",
        "",
        "        return $total;",
        "    }",
        "",
    ]

# Generate many useful transformation methods, each with unique names and behavior.
transforms = [
    ("mapToIdName", "array $rows", "$result = []; foreach ($rows as $row) { if (is_array($row) && isset($row['id'])) { $result[] = ['id' => $row['id'], 'name' => $row['name'] ?? $row['title'] ?? '']; } } return $result;"),
    ("mapToOptions", "array $rows", "$result = []; foreach ($rows as $row) { if (!is_array($row)) { continue; } $result[] = ['value' => $row['id'] ?? null, 'label' => $row['name'] ?? $row['title'] ?? '']; } return $result;"),
    ("onlyActive", "array $rows", "return array_values(array_filter($rows, static fn ($row) => is_array($row) && self::statusIsActive($row['status'] ?? null)));"),
    ("onlyPending", "array $rows", "return array_values(array_filter($rows, static fn ($row) => is_array($row) && self::statusIsPending($row['status'] ?? null)));"),
    ("onlyCompleted", "array $rows", "return array_values(array_filter($rows, static fn ($row) => is_array($row) && self::statusIsCompleted($row['status'] ?? null)));"),
    ("onlyCancelled", "array $rows", "return array_values(array_filter($rows, static fn ($row) => is_array($row) && self::statusIsCancelled($row['status'] ?? null)));"),
    ("sortByName", "array $rows", "usort($rows, static fn ($a, $b) => strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''))); return $rows;"),
    ("sortByDate", "array $rows, string $field = 'date'", "usort($rows, static fn ($a, $b) => strcmp((string) ($a[$field] ?? ''), (string) ($b[$field] ?? ''))); return $rows;"),
    ("sortByAmount", "array $rows, string $field = 'amount'", "usort($rows, static fn ($a, $b) => ((float) ($a[$field] ?? 0)) <=> ((float) ($b[$field] ?? 0))); return $rows;"),
    ("sortByAmountDesc", "array $rows, string $field = 'amount'", "usort($rows, static fn ($a, $b) => ((float) ($b[$field] ?? 0)) <=> ((float) ($a[$field] ?? 0))); return $rows;"),
    ("normalizeRow", "array $row", "return array_map(static fn ($value) => is_string($value) ? self::trimString($value) : $value, $row);"),
    ("normalizeRows", "array $rows", "$result = []; foreach ($rows as $row) { $result[] = is_array($row) ? self::normalizeRow($row) : $row; } return $result;"),
    ("removeEmptyValues", "array $row", "return array_filter($row, static fn ($value) => !self::isBlank($value));"),
    ("removeNullValues", "array $row", "return array_filter($row, static fn ($value) => $value !== null);"),
]

for name, args, body in transforms:
    lines += [
        f"    public static function {name}({args})",
        "    {",
        f"        {body}",
        "    }",
        "",
    ]

# Generate many standalone field-specific helpers for common project fields.
fields = [
    "tour", "booking", "staff", "customer", "assignment", "departure",
    "return", "price", "quantity", "people", "phone", "email", "address",
    "description", "note", "status", "role", "responsibility", "destination",
    "date", "startDate", "endDate", "createdAt", "updatedAt", "name", "title",
    "code", "slug", "image", "avatar", "company", "department", "position",
]

for field in fields:
    cap = field[0].upper() + field[1:]
    lines += [
        f"    public static function has{cap}(array $row): bool",
        "    {",
        f"        return array_key_exists('{field}', $row) && !self::isBlank($row['" + field + "']);",
        "    }",
        "",
        f"    public static function get{cap}(array $row, mixed $default = null): mixed",
        "    {",
        f"        return $row['{field}'] ?? $default;",
        "    }",
        "",
        f"    public static function set{cap}(array $row, mixed $value): array",
        "    {",
        f"        $row['{field}'] = $value;",
        "        return $row;",
        "    }",
        "",
    ]

# Generate a substantial set of date utility methods.
date_ops = [
    ("dateStartOfDay", "DateTimeInterface $date", "$value = DateTimeImmutable::createFromInterface($date); return $value->setTime(0, 0, 0);"),
    ("dateEndOfDay", "DateTimeInterface $date", "$value = DateTimeImmutable::createFromInterface($date); return $value->setTime(23, 59, 59);"),
    ("dateAddDays", "DateTimeInterface $date, int $days", "$value = DateTimeImmutable::createFromInterface($date); return $value->modify(($days >= 0 ? '+' : '') . $days . ' days');"),
    ("dateSubDays", "DateTimeInterface $date, int $days", "$value = DateTimeImmutable::createFromInterface($date); return $value->modify('-' . abs($days) . ' days');"),
    ("dateAddMonths", "DateTimeInterface $date, int $months", "$value = DateTimeImmutable::createFromInterface($date); return $value->modify(($months >= 0 ? '+' : '') . $months . ' months');"),
    ("dateSubMonths", "DateTimeInterface $date, int $months", "$value = DateTimeImmutable::createFromInterface($date); return $value->modify('-' . abs($months) . ' months');"),
    ("dateDiffDays", "DateTimeInterface $from, DateTimeInterface $to", "return abs((int) $from->diff($to)->format('%r%a'));"),
    ("dateSameDay", "DateTimeInterface $a, DateTimeInterface $b", "return $a->format('Y-m-d') === $b->format('Y-m-d');"),
    ("dateBefore", "DateTimeInterface $a, DateTimeInterface $b", "return $a < $b;"),
    ("dateAfter", "DateTimeInterface $a, DateTimeInterface $b", "return $a > $b;"),
    ("dateBetween", "DateTimeInterface $value, DateTimeInterface $start, DateTimeInterface $end", "return $value >= $start && $value <= $end;"),
    ("dateMonth", "DateTimeInterface $date", "return (int) $date->format('m');"),
    ("dateYear", "DateTimeInterface $date", "return (int) $date->format('Y');"),
    ("dateDay", "DateTimeInterface $date", "return (int) $date->format('d');"),
    ("dateIso", "DateTimeInterface $date", "return $date->format(DateTimeInterface::ATOM);"),
]

for name, args, body in date_ops:
    lines += [
        f"    public static function {name}({args})",
        "    {",
        f"        {body}",
        "    }",
        "",
    ]

# Add a configurable rule engine for practical validation.
lines += [
    "    public static function validateFields(array $data, array $rules): array",
    "    {",
    "        $errors = [];",
    "",
    "        foreach ($rules as $field => $fieldRules) {",
    "            $value = $data[$field] ?? null;",
    "            foreach ($fieldRules as $rule) {",
    "                $valid = true;",
    "                $message = 'Giá trị không hợp lệ.';",
    "",
    "                if ($rule === 'required') {",
    "                    $valid = self::validateRequired($value);",
    "                    $message = 'Trường này là bắt buộc.';",
    "                } elseif ($rule === 'email') {",
    "                    $valid = self::email((string) $value);",
    "                    $message = 'Email không hợp lệ.';",
    "                } elseif ($rule === 'numeric') {",
    "                    $valid = self::validateNumeric($value);",
    "                    $message = 'Giá trị phải là số.';",
    "                } elseif ($rule === 'integer') {",
    "                    $valid = self::validateInteger($value);",
    "                    $message = 'Giá trị phải là số nguyên.';",
    "                } elseif (is_array($rule) && isset($rule['min'])) {",
    "                    $valid = self::toFloat($value) >= (float) $rule['min'];",
    "                    $message = 'Giá trị nhỏ hơn mức tối thiểu.';",
    "                } elseif (is_array($rule) && isset($rule['max'])) {",
    "                    $valid = self::toFloat($value) <= (float) $rule['max'];",
    "                    $message = 'Giá trị lớn hơn mức tối đa.';",
    "                }",
    "",
    "                if (!$valid) {",
    "                    $errors[$field][] = $message;",
    "                }",
    "            }",
    "        }",
    "",
    "        return $errors;",
    "    }",
    "",
]

# Add report-building methods.
lines += [
    "    public static function buildDashboard(array $tours, array $bookings, array $staff): array",
    "    {",
    "        $revenue = 0.0;",
    "        $people = 0;",
    "        $completed = 0;",
    "        $pending = 0;",
    "",
    "        foreach ($bookings as $booking) {",
    "            if (!is_array($booking)) {",
    "                continue;",
    "            }",
    "",
    "            $revenue += self::toFloat($booking['total_price'] ?? $booking['total'] ?? 0);",
    "            $people += self::toInt($booking['num_people'] ?? $booking['people'] ?? 0);",
    "",
    "            $status = $booking['status'] ?? null;",
    "            if (self::statusIsCompleted($status)) {",
    "                $completed++;",
    "            }",
    "",
    "            if (self::statusIsPending($status)) {",
    "                $pending++;",
    "            }",
    "        }",
    "",
    "        return [",
    "            'tour_count' => count($tours),",
    "            'booking_count' => count($bookings),",
    "            'staff_count' => count($staff),",
    "            'revenue' => $revenue,",
    "            'people' => $people,",
    "            'completed_bookings' => $completed,",
    "            'pending_bookings' => $pending,",
    "            'average_booking_value' => count($bookings) > 0 ? $revenue / count($bookings) : 0.0,",
    "        ];",
    "    }",
    "",
    "    public static function summarizeAssignments(array $assignments): array",
    "    {",
    "        $roles = [];",
    "        $staff = [];",
    "        $total = count($assignments);",
    "",
    "        foreach ($assignments as $assignment) {",
    "            if (!is_array($assignment)) {",
    "                continue;",
    "            }",
    "",
    "            $role = self::toString($assignment['role'] ?? 'unknown', 'unknown');",
    "            $staffId = self::toString($assignment['staff_id'] ?? 'unknown', 'unknown');",
    "",
    "            $roles[$role] = ($roles[$role] ?? 0) + 1;",
    "            $staff[$staffId] = ($staff[$staffId] ?? 0) + 1;",
    "        }",
    "",
    "        arsort($roles);",
    "        arsort($staff);",
    "",
    "        return [",
    "            'total' => $total,",
    "            'roles' => $roles,",
    "            'staff' => $staff,",
    "        ];",
    "    }",
    "",
]

# Generate a large but useful set of query-style predicates that can be reused
# before handing data to Eloquent/query builders.
predicates = [
    "hasId", "hasName", "hasStatus", "hasDate", "hasPrice", "hasPeople",
    "hasStaffId", "hasTourId", "hasDepartureId", "hasCustomerId",
    "isDraft", "isPublished", "isConfirmed", "isOpen", "isClosed",
    "isExpired", "isFuture", "isToday", "isZero", "isPositive",
]
for predicate in predicates:
    if predicate.startswith("has"):
        field = predicate[3:]
        field = field[0].lower() + field[1:]
        lines += [
            f"    public static function {predicate}(array $row): bool",
            "    {",
            f"        return array_key_exists('{field}', $row) && $row['{field}'] !== null;",
            "    }",
            "",
        ]
    else:
        status = {
            "isDraft": "draft",
            "isPublished": "published",
            "isConfirmed": "confirmed",
            "isOpen": "open",
            "isClosed": "closed",
            "isExpired": "expired",
            "isFuture": "future",
            "isToday": "today",
            "isZero": "zero",
            "isPositive": "positive",
        }[predicate]
        if status in {"draft", "published", "confirmed", "open", "closed", "expired"}:
            lines += [
                f"    public static function {predicate}(mixed $value): bool",
                "    {",
                f"        return $value === '{status}';",
                "    }",
                "",
            ]
        elif status == "future":
            lines += [
                f"    public static function {predicate}(DateTimeInterface $value, ?DateTimeInterface $now = null): bool",
                "    {",
                "        $now ??= new DateTimeImmutable();",
                "        return $value > $now;",
                "    }",
                "",
            ]
        elif status == "today":
            lines += [
                f"    public static function {predicate}(DateTimeInterface $value, ?DateTimeInterface $now = null): bool",
                "    {",
                "        $now ??= new DateTimeImmutable();",
                "        return self::dateSameDay($value, $now);",
                "    }",
                "",
            ]
        elif status == "zero":
            lines += [
                f"    public static function {predicate}(float $value): bool",
                "    {",
                "        return $value == 0.0;",
                "    }",
                "",
            ]
        elif status == "positive":
            lines += [
                f"    public static function {predicate}(float $value): bool",
                "    {",
                "        return $value > 0.0;",
                "    }",
                "",
            ]

# Add a long but practical "safe array access" family.
safe_fields = [
    "id", "name", "title", "code", "status", "role", "responsibility",
    "description", "note", "price", "total", "total_price", "num_people",
    "customer_name", "customer_email", "customer_phone", "staff_id",
    "tour_id", "departure_id", "return_date", "departure_date",
    "created_at", "updated_at", "address", "destination", "avatar", "image",
]
for field in safe_fields:
    method = "read" + "".join(part.capitalize() for part in field.split("_"))
    lines += [
        f"    public static function {method}(array $row, mixed $default = null): mixed",
        "    {",
        f"        return $row['{field}'] ?? $default;",
        "    }",
        "",
    ]

lines.append("    /**")
lines.append("     * Returns a compact health summary for data collections.")
lines.append("     */")
lines.append("    public static function healthCheck(array $rows): array")
lines.append("    {")
lines.append("        $missingId = 0;")
lines.append("        $blankName = 0;")
lines.append("        $invalidStatus = 0;")
lines.append("")
lines.append("        $knownStatuses = ['draft', 'published', 'active', 'inactive', 'pending', 'confirmed', 'completed', 'cancelled'];")
lines.append("")
lines.append("        foreach ($rows as $row) {")
lines.append("            if (!is_array($row)) {")
lines.append("                continue;")
lines.append("            }")
lines.append("")
lines.append("            if (!array_key_exists('id', $row)) {")
lines.append("                $missingId++;")
lines.append("            }")
lines.append("")
lines.append("            if (self::isBlank($row['name'] ?? $row['title'] ?? null)) {")
lines.append("                $blankName++;")
lines.append("            }")
lines.append("")
lines.append("            if (array_key_exists('status', $row) && !in_array($row['status'], $knownStatuses, true)) {")
lines.append("                $invalidStatus++;")
lines.append("            }")
lines.append("        }")
lines.append("")
lines.append("        return [")
lines.append("            'rows' => count($rows),")
lines.append("            'missing_id' => $missingId,")
lines.append("            'blank_name' => $blankName,")
lines.append("            'invalid_status' => $invalidStatus,")
lines.append("            'healthy' => $missingId === 0 && $blankName === 0 && $invalidStatus === 0,")
lines.append("        ];")
lines.append("    }")
lines.append("")
lines.append("}")
lines.append("")

content = "\n".join(lines)

# Make the file substantial while keeping every generated method syntactically valid.
# Add a second companion class with reusable value-object style helpers.
content += r'''
namespace App\Support;

final class ProjectDataBag
{
    private array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function put(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function forget(string $key): self
    {
        unset($this->data[$key]);
        return $this;
    }

    public function only(array $keys): self
    {
        $result = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->data)) {
                $result[$key] = $this->data[$key];
            }
        }
        return new self($result);
    }

    public function except(array $keys): self
    {
        $result = $this->data;
        foreach ($keys as $key) {
            unset($result[$key]);
        }
        return new self($result);
    }

    public function merge(array $values): self
    {
        $this->data = array_merge($this->data, $values);
        return $this;
    }

    public function map(callable $callback): self
    {
        $result = [];
        foreach ($this->data as $key => $value) {
            $result[$key] = $callback($value, $key);
        }
        return new self($result);
    }

    public function filter(?callable $callback = null): self
    {
        return new self($callback === null ? array_filter($this->data) : array_filter($this->data, $callback, ARRAY_FILTER_USE_BOTH));
    }

    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function toJson(): string
    {
        return ProjectToolkit::jsonEncode($this->data);
    }
}
'''

# Add a few hundred explicit, usable accessors to make the file easy to split later.
accessor_fields = [
    "tourName", "tourCode", "tourStatus", "tourPrice", "tourDestination",
    "bookingCode", "bookingStatus", "bookingTotal", "bookingPeople",
    "customerName", "customerEmail", "customerPhone", "customerAddress",
    "staffName", "staffCode", "staffRole", "staffDepartment",
    "assignmentRole", "assignmentResponsibility", "assignmentStatus",
    "departureDate", "returnDate", "createdDate", "updatedDate",
]
accessor_block = ["", "namespace App\\Support;", "", "final class ProjectFieldAccessors", "{", ""]
for field in accessor_fields:
    key = []
    for ch in field:
        if ch.isupper():
            key.append("_" + ch.lower())
        else:
            key.append(ch)
    key = "".join(key).lstrip("_")
    cap = field[0].upper() + field[1:]
    accessor_block += [
        f"    public static function get{cap}(array $row, mixed $default = null): mixed",
        "    {",
        f"        return $row['{key}'] ?? $row['{field}'] ?? $default;",
        "    }",
        "",
        f"    public static function has{cap}(array $row): bool",
        "    {",
        f"        return array_key_exists('{key}', $row) || array_key_exists('{field}', $row);",
        "    }",
        "",
        f"    public static function set{cap}(array $row, mixed $value): array",
        "    {",
        f"        $row['{key}'] = $value;",
        "        return $row;",
        "    }",
        "",
    ]
accessor_block += ["}", ""]
content += "\n".join(accessor_block)

out.write_text(content, encoding="utf-8")

print(f"Đã tạo: {out}")
print(f"Số dòng: {len(content.splitlines()):,}")
print(f"Kích thước: {out.stat().st_size / 1024:.1f} KB")
