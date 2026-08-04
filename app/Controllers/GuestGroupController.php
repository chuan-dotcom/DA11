<?php

namespace App\Controllers;
  
use App\Controller;
use App\Models\Booking;
use App\Models\BookingGuest;
use App\Models\Departure;
use App\Models\Tour;

class GuestGroupController extends Controller
{
    private $modelBooking;
    private $modelBookingGuest;
    private $modelDeparture;
    private $modelTour;

    public function __construct()
    {
        $this->modelBooking = new Booking();
        $this->modelBookingGuest = new BookingGuest();
        $this->modelDeparture = new Departure();
        $this->modelTour = new Tour();
    }

    public function index()
    {
        $title = 'Danh sách đoàn tour';
        $tourId = isset($_GET['tour_id']) ? (int) $_GET['tour_id'] : null;
        $status = isset($_GET['status']) ? trim((string) $_GET['status']) : null;

        if ($tourId <= 0) {
            $tourId = null;
        }

        if ($status === '') {
            $status = null;
        }

        $tours = $this->modelTour->getAll();
        $guestGroups = $this->modelDeparture->getAllWithGuestStats($tourId, $status);

        return view(
            'guest-groups.index',
            compact('title', 'guestGroups', 'tours', 'tourId', 'status')
        );
    }

    public function show($id)
    {
        $title = 'Chi tiết đoàn khách';
        $guestGroup = $this->modelDeparture->findWithGuestStatsById($id);

        if (!$guestGroup) {
            setFlash('error', 'Đoàn khách không tồn tại.');
            return redirect('admin/guest-groups');
        }

        $assignedBookings = $this->modelBooking->getByDepartureId($id);
        $availableBookings = $this->modelBooking->getAvailableForDeparture($guestGroup['tour_id']);
        $stats = $this->modelBooking->getAssignedStatsByDepartureId($id);

        $selectedBookingId = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
        $selectedBooking = null;
        if (!empty($assignedBookings)) {
            if ($selectedBookingId > 0) {
                foreach ($assignedBookings as $b) {
                    if ((int) $b['id'] === $selectedBookingId) {
                        $selectedBooking = $b;
                        break;
                    }
                }
            }
            if (!$selectedBooking) {
                $selectedBooking = $assignedBookings[0];
                $selectedBookingId = (int) $selectedBooking['id'];
            }
        }

        $bookingGuests = [];
        $bookingGuestStats = [
            'total_guests' => 0,
            'checked_in_guests' => 0,
        ];

        if ($selectedBookingId > 0 && $selectedBooking) {
            $this->modelBookingGuest->ensureGuestsForBooking($selectedBooking);
            $bookingGuests = $this->modelBookingGuest->getByBookingId($selectedBookingId);
            $bookingGuestStats = $this->modelBookingGuest->getStatsByBookingId($selectedBookingId);
        }

        return view(
            'guest-groups.show',
            compact('title', 'guestGroup', 'assignedBookings', 'availableBookings', 'stats', 'selectedBookingId', 'selectedBooking', 'bookingGuests', 'bookingGuestStats')
        );
    }

    public function printList($id)
    {
        $guestGroup = $this->modelDeparture->findWithGuestStatsById($id);

        if (!$guestGroup) {
            setFlash('error', 'Đoàn khách không tồn tại.');
            return redirect('admin/guest-groups');
        }

        $guests = $this->modelBookingGuest->getByDepartureId($id);
        $stats = $this->modelBooking->getAssignedStatsByDepartureId($id);
        $title = 'In danh sách khách đoàn';

        return view(
            'guest-groups.print',
            compact('title', 'guestGroup', 'guests', 'stats')
        );
    }

