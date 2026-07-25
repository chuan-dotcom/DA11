@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="250">Mã Booking</th>
                    <td>{{ $booking['id'] }}</td>
                </tr>
                <tr>
                    <th>Tour</th>
                    <td>{{ $booking['tour_name'] }}</td>
                </tr>
                <tr>
                    <th>Khách hàng</th>
                    <td>{{ $booking['customer_name'] }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $booking['customer_email'] }}</td>
                </tr>
                <tr>
                    <th>Số điện thoại</th>
                    <td>{{ $booking['customer_phone'] }}</td>
                </tr>
                <tr>
                    <th>Số người</th>
                    <td>{{ $booking['num_people'] }}</td>
                </tr>
                <tr>
                    <th>Ngày khởi hành</th>
                    <td>{{ $booking['booking_date'] }}</td>
                </tr>
                <tr>
                    <th>Tổng tiền</th>
                    <td><strong class="text-danger">{{ number_format($booking['total_price']) }} VNĐ</strong></td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td>
                        @php
                            switch ($booking['status']) {
                                case 0:
                                    echo '<span class="badge bg-warning text-dark">Chờ xác nhận</span>';
                                    break;
                                case 1:
                                    echo '<span class="badge bg-success">Đã xác nhận</span>';
                                    break;
                                case 2:
                                    echo '<span class="badge bg-danger">Đã hủy</span>';
                                    break;
                            }
                        @endphp
                    </td>
                </tr>
                <tr>
                    <th>Ghi chú</th>
                    <td>{!! !empty($booking['note']) ? nl2br(e($booking['note'])) : 'Không có ghi chú' !!}</td>
                </tr>
                <tr>
                    <th>Ngày tạo</th>
                    <td>{{ $booking['created_at'] }}</td>
                </tr>
            </table>

            <a href="{{ route('admin/bookings/edit/' . $booking['id']) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('admin/bookings') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</div>
@endsection
