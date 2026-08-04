<?php

namespace App\Controllers;
  
use App\Controller;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\Tour;

class GuestGroupController extends Controller
{
    private $modelBooking;
    private $modelDeparture;
    private $modelTour;

    public function __construct()
    {
        $this->modelBooking = new Booking();
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

        return view(
            'guest-groups.show',
            compact('title', 'guestGroup', 'assignedBookings', 'availableBookings', 'stats')
        );
    }

    public function printList($id)
    {
        $guestGroup = $this->modelDeparture->findWithGuestStatsById($id);

        if (!$guestGroup) {
            setFlash('error', 'Đoàn khách không tồn tại.');
            return redirect('admin/guest-groups');
        }

        $bookings = $this->modelBooking->getByDepartureId($id);
        $stats = $this->modelBooking->getAssignedStatsByDepartureId($id);
        $title = 'In danh sách khách đoàn';

        return view(
            'guest-groups.print',
            compact('title', 'guestGroup', 'bookings', 'stats')
        );
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

    private function redirectToShow($departureId)
    {
        return redirect('admin/guest-groups/show/' . (int) $departureId);
    }
}