    public function createGuest($departureId, $bookingId)
    {
        $guestGroup = $this->modelDeparture->findWithGuestStatsById($departureId);
        if (!$guestGroup) {
            setFlash('error', 'Đoàn khách không tồn tại.');
            return redirect('admin/guest-groups');
        }

        $booking = $this->modelBooking->findById($bookingId);
        if (!$booking || (int) ($booking['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Booking không thuộc đoàn đã chọn.');
            return $this->redirectToShow($departureId);
        }

        $title = 'Thêm khách hàng';

        return view(
            'guest-groups.guest-create',
            compact('title', 'guestGroup', 'booking')
        );
    }

    public function storeGuest($departureId, $bookingId)
    {
        $booking = $this->modelBooking->findById($bookingId);
        if (!$booking || (int) ($booking['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Booking không thuộc đoàn đã chọn.');
            return $this->redirectToShow($departureId);
        }

        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        if ($fullName === '') {
            setFlash('error', 'Vui lòng nhập họ tên khách.');
            return redirect('admin/guest-groups/booking-guests/create/' . (int) $departureId . '/' . (int) $bookingId);
        }

        $this->modelBookingGuest->insert([
            'booking_id' => (int) $bookingId,
            'full_name' => $fullName,
            'gender' => $_POST['gender'] ?? null,
            'dob' => $_POST['dob'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'email' => $_POST['email'] ?? null,
            'identity_no' => $_POST['identity_no'] ?? null,
            'address' => $_POST['address'] ?? null,
            'payment_status' => $_POST['payment_status'] ?? 'unpaid',
            'note' => $_POST['note'] ?? null,
        ]);

        setFlash('success', 'Đã thêm khách hàng.');
        return redirect('admin/guest-groups/show/' . (int) $departureId . '?booking_id=' . (int) $bookingId);
    }

    public function editGuest($departureId, $guestId)
    {
        $guestGroup = $this->modelDeparture->findWithGuestStatsById($departureId);
        if (!$guestGroup) {
            setFlash('error', 'Đoàn khách không tồn tại.');
            return redirect('admin/guest-groups');
        }

        $guest = $this->modelBookingGuest->findById($guestId);
        if (!$guest || (int) ($guest['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Khách hàng không tồn tại trong đoàn.');
            return $this->redirectToShow($departureId);
        }

        $booking = $this->modelBooking->findById((int) ($guest['booking_id'] ?? 0));
        if (!$booking) {
            setFlash('error', 'Không tìm thấy booking của khách.');
            return $this->redirectToShow($departureId);
        }

        $title = 'Cập nhật khách hàng';

        return view(
            'guest-groups.guest-edit',
            compact('title', 'guestGroup', 'booking', 'guest')
        );
    }

    public function updateGuest($departureId, $guestId)
    {
        $guest = $this->modelBookingGuest->findById($guestId);
        if (!$guest || (int) ($guest['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Khách hàng không tồn tại trong đoàn.');
            return $this->redirectToShow($departureId);
        }

        $bookingId = (int) ($guest['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            setFlash('error', 'Không tìm thấy booking của khách.');
            return $this->redirectToShow($departureId);
        }

        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        if ($fullName === '') {
            setFlash('error', 'Vui lòng nhập họ tên khách.');
            return redirect('admin/guest-groups/booking-guests/edit/' . (int) $departureId . '/' . (int) $guestId);
        }

        $this->modelBookingGuest->update($guestId, [
            'full_name' => $fullName,
            'gender' => $_POST['gender'] ?? null,
            'dob' => $_POST['dob'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'email' => $_POST['email'] ?? null,
            'identity_no' => $_POST['identity_no'] ?? null,
            'address' => $_POST['address'] ?? null,
            'payment_status' => $_POST['payment_status'] ?? 'unpaid',
            'note' => $_POST['note'] ?? null,
        ]);

        setFlash('success', 'Đã cập nhật khách hàng.');
        return redirect('admin/guest-groups/show/' . (int) $departureId . '?booking_id=' . (int) $bookingId);
    }

    public function deleteGuest($departureId, $guestId)
    {
        $guest = $this->modelBookingGuest->findById($guestId);
        if (!$guest || (int) ($guest['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Khách hàng không tồn tại trong đoàn.');
            return $this->redirectToShow($departureId);
        }

        $bookingId = (int) ($guest['booking_id'] ?? 0);

        $this->modelBookingGuest->delete($guestId);
        setFlash('success', 'Đã xóa khách hàng.');
        return redirect('admin/guest-groups/show/' . (int) $departureId . '?booking_id=' . (int) $bookingId);
    }

    public function checkInGuest($departureId, $guestId)
    {
        $guest = $this->modelBookingGuest->findById($guestId);
        if (!$guest || (int) ($guest['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Khách hàng không tồn tại trong đoàn.');
            return $this->redirectToShow($departureId);
        }

        $bookingId = (int) ($guest['booking_id'] ?? 0);
        $this->modelBookingGuest->markCheckedIn($guestId);
        setFlash('success', 'Check-in thành công.');
        return redirect('admin/guest-groups/show/' . (int) $departureId . '?booking_id=' . (int) $bookingId);
    }

    public function cancelCheckInGuest($departureId, $guestId)
    {
        $guest = $this->modelBookingGuest->findById($guestId);
        if (!$guest || (int) ($guest['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Khách hàng không tồn tại trong đoàn.');
            return $this->redirectToShow($departureId);
        }

        $bookingId = (int) ($guest['booking_id'] ?? 0);
        $this->modelBookingGuest->cancelCheckedIn($guestId);
        setFlash('success', 'Đã hủy trạng thái check-in.');
        return redirect('admin/guest-groups/show/' . (int) $departureId . '?booking_id=' . (int) $bookingId);
    }

    public function assign($departureId, $bookingId)
    {
        $departure = $this->modelDeparture->findById($departureId);
        $booking = $this->modelBooking->findById($bookingId);

        if (!$departure || !$booking) {
            setFlash('error', 'Không tìm thấy booking hoặc đoàn khách.');
            return $this->redirectToShow($departureId);
        }

        if ((int) $booking['status'] !== 1) {
            setFlash('error', 'Chỉ có booking đã xác nhận mới được gắn vào đoàn.');
            return $this->redirectToShow($departureId);
        }

        if ((int) $booking['tour_id'] !== (int) $departure['tour_id']) {
            setFlash('error', 'Booking này không thuộc cùng tour với đoàn đã chọn.');
            return $this->redirectToShow($departureId);
        }

        if (!empty($booking['departure_id'])) {
            if ((int) $booking['departure_id'] === (int) $departureId) {
                setFlash('success', 'Booking đã nằm trong đoàn này.');
            } else {
                setFlash('error', 'Booking này đang thuộc một đoàn khác.');
            }

            return $this->redirectToShow($departureId);
        }

        if (($departure['status'] ?? 'scheduled') === 'cancelled') {
            setFlash('error', 'Không thể gắn khách vào đoàn đã hủy.');
            return $this->redirectToShow($departureId);
        }

        $currentPeople = $this->modelBooking->getAssignedPeopleCount($departureId);
        $bookingPeople = (int) ($booking['num_people'] ?? 0);
        $maxParticipants = (int) ($departure['max_participants'] ?? 0);

        if ($maxParticipants > 0 && ($currentPeople + $bookingPeople) > $maxParticipants) {
            setFlash('error', 'Không thể gắn khách vì vượt quá sức chứa của đoàn.');
            return $this->redirectToShow($departureId);
        }

        $this->modelBooking->assignToDeparture($bookingId, $departureId);

        setFlash('success', 'Đã thêm booking vào đoàn khách.');
        return $this->redirectToShow($departureId);
    }

    public function unassign($departureId, $bookingId)
    {
        $departure = $this->modelDeparture->findById($departureId);
        $booking = $this->modelBooking->findById($bookingId);

        if (!$departure || !$booking) {
            setFlash('error', 'Không tìm thấy booking hoặc đoàn khách.');
            return $this->redirectToShow($departureId);
        }

        if ((int) ($booking['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Booking này không thuộc đoàn đã chọn.');
            return $this->redirectToShow($departureId);
        }

        $this->modelBooking->removeFromDeparture($bookingId, $departureId);

        setFlash('success', 'Đã gỡ booking khỏi đoàn khách.');
        return $this->redirectToShow($departureId);
    }

    public function checkIn($departureId, $bookingId)
    {
        $departure = $this->modelDeparture->findById($departureId);
        $booking = $this->modelBooking->findById($bookingId);

        if (!$departure || !$booking) {
            setFlash('error', 'Không tìm thấy booking hoặc đoàn khách.');
            return $this->redirectToShow($departureId);
        }

        if ((int) ($booking['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Booking này chưa nằm trong đoàn đã chọn.');
            return $this->redirectToShow($departureId);
        }

        if ((int) $booking['status'] !== 1) {
            setFlash('error', 'Booking chưa xác nhận nên chưa thể check-in.');
            return $this->redirectToShow($departureId);
        }

        $this->modelBooking->markCheckedIn($bookingId, $departureId);

        setFlash('success', 'Check-in thành công.');
        return $this->redirectToShow($departureId);
    }

    public function cancelCheckIn($departureId, $bookingId)
    {
        $departure = $this->modelDeparture->findById($departureId);
        $booking = $this->modelBooking->findById($bookingId);

        if (!$departure || !$booking) {
            setFlash('error', 'Không tìm thấy booking hoặc đoàn khách.');
            return $this->redirectToShow($departureId);
        }

        if ((int) ($booking['departure_id'] ?? 0) !== (int) $departureId) {
            setFlash('error', 'Booking này không thuộc đoàn đã chọn.');
            return $this->redirectToShow($departureId);
        }

        $this->modelBooking->cancelCheckedIn($bookingId, $departureId);

        setFlash('success', 'Đã hủy trạng thái check-in.');
        return $this->redirectToShow($departureId);
    }

    public function seedCustomers($departureId)
    {
        $guestGroup = $this->modelDeparture->findWithGuestStatsById($departureId);
        if (!$guestGroup) {
            setFlash('error', 'Đoàn khách không tồn tại.');
            return redirect('admin/guest-groups');
        }

        $tour = $this->modelBooking->getTour($guestGroup['tour_id']);
        if (!$tour) {
            setFlash('error', 'Không tìm thấy tour để tạo khách hàng.');
            return $this->redirectToShow($departureId);
        }

        $names = [
            'Nguyễn Văn An',
            'Trần Thị Bích',
            'Lê Minh Châu',
            'Phạm Quốc Dũng',
            'Võ Thị Hạnh',
            'Đặng Gia Huy',
            'Bùi Thị Lan',
            'Hoàng Đức Long',
            'Phan Thị Mai',
            'Ngô Minh Nhật',
            'Đỗ Thị Oanh',
            'Dương Quốc Phong',
            'Lý Thị Quỳnh',
            'Hồ Văn Sang',
            'Tạ Minh Tâm',
            'Vũ Thị Uyên',
            'Đinh Văn Vinh',
        ];

        $created = 0;
        $bookingDate = date('Y-m-d');
        $unitPrice = (float) ($tour['price'] ?? 0);

        foreach ($names as $index => $name) {
            $seq = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
            $email = 'demo-guest-' . (int) $departureId . '-' . $seq . '@example.com';
            $phone = '09' . str_pad((string) ($departureId % 10000), 4, '0', STR_PAD_LEFT) . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            try {
                $this->modelBooking->insert([
                    'tour_id' => (int) $guestGroup['tour_id'],
                    'customer_name' => $name,
                    'customer_email' => $email,
                    'customer_phone' => $phone,
                    'num_people' => 1,
                    'total_price' => $unitPrice,
                    'booking_date' => $bookingDate,
                    'status' => 1,
                    'note' => 'Dữ liệu mẫu (17 khách)',
                ]);

                $bookingId = $this->modelBooking->getLastInsertId();
                if ($bookingId > 0) {
                    $this->modelBooking->assignToDeparture($bookingId, $departureId);
                }

                $created++;
            } catch (\Throwable $e) {
            }
        }

        setFlash('success', 'Đã tạo ' . $created . ' khách hàng mẫu và gắn vào đoàn.');
        return $this->redirectToShow($departureId);
    }

    private function redirectToShow($departureId)
    {
        return redirect('admin/guest-groups/show/' . (int) $departureId);
    }
}
