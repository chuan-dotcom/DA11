<?php
/**
 * Fragment view: Danh sách người tham gia của 1 Booking (dùng cho modal AJAX).
 * Không dùng layout.
 */
$total      = (int) ($stats['total_guests']     ?? 0);
$checkedIn  = (int) ($stats['checked_in_guests'] ?? 0);
?>
<div class="mb-3 p-3 rounded-3 bg-light border">
    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div>
            <div class="small text-muted">Booking #<?= (int)$booking['id'] ?> · Khách đại diện</div>
            <div class="fw-semibold"><?= htmlentities($booking['customer_name']) ?></div>
            <div class="small text-muted">
                <?= htmlentities($booking['customer_phone']) ?> · <?= htmlentities($booking['customer_email']) ?>
            </div>
        </div>
        <div>
            <div class="small text-muted">Tour</div>
            <div class="fw-semibold d-flex align-items-center gap-2">
                <?= htmlentities($booking['tour_name']) ?>
                <?php if (!empty($booking['tour_location'])): ?>
                    <span class="badge bg-secondary">
                        <i class="bi bi-geo-alt me-1"></i><?= htmlentities($booking['tour_location']) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary">
                <i class="bi bi-people me-1"></i><?= $total ?> người
            </span>
            <span class="badge bg-success">
                <i class="bi bi-check-circle me-1"></i><?= $checkedIn ?> đã check-in
            </span>
            <span class="badge bg-danger text-light">
                <i class="bi bi-hourglass-split me-1"></i><?= max(0, $total - $checkedIn) ?> chờ
            </span>
        </div>
    </div>
</div>

<?php if (empty($guests)): ?>
    <div class="text-center text-muted py-5">
        <i class="bi bi-person-x fs-1 d-block mb-2 opacity-30"></i>
        Booking này chưa có danh sách người tham gia.
    </div>
<?php else: ?>
    <div class="table-responsive" style="max-height:55vh;overflow:auto">
        <table class="table table-sm table-bordered align-middle mb-0">
            <thead class="table-dark sticky-top">
                <tr>
                    <th width="60" class="text-center">STT</th>
                    <th>Họ tên</th>
                    <th width="100">Giới tính</th>
                    <th width="120">Ngày sinh</th>
                    <th width="130">SĐT</th>
                    <th width="140">CMND/CCCD</th>
                    <th width="120">Thanh toán</th>
                    <th width="110">Check-in</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($guests as $idx => $g): ?>
                    <?php
                    $genderText = '';
                    $genderClass = 'text-muted';
                    switch ($g['gender'] ?? '') {
                        case 'male':   $genderText = 'Nam';      $genderClass = 'text-primary';  break;
                        case 'female': $genderText = 'Nữ';       $genderClass = 'text-pink';     break;
                        case 'other':  $genderText = 'Khác';     $genderClass = 'text-secondary'; break;
                        default:       $genderText = '—';
                    }

                    $payMap = [
                        'unpaid'  => ['Chưa thanh toán', 'bg-secondary'],
                        'deposit' => ['Đặt cọc',        'bg-warning text-dark'],
                        'paid'    => ['Đã thanh toán',  'bg-success'],
                    ];
                    $payBadge = $payMap[$g['payment_status'] ?? 'unpaid'] ?? $payMap['unpaid'];

                    $dob = !empty($g['dob']) ? date('d/m/Y', strtotime($g['dob'])) : '—';
                    $ciAt = !empty($g['checked_in_at']) ? date('H:i d/m', strtotime($g['checked_in_at'])) : '';
                    ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td class="fw-semibold"><?= htmlentities($g['full_name']) ?></td>
                        <td class="<?= $genderClass ?>"><?= $genderText ?></td>
                        <td><?= $dob ?></td>
                        <td><?= !empty($g['phone']) ? htmlentities($g['phone']) : '<span class="text-muted">—</span>' ?></td>
                        <td><?= !empty($g['identity_no']) ? htmlentities($g['identity_no']) : '<span class="text-muted">—</span>' ?></td>
                        <td>
                            <span class="badge <?= $payBadge[1] ?>"><?= $payBadge[0] ?></span>
                        </td>
                        <td>
                            <?php if (!empty($g['check_in_status'])): ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Đã lên xe
                                </span>
                                <?php if ($ciAt): ?>
                                    <div class="small text-muted mt-1"><?= $ciAt ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-secondary">Chưa lên</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
